<?php
require 'auth.php';
require 'conexao.php';
checkAccess('admin,funcionario');


$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: gestaoUsuariosSite.php");
    exit;
}

// Buscar dados do usuário
$stmt = $pdo->prepare("SELECT id, nome, email FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Usuário não encontrado.");
}

// Atualizar usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=? WHERE id=?");
    $stmt->execute([$nome, $email, $id]);

    header("Location: gestaoUsuariosSite.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Editar Usuário (Site) - Stingray Gestão</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/styleGestao.css">
</head>
<body>
<div class="container py-5">
    <h2 class="text-center mb-4">Editar Usuário (Site de Compras)</h2>
    <form method="post" class="card p-4 bg-dark text-light shadow-lg rounded">
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">E-mail</label>
            <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" class="form-control" required>
        </div>
    <div class="mt-3">
    <button type="submit" class="btn-salvar w-100">Salvar Alterações</button>
    <a href="gestaoUsuariosSite.php" class="btn-cancelar w-100 mt-2">Cancelar</a>
    </div>
    </form>
</div>
</body>
</html>
