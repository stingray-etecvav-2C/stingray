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
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

$erro = '';

// Processar formulário de registro
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];
    $confirmar_senha = $_POST['confirmar_senha'];
    
    // Validações básicas
    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = 'Todos os campos são obrigatórios.';
    } elseif ($senha !== $confirmar_senha) {
        $erro = 'As senhas não coincidem.';
    } elseif (strlen($senha) < 8) {
        $erro = 'A senha deve ter pelo menos 8 caracteres.';
    } else {
        // Validação de senha forte - CORREÇÃO APLICADA AQUI
        $uppercase = preg_match('@[A-Z]@', $senha);
        $lowercase = preg_match('@[a-z]@', $senha);
        $number = preg_match('@[0-9]@', $senha);
        // Correção: usar [^a-zA-Z0-9] em vez de [^\w] para incluir underscore como caractere especial
        $specialChars = preg_match('@[^a-zA-Z0-9]@', $senha);
        
        if (!$uppercase) {
            $erro = 'A senha deve conter pelo menos uma letra maiúscula.';
        } elseif (!$lowercase) {
            $erro = 'A senha deve conter pelo menos uma letra minúscula.';
        } elseif (!$number) {
            $erro = 'A senha deve conter pelo menos um número.';
        } elseif (!$specialChars) {
            $erro = 'A senha deve conter pelo menos um caractere especial.';
        } else {
            // Verificar se email já existe
            $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $erro = 'Este email já está em uso.';
            } else {
                // Hash da senha
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                
                // Inserir usuário no banco
                $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
                if ($stmt->execute([$nome, $email, $senha_hash])) {
                    $_SESSION['sucesso'] = 'Conta criada com sucesso! Faça login para continuar.';
                    header('Location: login.php');
                    exit;
                } else {
                    $erro = 'Erro ao criar conta. Tente novamente.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stingray Tech - Criar Conta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .requirement {
            margin-bottom: 3px;
        }
        .requirement.met {
            color: #28a745;
        }
        .requirement.unmet {
            color: #dc3545;
        }
    </style>
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
            
            <h2 class="text-center mb-4">Criar Conta</h2>
            
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="registroForm">
                <div class="mb-3">
                    <label for="nome" class="form-label">Nome Completo</label>
                    <input type="text" class="form-control" id="nome" name="nome" required 
                           value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>">
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required 
                           value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                
                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" class="form-control" id="senha" name="senha" required 
                           onkeyup="validarSenha()">
                    <div class="password-requirements">
                        <div id="req-length" class="requirement unmet">• Pelo menos 8 caracteres</div>
                        <div id="req-uppercase" class="requirement unmet">• Pelo menos uma letra maiúscula</div>
                        <div id="req-lowercase" class="requirement unmet">• Pelo menos uma letra minúscula</div>
                        <div id="req-number" class="requirement unmet">• Pelo menos um número</div>
                        <div id="req-special" class="requirement unmet">• Pelo menos um caractere especial</div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required 
                           onkeyup="validarConfirmacaoSenha()">
                    <div id="confirmacao-senha" class="password-requirements"></div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100" id="btn-submit" >Criar Conta</button>
            </form>
            
            <div class="text-center mt-3">
                <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function validarSenha() {
            const senha = document.getElementById('senha').value;
            const btnSubmit = document.getElementById('btn-submit');
            
            // Verificar requisitos - CORREÇÃO APLICADA AQUI
            const temMinimo = senha.length >= 8;
            const temMaiuscula = /[A-Z]/.test(senha);
            const temMinuscula = /[a-z]/.test(senha);
            const temNumero = /[0-9]/.test(senha);
            // Correção: usar [^a-zA-Z0-9] em vez de [^\w] para incluir underscore como caractere especial
            const temEspecial = /[^a-zA-Z0-9]/.test(senha);
            
            // Atualizar visual dos requisitos
            document.getElementById('req-length').className = temMinimo ? 'requirement met' : 'requirement unmet';
            document.getElementById('req-uppercase').className = temMaiuscula ? 'requirement met' : 'requirement unmet';
            document.getElementById('req-lowercase').className = temMinuscula ? 'requirement met' : 'requirement unmet';
            document.getElementById('req-number').className = temNumero ? 'requirement met' : 'requirement unmet';
            document.getElementById('req-special').className = temEspecial ? 'requirement met' : 'requirement unmet';
            
            // Validar confirmação de senha também
            validarConfirmacaoSenha();
            
            // Habilitar botão apenas se todos os requisitos forem atendidos
            const senhaValida = temMinimo && temMaiuscula && temMinuscula && temNumero && temEspecial;
            const confirmacaoValida = document.getElementById('confirmar_senha').value === senha;
            
            btnSubmit.disabled = !(senhaValida && confirmacaoValida);
        }
        
        function validarConfirmacaoSenha() {
            const senha = document.getElementById('senha').value;
            const confirmacao = document.getElementById('confirmar_senha').value;
            const mensagem = document.getElementById('confirmacao-senha');
            const btnSubmit = document.getElementById('btn-submit');
            
            if (confirmacao === '') {
                mensagem.innerHTML = '';
            } else if (confirmacao === senha) {
                mensagem.innerHTML = '<div class="requirement met">• Senhas coincidem</div>';
            } else {
                mensagem.innerHTML = '<div class="requirement unmet">• Senhas não coincidem</div>';
            }
            
            // Validar senha também para atualizar estado do botão
            validarSenha();
        }
    </script>
</body>
</html>
