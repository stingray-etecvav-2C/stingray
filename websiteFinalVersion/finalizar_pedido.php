<?php
session_start();
require 'gestao/conexao.php'; // arquivo com $pdo (PDO)

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$erro = '';
$sucesso = '';

try {
    $pdo->beginTransaction();

    // Buscar o carrinho aberto do usuário
    $sqlCarrinho = "SELECT * FROM carrinhos WHERE usuario_id = :usuario_id AND status = 'aberto' LIMIT 1";
    $stmt = $pdo->prepare($sqlCarrinho);
    $stmt->execute([':usuario_id' => $usuario_id]);
    $carrinho = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$carrinho) {
        throw new Exception("Você não possui carrinhos abertos.");
    }

    $carrinho_id = $carrinho['id'];

    // Buscar itens do carrinho
    $sqlItens = "SELECT ci.id, ci.produto_id, ci.quantidade, p.nome_produto, p.preco, p.quantidade_estoque
                 FROM carrinho_itens ci
                 JOIN produtos p ON ci.produto_id = p.id_produto
                 WHERE ci.carrinho_id = :carrinho_id";
    $stmtItens = $pdo->prepare($sqlItens);
    $stmtItens->execute([':carrinho_id' => $carrinho_id]);
    $itens = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

    if (empty($itens)) {
        throw new Exception("Seu carrinho está vazio.");
    }

    // Inserir pedido
    $sqlPedido = "INSERT INTO pedidos (usuario_id, total, status, created_at) VALUES (:usuario_id, :total, 'finalizado', NOW())";
    $total = 0;
    foreach ($itens as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }

    $stmtPedido = $pdo->prepare($sqlPedido);
    $stmtPedido->execute([
        ':usuario_id' => $usuario_id,
        ':total' => $total
    ]);
    $pedido_id = $pdo->lastInsertId();

    // Inserir itens do pedido e atualizar estoque
    $sqlInserirItem = "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco) 
                       VALUES (:pedido_id, :produto_id, :quantidade, :preco)";
    $sqlAtualizaEstoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque - :quantidade 
                           WHERE id_produto = :produto_id";

    $stmtItem = $pdo->prepare($sqlInserirItem);
    $stmtEstoque = $pdo->prepare($sqlAtualizaEstoque);

    foreach ($itens as $item) {
        if ($item['quantidade'] > $item['quantidade_estoque']) {
            throw new Exception("Estoque insuficiente para o produto: {$item['nome_produto']}");
        }

        $stmtItem->execute([
            ':pedido_id' => $pedido_id,
            ':produto_id' => $item['produto_id'],
            ':quantidade' => $item['quantidade'],
            ':preco' => $item['preco']
        ]);

        $stmtEstoque->execute([
            ':quantidade' => $item['quantidade'],
            ':produto_id' => $item['produto_id']
        ]);
    }

    // Marcar carrinho como finalizado
    $stmtStatus = $pdo->prepare("UPDATE carrinhos SET status = 'finalizado' WHERE id = :carrinho_id");
    $stmtStatus->execute([':carrinho_id' => $carrinho_id]);

    $pdo->commit();
    $sucesso = "Pedido finalizado com sucesso! Total: R$ " . number_format($total, 2, ",", ".");
} catch (Exception $e) {
    $pdo->rollBack();
    $erro = "Erro ao finalizar pedido: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Finalizar Pedido - Stingray</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="container mt-5">
    <h3>Finalizar Pedido</h3>
    <?php if($erro): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php elseif($sucesso): ?>
        <div class="alert alert-success"><?= $sucesso ?></div>
    <?php endif; ?>
    <a href="ver_carrinho.php" class="btn btn-secondary">← Voltar ao Carrinho</a>
</div>
</body>
</html>
