<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$host = 'localhost';
$dbname = 'stingray';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Buscar pedidos do usuário
    $stmt = $pdo->prepare("
        SELECT c.id as carrinho_id, c.status, c.created_at,
               SUM(p.preco * ci.quantidade) as total
        FROM carrinhos c
        JOIN carrinho_itens ci ON ci.carrinho_id = c.id
        JOIN produtos p ON p.id_produto = ci.produto_id
        WHERE c.usuario_id = ?
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");
    $stmt->execute([$_SESSION['usuario_id']]);
    $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Meus Pedidos - Stingray</title>
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
    <h2 class="mb-4">Meus Pedidos</h2>

    <?php if(count($pedidos) > 0): ?>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID do Pedido</th>
                <th>Data</th>
                <th>Status</th>
                <th>Total</th>
                <th>Detalhes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($pedidos as $pedido): ?>
            <tr>
                <td>#<?= $pedido['carrinho_id'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></td>
                <td>
                    <?php if($pedido['status'] == 'aberto'): ?>
                        <span class="badge bg-warning text-dark">Aberto</span>
                    <?php else: ?>
                        <span class="badge bg-success">Finalizado</span>
                    <?php endif; ?>
                </td>
                <td>R$ <?= number_format($pedido['total'], 2, ',', '.') ?></td>
                <td>
                    <a href="detalhes_pedido.php?id=<?= $pedido['carrinho_id'] ?>" class="btn btn-info btn-sm">Ver Itens</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="alert alert-info">Você ainda não realizou nenhum pedido.</div>
    <?php endif; ?>
</div>

</body>
</html>
