<?php
session_start();

// Se o usuário já estiver logado, redireciona para a página inicial
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

// Configurações do banco de dados
$host = 'localhost';
$dbname = 'stingray';
$username = 'root';
$password = '';

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

$erro = '';

// Mostrar mensagem de sucesso se veio do registro
if (isset($_SESSION['sucesso'])) {
    $sucesso = $_SESSION['sucesso'];
    unset($_SESSION['sucesso']);
}

// Processar formulário de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    
    if (empty($email) || empty($senha)) {
        $erro = 'Por favor, preencha todos os campos.';
    } else {
        // Buscar usuário pelo email
        $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            
            // Redirecionar para a página inicial
            header('Location: index.php');
            exit;
        } else {
            $erro = 'Email ou senha incorretos.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stingray Tech - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">
                <img src="midias/logoStingray.png" alt="Logo Stingray" id="nav-logo">
            </a>
        </div>
    </nav>

    <div class="container">
        <div class="auth-container">
            <div class="auth-logo">
                <img src="midias/logoStingray.png" alt="Stingray Tech">
            </div>
            
            <h2 class="text-center mb-4">Login</h2>
            
            <?php if (isset($sucesso)): ?>
                <div class="alert alert-success"><?= $sucesso ?></div>
            <?php endif; ?>
            
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required 
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Entrar</button>
            </form>
            
            <div class="text-center mt-3">
                <p>Não tem uma conta? <a href="registro.php">Crie uma agora</a></p>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>