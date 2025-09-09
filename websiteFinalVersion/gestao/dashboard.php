<?php
require 'auth.php';
require 'conexao.php';
checkAccess('admin,repositor,funcionario');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard - Stingray</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/styleGestao.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 sidebar d-flex flex-column">
            <img src="../midias/logoStingray.png" alt="Stingray" class="top-logo">
            <h4>Dashboard</h4>
            <p>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']); ?></p>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="dashboard.php" class="nav-link active">Dashboard</a></li>
                <li class="nav-item"><a href="produtos.php" class="nav-link">Gerenciar Produtos</a></li>
                <li class="nav-item"><a href="gestaoUsuarios.php" class="nav-link">Usuários Gestão</a></li>
                <li class="nav-item"><a href="gestaoUsuariosSite.php" class="nav-link">Usuários do Site</a></li>
                <li class="nav-item"><a href="relatorios.php" class="nav-link">Relatórios</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger">Sair</a></li>
            </ul>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="mb-4">Painel de Controle</h1>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card text-white bg-primary shadow">
                        <div class="card-body">
                            <h5 class="card-title">Usuários Gestão</h5>
                            <p class="card-text">Gerencie usuários da equipe.</p>
                            <a href="gestaoUsuarios.php" class="btn btn-light">Acessar</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-success shadow">
                        <div class="card-body">
                            <h5 class="card-title">Usuários do Site</h5>
                            <p class="card-text">Gerencie clientes cadastrados.</p>
                            <a href="gestaoUsuariosSite.php" class="btn btn-light">Acessar</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-warning shadow">
                        <div class="card-body">
                            <h5 class="card-title">Produtos</h5>
                            <p class="card-text">Gerencie o catálogo de produtos.</p>
                            <a href="produtos.php" class="btn btn-light">Acessar</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card text-white bg-danger shadow">
                        <div class="card-body">
                            <h5 class="card-title">Relatórios</h5>
                            <p class="card-text">Visualize relatórios gerenciais.</p>
                            <a href="relatorios.php" class="btn btn-light">Acessar</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
