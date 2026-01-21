<?php
/**
 * Minhas Encomendas - Livraria Online
 */
$pageTitle = 'Minhas Encomendas';
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$userId = $_SESSION['user_id'];

$ordersStmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$ordersStmt->execute([$userId]);
$orders = $ordersStmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="bg-gradient-primary text-white py-4">
    <div class="container">
        <h1 class="h2 fw-bold mb-1"><i class="bi bi-bag me-2"></i>Minhas Encomendas</h1>
    </div>
</section>

<section class="section py-5">
    <div class="container">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="bi bi-bag-x text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Ainda não tem encomendas</h4>
                <a href="books.php" class="btn btn-primary mt-3">Explorar Catálogo</a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($orders as $order):
                    $itemsStmt = $db->prepare("SELECT oi.*, b.title FROM order_items oi JOIN books b ON oi.book_id = b.id WHERE oi.order_id = ?");
                    $itemsStmt->execute([$order['id']]);
                    $items = $itemsStmt->fetchAll();

                    $statusClass = match ($order['status']) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        default => 'secondary'
                    };
                    $statusText = match ($order['status']) {
                        'pending' => 'Pendente',
                        'confirmed' => 'Confirmada',
                        'shipped' => 'Enviada',
                        'delivered' => 'Entregue',
                        'cancelled' => 'Cancelada',
                        default => $order['status']
                    };
                    ?>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Encomenda #
                                        <?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                                    </strong>
                                    <small class="text-muted ms-2">
                                        <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                    </small>
                                </div>
                                <span class="badge bg-<?php echo $statusClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-3">
                                    <?php foreach ($items as $item): ?>
                                        <li>
                                            <?php echo htmlspecialchars($item['title']); ?> x
                                            <?php echo $item['quantity']; ?> -
                                            <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="d-flex justify-content-between">
                                    <span><strong>Total:</strong>
                                        <?php echo formatPrice($order['total_amount']); ?>
                                    </span>
                                    <span><strong>Pagamento:</strong>
                                        <?php echo htmlspecialchars($order['payment_method']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>