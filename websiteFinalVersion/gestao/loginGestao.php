<?php
session_start();
require 'conexao.php';

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM usuariosgestao WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['cargo'] = $usuario['cargo'];
        header("Location: dashboard.php");
        exit;
    } else {
        $erro = "Email ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Login Gestão - Stingray</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container mt-5">
    <h2>Login - Painel de Gestão</h2>
    <?php if($erro): ?>
        <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
</div>
</body>
</html>
