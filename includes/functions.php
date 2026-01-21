<?php
/**
 * Funções Auxiliares
 * Livraria Online - PHP e MySQL
 */

// Iniciar sessão se ainda não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitizar input do utilizador
 */
function sanitize($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Verificar se o utilizador está logado
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

/**
 * Verificar se o utilizador é admin
 */
function isAdmin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Redirecionar para uma página
 */
function redirect($url)
{
    header("Location: $url");
    exit();
}

/**
 * Mostrar mensagens de alerta
 */
function showAlert($message, $type = 'info')
{
    return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert">
                ' . $message . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

/**
 * Definir mensagem flash
 */
function setFlashMessage($message, $type = 'info')
{
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Obter e limpar mensagem flash
 */
function getFlashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return showAlert($message, $type);
    }
    return '';
}

/**
 * Formatar preço em Euros
 */
function formatPrice($price)
{
    return number_format($price, 2, ',', '.') . ' €';
}

/**
 * Obter total de itens no carrinho
 */
function getCartCount()
{
    if (!isLoggedIn()) {
        return 0;
    }

    require_once __DIR__ . '/../config/database.php';
    $db = getDB();

    $stmt = $db->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();

    return $result['total'] ?? 0;
}

/**
 * Obter todas as categorias
 */
function getCategories()
{
    require_once __DIR__ . '/../config/database.php';
    $db = getDB();

    $stmt = $db->query("SELECT * FROM categories ORDER BY name");
    return $stmt->fetchAll();
}

/**
 * Obter subcategorias por categoria
 */
function getSubcategoriesByCategory($categoryId)
{
    require_once __DIR__ . '/../config/database.php';
    $db = getDB();

    $stmt = $db->prepare("SELECT * FROM subcategories WHERE category_id = ? ORDER BY name");
    $stmt->execute([$categoryId]);
    return $stmt->fetchAll();
}

/**
 * Truncar texto
 */
function truncateText($text, $length = 100)
{
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

/**
 * Gerar slug a partir de texto
 */
function generateSlug($text)
{
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Validar email
 */
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Upload de imagem
 */
function uploadImage($file, $directory = 'assets/images/books/')
{
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Erro no upload do ficheiro.'];
    }

    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipo de ficheiro não permitido.'];
    }

    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'Ficheiro demasiado grande (máx. 5MB).'];
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $destination = __DIR__ . '/../' . $directory . $filename;

    // Criar diretório se não existir
    if (!is_dir(dirname($destination))) {
        mkdir(dirname($destination), 0755, true);
    }

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    }

    return ['success' => false, 'message' => 'Erro ao guardar o ficheiro.'];
}

/**
 * Obter URL base do site
 */
function getBaseUrl()
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);

    // Remover subdiretórios admin se estiver no painel
    $script = str_replace('/admin', '', $script);

    return $protocol . '://' . $host . $script;
}
?>