<?php
session_start();
require 'gestao/conexao.php'; // arquivo com $conn (mysqli)

if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$erro = '';
$sucesso = '';

$conn->begin_transaction();

try {
    // Buscar o carrinho aberto do usuário
    $sqlCarrinho = "SELECT * FROM carrinhos WHERE usuario_id = ? AND status = 'aberto' LIMIT 1";
    $stmt = $conn->prepare($sqlCarrinho);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $carrinho = $stmt->get_result()->fetch_assoc();

    if (!$carrinho) {
        throw new Exception("Você não possui carrinhos abertos.");
    }

    $carrinho_id = $carrinho['id'];

    // Buscar itens do carrinho
    $sqlItens = "SELECT ci.id, ci.produto_id, ci.quantidade, p.nome_produto, p.preco, p.quantidade_estoque
                 FROM carrinho_itens ci
                 JOIN produtos p ON ci.produto_id = p.id_produto
                 WHERE ci.carrinho_id = ?";
    $stmtItens = $conn->prepare($sqlItens);
    $stmtItens->bind_param("i", $carrinho_id);
    $stmtItens->execute();
    $resultado = $stmtItens->get_result();
    $itens = $resultado->fetch_all(MYSQLI_ASSOC);

    if (empty($itens)) {
        throw new Exception("Seu carrinho está vazio.");
    }

    // Inserir pedido na tabela pedidos (crie se não existir)
    $sqlPedido = "INSERT INTO pedidos (usuario_id, total, status, created_at) VALUES (?, ?, 'finalizado', NOW())";
    $total = 0;
    foreach ($itens as $item) {
        $total += $item['preco'] * $item['quantidade'];
    }
    $stmtPedido = $conn->prepare($sqlPedido);
    $stmtPedido->bind_param("id", $usuario_id, $total);
    $stmtPedido->execute();
    $pedido_id = $stmtPedido->insert_id;

    // Inserir itens do pedido e atualizar estoque
    $sqlInserirItem = "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco) VALUES (?, ?, ?, ?)";
    $sqlAtualizaEstoque = "UPDATE produtos SET quantidade_estoque = quantidade_estoque - ? WHERE id_produto = ?";

    $stmtItem = $conn->prepare($sqlInserirItem);
    $stmtEstoque = $conn->prepare($sqlAtualizaEstoque);

    foreach ($itens as $item) {
        if ($item['quantidade'] > $item['quantidade_estoque']) {
            throw new Exception("Estoque insuficiente para o produto: {$item['nome_produto']}");
        }

        $stmtItem->bind_param("iiid", $pedido_id, $item['produto_id'], $item['quantidade'], $item['preco']);
        $stmtItem->execute();

        $stmtEstoque->bind_param("ii", $item['quantidade'], $item['produto_id']);
        $stmtEstoque->execute();
    }

    // Marcar carrinho como finalizado
    $stmtStatus = $conn->prepare("UPDATE carrinhos SET status = 'finalizado' WHERE id = ?");
    $stmtStatus->bind_param("i", $carrinho_id);
    $stmtStatus->execute();

    $conn->commit();
    $sucesso = "Pedido finalizado com sucesso! Total: R$ " . number_format($total, 2, ",", ".");
} catch (Exception $e) {
    $conn->rollback();
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
