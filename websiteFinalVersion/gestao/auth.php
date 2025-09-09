<?php
// Inicia a sessão
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: loginGestao.php");
    exit;
}

/**
 * Função para verificar se o usuário tem acesso a determinada página
 * @param string|array $niveis Níveis de acesso permitidos (ex: "admin,repositor" ou ['admin','repositor'])
 */
function checkAccess($niveis) {
    global $_SESSION; // importante para acessar a sessão dentro da função

    // Converte string separada por vírgula em array, se necessário
    if (!is_array($niveis)) {
        $niveis = explode(',', $niveis);
        $niveis = array_map('trim', $niveis); // remove espaços
    }

    // Admin sempre tem acesso
    if (isset($_SESSION['cargo']) && $_SESSION['cargo'] === 'admin') {
        return;
    }

    // Se não existir cargo na sessão ou cargo não estiver nos níveis permitidos, bloqueia
    if (!isset($_SESSION['cargo']) || !in_array($_SESSION['cargo'], $niveis)) {
        echo "<h3>Acesso negado!</h3>";
        exit;
    }
}

/**
 * Função opcional para exibir links de menu conforme o cargo
 * @param array $permitidos Lista de cargos permitidos para exibir o link
 * @return bool
 */
function menuVisible($permitidos) {
    global $_SESSION;
    if (!isset($_SESSION['cargo'])) return false;
    if ($_SESSION['cargo'] === 'admin') return true; // admin sempre vê
    return in_array($_SESSION['cargo'], $permitidos);
}
?>

