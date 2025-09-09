<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'stingray';
$username = 'root';
$password = '';

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Obter ou criar carrinho para o usuário
$usuario_id = $_SESSION['usuario_id'];
$stmt = $pdo->prepare("SELECT id FROM carrinhos WHERE usuario_id = ? AND status = 'aberto'");
$stmt->execute([$usuario_id]);
$carrinho = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$carrinho) {
    // Criar novo carrinho
    $stmt = $pdo->prepare("INSERT INTO carrinhos (usuario_id, status) VALUES (?, 'aberto')");
    $stmt->execute([$usuario_id]);
    $carrinho_id = $pdo->lastInsertId();
} else {
    $carrinho_id = $carrinho['id'];
}

// Adicionar produto ao carrinho
if (isset($_GET['adicionar'])) {
    $produto_id = (int)$_GET['adicionar'];
    
    // Verificar se o produto existe e está disponível
    $stmt = $pdo->prepare("SELECT * FROM produtos WHERE id_produto = ? AND estado = 'disponivel' AND quantidade_estoque > 0");
    $stmt->execute([$produto_id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($produto) {
        // Verificar se o produto já está no carrinho
        $stmt = $pdo->prepare("SELECT * FROM carrinho_itens WHERE carrinho_id = ? AND produto_id = ?");
        $stmt->execute([$carrinho_id, $produto_id]);
        $item_existente = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($item_existente) {
            // Atualizar quantidade
            $nova_quantidade = $item_existente['quantidade'] + 1;
            $stmt = $pdo->prepare("UPDATE carrinho_itens SET quantidade = ? WHERE id = ?");
            $stmt->execute([$nova_quantidade, $item_existente['id']]);
        } else {
            // Adicionar novo item
            $stmt = $pdo->prepare("INSERT INTO carrinho_itens (carrinho_id, produto_id, quantidade) VALUES (?, ?, 1)");
            $stmt->execute([$carrinho_id, $produto_id]);
        }
        
        header('Location: ver_carrinho.php?adicionado=1');
        exit;
    }
}

// Remover produto do carrinho
if (isset($_GET['remover'])) {
    $produto_id = (int)$_GET['remover'];
    
    $stmt = $pdo->prepare("DELETE FROM carrinho_itens WHERE carrinho_id = ? AND produto_id = ?");
    $stmt->execute([$carrinho_id, $produto_id]);
    
    header('Location: ver_carrinho.php?removido=1');
    exit;
}

// Atualizar quantidades
if (isset($_POST['atualizar'])) {
    foreach ($_POST['quantidade'] as $produto_id => $quantidade) {
        $produto_id = (int)$produto_id;
        $quantidade = (int)$quantidade;
        
        if ($quantidade <= 0) {
            // Remover item se quantidade for 0 ou menos
            $stmt = $pdo->prepare("DELETE FROM carrinho_itens WHERE carrinho_id = ? AND produto_id = ?");
            $stmt->execute([$carrinho_id, $produto_id]);
        } else {
            // Atualizar quantidade
            $stmt = $pdo->prepare("UPDATE carrinho_itens SET quantidade = ? WHERE carrinho_id = ? AND produto_id = ?");
            $stmt->execute([$quantidade, $carrinho_id, $produto_id]);
        }
    }
    
    header('Location: ver_carrinho.php?atualizado=1');
    exit;
}

// Buscar itens do carrinho
$stmt = $pdo->prepare("
    SELECT p.id_produto, p.nome_produto, p.preco, ci.quantidade 
    FROM carrinho_itens ci 
    JOIN produtos p ON ci.produto_id = p.id_produto 
    WHERE ci.carrinho_id = ?
");
$stmt->execute([$carrinho_id]);
$itens_carrinho = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular total
$total = 0;
foreach ($itens_carrinho as $item) {
    $total += $item['preco'] * $item['quantidade'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stingray Tech - Carrinho de Compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="midias/logoStingray.png" alt="Logo Stingray" id="nav-logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="index.php#products-section" role="button">Produtos</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="index.php#contato">Contato</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-outline-light" href="index.php#crdt">Créditos</a>
                    </li>
                    <li>
                        <a class="btn btn-outline-light" href="ver_carrinho.php">🛒 Carrinho (<?= count($itens_carrinho) ?>)</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['usuario_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-light" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
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
                        <li class="nav-item">
                            <a class="btn btn-outline-light me-2" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary" href="registro.php">Criar Conta</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Carrinho de Compras</h1>
        
        <?php if (isset($_GET['adicionado'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Produto adicionado ao carrinho com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['removido'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                Produto removido do carrinho!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['atualizado'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                Carrinho atualizado com sucesso!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (empty($itens_carrinho)): ?>
            <div class="alert alert-info text-center">
                Seu carrinho está vazio. <a href="index.php" class="alert-link">Voltar às compras</a>
            </div>
        <?php else: ?>
            <form method="POST" action="ver_carrinho.php">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Preço</th>
                            <th>Quantidade</th>
                            <th>Subtotal</th>
                            <th>Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($itens_carrinho as $item): ?>
                        <tr>
                            <td>
                                <img src="midias/produtos/<?= $item['id_produto'] ?>.jpg" alt="<?= htmlspecialchars($item['nome_produto']) ?>" width="50" class="me-3">
                                <?= htmlspecialchars($item['nome_produto']) ?>
                            </td>
                            <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                            <td>
                                <input type="number" name="quantidade[<?= $item['id_produto'] ?>]" value="<?= $item['quantidade'] ?>" min="1" class="form-control" style="width: 80px;">
                            </td>
                            <td>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></td>
                            <td>
                                <a href="ver_carrinho.php?remover=<?= $item['id_produto'] ?>" class="btn btn-danger btn-sm">Remover</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>R$ <?= number_format($total, 2, ',', '.') ?></strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                
                <div class="d-flex justify-content-between">
                    <a href="index.php" class="btn btn-secondary">Continuar Comprando</a>
                    <div>
                        <button type="submit" name="atualizar" class="btn btn-info">Atualizar Carrinho</button>
                        <a href="finalizar_pedido.php" class="btn btn-success">Finalizar Compra</a>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Rodapé -->
    <footer class="mt-5" id="crdt">
        <div class="text-center p-3">
            Projeto protegido pela <strong>GPL-3.0 license</strong> <br>
            Desenvolvido por <a href="mailto:guilhermefilho095@gmail.com" style="color: white;">guilhermefilho095@gmail.com</a>
        </div>
    </footer>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
