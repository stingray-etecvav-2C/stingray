<?php
require 'auth.php';
require 'conexao.php';
checkAccess('admin,repositor');

// Inserir produto
if(isset($_POST['acao']) && $_POST['acao'] == 'inserir'){
    $nome = $_POST['nome_produto'];
    $preco = $_POST['preco'];
    $estado = $_POST['estado'];
    $quantidade = $_POST['quantidade_estoque'];
    $categoria = $_POST['categoria'];
    $marca = $_POST['marca'];

    $sql = "INSERT INTO produtos (nome_produto, preco, estado, quantidade_estoque, categoria, marca) 
            VALUES (:nome, :preco, :estado, :quantidade, :categoria, :marca)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nome' => $nome,
        ':preco' => $preco,
        ':estado' => $estado,
        ':quantidade' => $quantidade,
        ':categoria' => $categoria,
        ':marca' => $marca
    ]);
}

// Editar produto
if(isset($_POST['acao']) && $_POST['acao'] == 'editar'){
    $id = $_POST['id_produto'];
    $preco = $_POST['preco'];
    $estado = $_POST['estado'];
    $quantidade = $_POST['quantidade_estoque'];

    $sql = "UPDATE produtos SET preco=:preco, estado=:estado, quantidade_estoque=:quantidade WHERE id_produto=:id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':preco' => $preco,
        ':estado' => $estado,
        ':quantidade' => $quantidade,
        ':id' => $id
    ]);
}

// Deletar produto
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM produtos WHERE id_produto=:id");
    $stmt->execute([':id' => $id]);
}

// Lista de produtos
$result = $pdo->query("SELECT * FROM produtos ORDER BY id_produto DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gestão de Produtos - Stingray</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/styleGestao.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 sidebar d-flex flex-column">
            <img src="../midias/logoStingray.png" alt="Stingray" class="top-logo">
            <h4>Gestão</h4>
            <p>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']); ?></p>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li class="nav-item"><a href="produtos.php" class="nav-link active">Gerenciar Produtos</a></li>
                <li class="nav-item"><a href="gestaoUsuarios.php" class="nav-link">Usuários Gestão</a></li>
                <li class="nav-item"><a href="gestaoUsuariosSite.php" class="nav-link">Usuários do Site</a></li>
                <li class="nav-item"><a href="relatorios.php" class="nav-link">Relatórios</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger">Sair</a></li>
            </ul>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="mb-4">Gestão de Produtos</h1>

            <!-- Form Inserir/Editar -->
            <?php if(isset($_GET['edit'])): 
                $id = $_GET['edit'];
                $produto = $pdo->prepare("SELECT * FROM produtos WHERE id_produto=:id");
                $produto->execute([':id'=>$id]);
                $produto = $produto->fetch(PDO::FETCH_ASSOC);
            ?>
            <form method="POST" class="mb-4">
                <input type="hidden" name="acao" value="editar">
                <input type="hidden" name="id_produto" value="<?= $produto['id_produto'] ?>">

                <input type="text" value="<?= htmlspecialchars($produto['nome_produto']) ?>" class="form-control mb-2" readonly>
                <input type="text" value="<?= htmlspecialchars($produto['categoria']) ?>" class="form-control mb-2" readonly>
                <input type="text" value="<?= htmlspecialchars($produto['marca']) ?>" class="form-control mb-2" readonly>

                <input type="number" step="0.01" name="preco" value="<?= $produto['preco'] ?>" class="form-control mb-2" required>
                <input type="text" name="estado" value="<?= $produto['estado'] ?>" class="form-control mb-2" required>
                <input type="number" name="quantidade_estoque" value="<?= $produto['quantidade_estoque'] ?>" class="form-control mb-2" required>

                <button type="submit" class="btn-salvar">Salvar Alterações</button>
                <a href="produtos.php" class="btn-cancelar">Cancelar</a>
            </form>
            <?php else: ?>
            <form method="POST" class="mb-4">
                <input type="hidden" name="acao" value="inserir">
                <input type="text" name="nome_produto" class="form-control mb-2" placeholder="Nome do Produto" required>
                <input type="number" step="0.01" name="preco" class="form-control mb-2" placeholder="Preço" required>
                <input type="text" name="estado" class="form-control mb-2" placeholder="Estado" required>
                <input type="number" name="quantidade_estoque" class="form-control mb-2" placeholder="Quantidade em Estoque" required>
                <input type="text" name="categoria" class="form-control mb-2" placeholder="Categoria" required>
                <input type="text" name="marca" class="form-control mb-2" placeholder="Marca" required>
                <button type="submit" class="btn-salvar">Adicionar Produto</button>
            </form>
            <?php endif; ?>

            <!-- Lista de produtos -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produto</th>
                            <th>Marca</th>
                            <th>Categoria</th>
                            <th>Preço</th>
                            <th>Estado</th>
                            <th>Estoque</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($result as $produto): ?>
                        <tr>
                            <td><?= $produto['id_produto'] ?></td>
                            <td><?= htmlspecialchars($produto['nome_produto']) ?></td>
                            <td><?= htmlspecialchars($produto['marca']) ?></td>
                            <td><?= htmlspecialchars($produto['categoria']) ?></td>
                            <td>R$ <?= number_format($produto['preco'],2,",",".") ?></td>
                            <td class="status-<?= $produto['estado'] ?>"><?= htmlspecialchars($produto['estado']) ?></td>
                            <td><?= $produto['quantidade_estoque'] ?></td>
                            <td class="d-flex gap-2">
                                <a href="produtos.php?edit=<?= $produto['id_produto'] ?>" class="btn-opcao btn-warning btn-sm">Editar</a>
                                <a href="produtos.php?delete=<?= $produto['id_produto'] ?>" class="btn-opcao btn-danger btn-sm" onclick="return confirm('Deseja realmente deletar este produto?')">Deletar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
