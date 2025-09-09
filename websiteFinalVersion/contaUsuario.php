<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Conexão com o banco
$host = 'localhost';
$dbname = 'stingray';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e){
    die("Erro de conexão: " . $e->getMessage());
}

$usuario_id = $_SESSION['usuario_id'];

// Buscar informações do usuário
$stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios WHERE id = ?");
$stmt->execute([$usuario_id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar pedidos do usuário
$stmt2 = $pdo->prepare("
    SELECT c.id as pedido_id, c.status, c.created_at, SUM(ci.quantidade * p.preco) AS total
    FROM carrinhos c
    JOIN carrinho_itens ci ON ci.carrinho_id = c.id
    JOIN produtos p ON ci.produto_id = p.id_produto
    WHERE c.usuario_id = ?
    GROUP BY c.id
    ORDER BY c.created_at DESC
");
$stmt2->execute([$usuario_id]);
$pedidos = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$mensagem = "";

// Processar atualização de dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_info'])) {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);

        if (!empty($nome) && !empty($email)) {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $usuario_id]);
            $_SESSION['usuario_nome'] = $nome;
            $mensagem = "Informações atualizadas com sucesso!";
            $usuario['nome'] = $nome;
            $usuario['email'] = $email;
        }
    }

    if (isset($_POST['change_password'])) {
        $senha_atual = $_POST['senha_atual'];
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];

        // Buscar senha atual
        $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$usuario_id]);
        $hash = $stmt->fetchColumn();

        if (password_verify($senha_atual, $hash)) {
            if ($nova_senha === $confirmar_senha) {
                if (preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W]).{8,}$/', $nova_senha)) {
                    $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
                    $stmt->execute([$nova_senha_hash, $usuario_id]);
                    $mensagem = "Senha alterada com sucesso!";
                } else {
                    $mensagem = "A nova senha não atende aos critérios de segurança.";
                }
            } else {
                $mensagem = "Nova senha e confirmação não coincidem.";
            }
        } else {
            $mensagem = "Senha atual incorreta.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Minha Conta - Stingray</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
</head>
<body>

 <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
      <img src="midias/logoStingray.png" alt="Logo Stingray" id="nav-logo">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>

        <?php if (isset($_SESSION['usuario_id'])): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="#">Minha Conta</a></li>
            <li><a class="dropdown-item" href="meus_pedidos.php">Meus Pedidos</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout.php">Sair</a></li>
          </ul>
        </li>
        <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="registro.php">Criar Conta</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-5">
    <h2>Minha Conta</h2>
    <?php if($mensagem): ?>
        <div class="alert alert-success"><?= $mensagem ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <ul class="list-group">
                <li class="list-group-item"><a href="#info">Informações Pessoais</a></li>
                <li class="list-group-item"><a href="#senha">Alterar Senha</a></li>
                <li class="list-group-item"><a href="#pedidos">Meus Pedidos</a></li>
            </ul>
        </div>

        <!-- Conteúdo -->
        <div class="col-md-9">
            <!-- Informações Pessoais -->
            <section id="info" class="mb-5">
                <h4>Informações Pessoais</h4>
                <form method="POST">
                    <input type="hidden" name="update_info">
                    <div class="mb-3">
                        <label>Nome</label>
                        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Atualizar Informações</button>
                </form>
            </section>

            <!-- Alterar Senha -->
            <section id="senha" class="mb-5">
                <h4>Alterar Senha</h4>
                <form method="POST">
                    <input type="hidden" name="change_password">
                    <div class="mb-3">
                        <label>Senha Atual</label>
                        <input type="password" id="senha_atual" name="senha_atual" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nova Senha</label>
                        <input type="password" id="nova_senha" name="nova_senha" class="form-control" required>
                        <small class="text-muted">Deve ter mínimo 8 caracteres, 1 maiúscula, 1 minúscula, 1 número e 1 caractere especial.</small>
                    </div>
                    <div class="mb-3">
                        <label>Confirmar Nova Senha</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required>
                    </div>

                    <!-- Checkbox para mostrar senha -->
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="ver_senha">
                        <label class="form-check-label" for="ver_senha">Mostrar senhas</label>
                    </div>

                    <button type="submit" class="btn btn-warning">Alterar Senha</button>
                </form>
            </section>

            <script>
                // Script para alternar visibilidade da senha
                document.getElementById('ver_senha').addEventListener('change', function() {
                    const tipo = this.checked ? 'text' : 'password';
                    document.getElementById('senha_atual').type = tipo;
                    document.getElementById('nova_senha').type = tipo;
                    document.getElementById('confirmar_senha').type = tipo;
                });
            </script>

            <!-- Meus Pedidos -->
            <section id="pedidos">
                <h4>Meus Pedidos</h4>
                <?php if(count($pedidos) > 0): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID Pedido</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($pedidos as $p): ?>
                            <tr>
                                <td>#<?= $p['pedido_id'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
                                <td><?= ucfirst($p['status']) ?></td>
                                <td>R$ <?= number_format($p['total'],2,',','.') ?></td>
                                <td><a href="detalhes_pedido.php?id=<?= $p['pedido_id'] ?>" class="btn btn-info btn-sm">Detalhes</a></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="alert alert-info">Você ainda não realizou nenhum pedido.</div>
                <?php endif; ?>
            </section>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
