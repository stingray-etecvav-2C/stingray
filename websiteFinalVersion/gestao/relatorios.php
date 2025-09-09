<?php
require 'auth.php';
require 'conexao.php';
checkAccess('admin,repositor,funcionario');

// Buscar pedidos
$stmt = $pdo->prepare("SELECT p.id, p.usuario_id, p.total, p.status, p.created_at, u.nome 
                       FROM pedidos p
                       JOIN usuarios u ON p.usuario_id = u.id
                       ORDER BY p.created_at DESC");
$stmt->execute();
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Totais para indicadores
$totalPedidos = count($pedidos);
$totalAberto = count(array_filter($pedidos, fn($p) => $p['status'] === 'aberto'));
$totalFinalizado = count(array_filter($pedidos, fn($p) => $p['status'] === 'finalizado'));
$totalValor = array_sum(array_column($pedidos, 'total'));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Relatórios - Stingray</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../css/styleGestao.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 sidebar d-flex flex-column">
            <img src="../midias/logoStingray.png" alt="Stingray" class="top-logo">
            <h4>Relatórios</h4>
            <p>Bem-vindo, <?= htmlspecialchars($_SESSION['usuario_nome']); ?></p>
            <ul class="nav flex-column">
                <li class="nav-item"><a href="dashboard.php" class="nav-link">Dashboard</a></li>
                <li class="nav-item"><a href="produtos.php" class="nav-link">Gerenciar Produtos</a></li>
                <li class="nav-item"><a href="gestaoUsuarios.php" class="nav-link">Usuários Gestão</a></li>
                <li class="nav-item"><a href="gestaoUsuariosSite.php" class="nav-link">Usuários do Site</a></li>
                <li class="nav-item"><a href="relatorios.php" class="nav-link active">Relatórios</a></li>
                <li class="nav-item"><a href="logout.php" class="nav-link text-danger">Sair</a></li>
            </ul>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            <h1 class="mb-4">Relatórios de Pedidos</h1>

            <!-- Cards de indicadores -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card card-indicador text-white bg-primary shadow">
                        <div class="card-body text-center">
                            <h2><?= $totalPedidos ?></h2>
                            <h5>Total de Pedidos</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-indicador text-white bg-warning shadow">
                        <div class="card-body text-center">
                            <h2><?= $totalAberto ?></h2>
                            <h5>Pedidos Abertos</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-indicador text-white bg-success shadow">
                        <div class="card-body text-center">
                            <h2><?= $totalFinalizado ?></h2>
                            <h5>Pedidos Finalizados</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-indicador text-white bg-info shadow">
                        <div class="card-body text-center">
                            <h2>R$ <?= number_format($totalValor,2,",",".") ?></h2>
                            <h5>Valor Total</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabela de pedidos -->
            <div class="table-responsive">
                <table class="table table-hover table-dark">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Usuário</th>
                            <th>Total (R$)</th>
                            <th>Status</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pedidos as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['nome']) ?></td>
                            <td><?= number_format($p['total'],2,",",".") ?></td>
                            <td class="status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></td>
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
