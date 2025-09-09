<?php
session_start();

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'stingray';
$username = 'root';
$password = '';

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buscar produtos do banco de dados
    $categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';
    $marca_filtro = isset($_GET['marca']) ? $_GET['marca'] : '';
    
    $sql = "SELECT * FROM produtos WHERE estado = 'disponivel'";
    
    if (!empty($categoria_filtro)) {
        $sql .= " AND categoria = :categoria";
    }
    
    if (!empty($marca_filtro)) {
        $sql .= " AND marca = :marca";
    }
    
    $sql .= " ORDER BY id_produto DESC";
    
    $stmt = $pdo->prepare($sql);
    
    if (!empty($categoria_filtro)) {
        $stmt->bindValue(':categoria', $categoria_filtro);
    }
    
    if (!empty($marca_filtro)) {
        $stmt->bindValue(':marca', $marca_filtro);
    }
    
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Buscar categorias e marcas distintas para os filtros
    $categorias = $pdo->query("SELECT DISTINCT categoria FROM produtos ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);
    $marcas = $pdo->query("SELECT DISTINCT marca FROM produtos ORDER BY marca")->fetchAll(PDO::FETCH_COLUMN);
    
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Informações do carrinho
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total_itens 
    FROM carrinho_itens ci 
    JOIN carrinhos c ON ci.carrinho_id = c.id 
    WHERE c.usuario_id = ? AND c.status = 'aberto'
");
$stmt->execute([$_SESSION['usuario_id'] ?? 0]);
$carrinho_info = $stmt->fetch(PDO::FETCH_ASSOC);
$total_itens = $carrinho_info['total_itens'] ?? 0;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Stingray Tech - Produtos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="css/style.css" rel="stylesheet">
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">
      <img src="midias/logoStingray.png" alt="Logo Stingray" id="nav-logo">
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="#products-section">Produtos</a></li>
        <li class="nav-item"><a class="nav-link" href="#contato">Contato</a></li>
        <li class="nav-item"><a class="nav-link" href="#crdt">Créditos</a></li>
        <li class="nav-item"><a class="nav-link" href="ver_carrinho.php">Carrinho (<?= $total_itens ?>)</a></li>

        <?php if (isset($_SESSION['usuario_id'])): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Olá, <?= htmlspecialchars($_SESSION['usuario_nome']) ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-dark">
            <li><a class="dropdown-item" href="contaUsuario.php">Minha Conta</a></li>
            <li><a class="dropdown-item" href="meus_pedidos.php">Meus Pedidos</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="logout.php">Sair</a></li>
          </ul>
        </li>
        <?php else: ?>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <li class="nav-item"><a class="nav-link" href="registro.php">Criar Conta</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>


<!-- Vídeo -->
<div class="video-container">
    <video src="midias/videoPC.mp4" autoplay muted loop></video>
    <div class="video-text">
        <h1>O melhor e-commerce brasileiro</h1>
        <p>Compre agora mesmo</p>
    </div>
</div>

<!-- Produtos -->
<div class="main-container" id="products-section">
    <aside class="sidebar">
        <div>
            <div class="filter-title">
                <h3>Categorias</h3>
                <?php if (!empty($categoria_filtro)): ?>
                <span class="clear-filters" onclick="window.location.href='?'">Limpar</span>
                <?php endif; ?>
            </div>
            <ul>
                <?php foreach ($categorias as $categoria): ?>
                <li class="<?= ($categoria_filtro == $categoria) ? 'active' : '' ?>" 
                    onclick="window.location.href='?categoria=<?= urlencode($categoria) ?>&marca=<?= urlencode($marca_filtro) ?>'">
                    <?= htmlspecialchars($categoria) ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div>
            <div class="filter-title">
                <h3>Marcas</h3>
                <?php if (!empty($marca_filtro)): ?>
                <span class="clear-filters" onclick="window.location.href='?categoria=<?= urlencode($categoria_filtro) ?>'">Limpar</span>
                <?php endif; ?>
            </div>
            <ul>
                <?php foreach ($marcas as $marca): ?>
                <li class="<?= ($marca_filtro == $marca) ? 'active' : '' ?>" 
                    onclick="window.location.href='?marca=<?= urlencode($marca) ?>&categoria=<?= urlencode($categoria_filtro) ?>'">
                    <?= htmlspecialchars($marca) ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </aside>

    <section class="products-section">
        <div class="products-grid">
            <?php if (count($produtos) > 0): ?>
                <?php foreach ($produtos as $produto): ?>
                <div class="product-card">
                    <img src="midias/produtos/<?= $produto['id_produto'] ?>.jpg" alt="<?= htmlspecialchars($produto['nome_produto']) ?>">
                    <h4><?= htmlspecialchars($produto['nome_produto']) ?></h4>
                    <div class="category"><?= htmlspecialchars($produto['categoria']) ?></div>
                    <div class="brand"><?= htmlspecialchars($produto['marca']) ?></div>
                    <div class="price">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></div>
                    <?php if ($produto['quantidade_estoque'] <= 0): ?>
                        <span class="badge bg-danger">Indisponível</span>
                        <button class="btn btn-secondary mt-2" disabled>Adicionar ao Carrinho</button>
                    <?php else: ?>
                        <form method="GET" action="ver_carrinho.php">
                            <input type="hidden" name="adicionar" value="<?= $produto['id_produto'] ?>">
                            <button type="submit" class="btn btn-primary mt-2">Adicionar ao Carrinho</button>
                        </form>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">Nenhum produto encontrado com os filtros selecionados.</div>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- Contato e Projeto -->
<section class="projects-section">
    <div class="container px-4 px-lg-6" id="contato">
        <div class="row gx-0 justify-content-center mb-5">
            <div class="col-lg-6 col-md-12 mb-4">
                <img class="img-fluid" src="midias/logoStingray2.png"/>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="bg-black text-center h-100 project">
                    <div class="d-flex h-100">
                        <div class="project-text w-100 my-auto">
                            <h4 class="text-white" id="sbn">Prezamos pelo melhor produto</h4>
                            <p class="mb-0 text-white">
                                Stingray é um projeto acadêmico de e-commerce especializado em acessórios e periféricos para computadores.
                                O usuário pode visualizar detalhes, comparar e adicionar itens ao carrinho de forma simples e segura.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>

<!-- Rodapé -->
<footer id="crdt">
    Projeto protegido pela <strong>GPL-3.0 license</strong> <br>
    Desenvolvido por <a href="mailto:guilhermefilho095@gmail.com" style="color: white;">guilhermefilho095@gmail.com</a>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
