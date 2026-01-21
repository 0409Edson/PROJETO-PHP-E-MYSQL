<?php
/**
 * Checkout - Livraria Online
 */
$pageTitle = 'Finalizar Compra';
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    setFlashMessage('Por favor, faça login para finalizar a compra.', 'warning');
    redirect('login.php');
}

$db = getDB();
$userId = $_SESSION['user_id'];

$userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

$cartStmt = $db->prepare("SELECT c.*, b.title, b.price, b.stock FROM cart c JOIN books b ON c.book_id = b.id WHERE c.user_id = ?");
$cartStmt->execute([$userId]);
$cartItems = $cartStmt->fetchAll();

if (empty($cartItems)) {
    redirect('cart.php');
}

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = $subtotal >= 25 ? 0 : 3.99;
$total = $subtotal + $shipping;

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city = sanitize($_POST['city'] ?? '');
    $postalCode = sanitize($_POST['postal_code'] ?? '');

    if (empty($name) || empty($phone) || empty($address) || empty($city) || empty($postalCode)) {
        $errors[] = 'Preencha todos os campos obrigatórios.';
    }

    if (empty($errors)) {
        try {
            $db->beginTransaction();

            $fullAddress = "$address, $postalCode $city";
            $orderStmt = $db->prepare("INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address) VALUES (?, ?, 'pending', 'Pagamento na Entrega', ?)");
            $orderStmt->execute([$userId, $total, $fullAddress]);
            $orderId = $db->lastInsertId();

            foreach ($cartItems as $item) {
                $itemStmt = $db->prepare("INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)");
                $itemStmt->execute([$orderId, $item['book_id'], $item['quantity'], $item['price']]);
                $db->prepare("UPDATE books SET stock = stock - ? WHERE id = ?")->execute([$item['quantity'], $item['book_id']]);
            }

            $db->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$userId]);
            $db->commit();

            $_SESSION['order_id'] = $orderId;
            redirect('order-confirmation.php');

        } catch (Exception $e) {
            $db->rollBack();
            $errors[] = 'Erro ao processar encomenda.';
        }
    }
}

require_once 'includes/header.php';
?>

<section class="bg-gradient-primary text-white py-4">
    <div class="container">
        <h1 class="h2 fw-bold mb-1"><i class="bi bi-credit-card me-2"></i>Finalizar Compra</h1>
    </div>
</section>

<section class="section py-5">
    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php echo implode('<br>', $errors); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5><i class="bi bi-person me-2"></i>Dados de Entrega</h5>
                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <label class="form-label">Nome *</label>
                                    <input type="text" class="form-control" name="name"
                                        value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telefone *</label>
                                    <input type="tel" class="form-control" name="phone"
                                        value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Morada *</label>
                                    <textarea class="form-control" name="address" rows="2" required></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Cidade *</label>
                                    <input type="text" class="form-control" name="city" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Código Postal *</label>
                                    <input type="text" class="form-control" name="postal_code" placeholder="0000-000"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5><i class="bi bi-cash-coin me-2"></i>Pagamento</h5>
                            <div class="form-check p-3 border rounded bg-light mt-3">
                                <input class="form-check-input" type="radio" checked disabled>
                                <label class="form-check-label"><strong>Pagamento na Entrega</strong></label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4>Resumo</h4>
                        <?php foreach ($cartItems as $item): ?>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span>
                                    <?php echo htmlspecialchars(truncateText($item['title'], 25)); ?> x
                                    <?php echo $item['quantity']; ?>
                                </span>
                                <span>
                                    <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span>Subtotal:</span><span>
                                <?php echo formatPrice($subtotal); ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Portes:</span><span>
                                <?php echo $shipping === 0 ? 'Grátis' : formatPrice($shipping); ?>
                            </span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Total:</span><span class="text-success">
                                <?php echo formatPrice($total); ?>
                            </span>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100 mt-4"><i
                                class="bi bi-check-circle me-2"></i>Confirmar Encomenda</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>