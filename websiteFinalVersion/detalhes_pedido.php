<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    die("Pedido não especificado.");
}

$pedido_id = intval($_GET['id']);

$host = 'localhost';
$dbname = 'stingray';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Verificar se o pedido pertence ao usuário
    $stmt = $pdo->prepare("SELECT * FROM carrinhos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$pedido_id, $_SESSION['usuario_id']]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        die("Pedido não encontrado ou você não tem permissão para visualizar.");
    }

    // Buscar itens do pedido
    $stmt = $pdo->prepare("
        SELECT p.nome_produto, p.preco, ci.quantidade, (p.preco * ci.quantidade) as subtotal
        FROM carrinho_itens ci
        JOIN produtos p ON ci.produto_id = p.id_produto
        WHERE ci.carrinho_id = ?
    ");
    $stmt->execute([$pedido_id]);
    $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcular total
    $total = 0;
    foreach ($itens as $item) {
        $total += $item['subtotal'];
    }

} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Detalhes do Pedido #<?= $pedido_id ?> - Stingray</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
            <img src="midias/logoStingray.png" alt="Logo Stingray" id="nav-logo">
    </a>
    <ul class="navbar-nav ms-auto">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="ver_carrinho.php">Carrinho</a></li>
      <li class="nav-item"><a class="nav-link" href="logout.php">Sair</a></li>
    </ul>
  </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4">Detalhes do Pedido #<?= $pedido_id ?></h2>
    <p><strong>Status:</strong> 
        <?php if($pedido['status'] == 'aberto'): ?>
            <span class="badge bg-warning text-dark">Aberto</span>
        <?php else: ?>
            <span class="badge bg-success">Finalizado</span>
        <?php endif; ?>
    </p>
    <p><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></p>

    <?php if(count($itens) > 0): ?>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Produto</th>
                <th>Preço Unitário</th>
                <th>Quantidade</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($itens as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['nome_produto']) ?></td>
                <td>R$ <?= number_format($item['preco'], 2, ',', '.') ?></td>
                <td><?= $item['quantidade'] ?></td>
                <td>R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Total</strong></td>
                <td><strong>R$ <?= number_format($total, 2, ',', '.') ?></strong></td>
            </tr>
        </tbody>
    </table>
    <?php else: ?>
        <div class="alert alert-info">Este pedido não possui itens.</div>
    <?php endif; ?>

    <a href="meus_pedidos.php" class="btn btn-secondary mt-3">← Voltar aos Pedidos</a>
</div>

</body>
</html>
