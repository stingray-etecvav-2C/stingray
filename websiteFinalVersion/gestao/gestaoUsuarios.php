<?php
require 'auth.php';
require 'conexao.php';
checkAccess('admin');

// Deletar usuário
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $stmt = $pdo->prepare("DELETE FROM usuariosgestao WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: gestaoUsuarios.php");
    exit;
}

// Buscar usuários da gestão
$stmt = $pdo->prepare("SELECT id, nome, email, cargo FROM usuariosgestao ORDER BY id ASC");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Gestão de Usuários - Stingray</title>
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
                <li class="nav-item"><a href="produtos.php" class="nav-link">Gerenciar Produtos</a></li>
                <li class="nav-item"><a href="gestaoUsuarios.php" class="nav-link active">Usuários Gestão</a></li>
                <li class="nav-item"><a href="gestaoUsuariosSite.php" class="nav-link">Usuários do Site</a></li>
                <li class="nav-item"><a href="relatorios.php" class="nav-link">Relatórios</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger">Sair</a></li>
            </ul>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="mb-4">Usuários da Gestão</h1>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Cargo</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($usuarios)): ?>
                            <?php foreach($usuarios as $usuario): ?>
                            <tr>
                                <td><?= $usuario['id'] ?></td>
                                <td><?= htmlspecialchars($usuario['nome']) ?></td>
                                <td><?= htmlspecialchars($usuario['email']) ?></td>
                                <td><?= htmlspecialchars($usuario['cargo']) ?></td>
                                <td class="d-flex gap-2">
                                    <a href="editarUsuario.php?id=<?= $usuario['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                    <a href="gestaoUsuarios.php?delete=<?= $usuario['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deseja realmente deletar este usuário?')">Deletar</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhum usuário encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
