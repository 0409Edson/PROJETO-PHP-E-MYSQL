<?php
/**
 * AJAX - Adicionar ao Carrinho
 */
header('Content-Type: application/json');
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Faça login para adicionar ao carrinho']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método inválido']);
    exit;
}

$bookId = (int) ($_POST['book_id'] ?? 0);
$quantity = max(1, (int) ($_POST['quantity'] ?? 1));
$userId = $_SESSION['user_id'];

if (!$bookId) {
    echo json_encode(['success' => false, 'message' => 'Livro inválido']);
    exit;
}

$db = getDB();

// Verificar stock
$stockStmt = $db->prepare("SELECT stock FROM books WHERE id = ?");
$stockStmt->execute([$bookId]);
$stock = $stockStmt->fetchColumn();

if ($stock === false) {
    echo json_encode(['success' => false, 'message' => 'Livro não encontrado']);
    exit;
}

// Verificar se já está no carrinho
$existsStmt = $db->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND book_id = ?");
$existsStmt->execute([$userId, $bookId]);
$existing = $existsStmt->fetch();

if ($existing) {
    $newQty = $existing['quantity'] + $quantity;
    if ($newQty > $stock) {
        echo json_encode(['success' => false, 'message' => 'Quantidade indisponível em stock']);
        exit;
    }
    $updateStmt = $db->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $updateStmt->execute([$newQty, $existing['id']]);
} else {
    if ($quantity > $stock) {
        echo json_encode(['success' => false, 'message' => 'Quantidade indisponível em stock']);
        exit;
    }
    $insertStmt = $db->prepare("INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?)");
    $insertStmt->execute([$userId, $bookId, $quantity]);
}

// Obter total no carrinho
$countStmt = $db->prepare("SELECT SUM(quantity) FROM cart WHERE user_id = ?");
$countStmt->execute([$userId]);
$cartCount = $countStmt->fetchColumn() ?? 0;

echo json_encode(['success' => true, 'cartCount' => $cartCount]);
