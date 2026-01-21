<?php
/**
 * Confirmação de Encomenda - Livraria Online
 */
$pageTitle = 'Encomenda Confirmada';
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isLoggedIn() || !isset($_SESSION['order_id'])) {
    redirect('index.php');
}

$db = getDB();
$orderId = $_SESSION['order_id'];
unset($_SESSION['order_id']);

$orderStmt = $db->prepare("SELECT o.*, u.name, u.email FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$orderStmt->execute([$orderId]);
$order = $orderStmt->fetch();

$itemsStmt = $db->prepare("SELECT oi.*, b.title, b.author FROM order_items oi JOIN books b ON oi.book_id = b.id WHERE oi.order_id = ?");
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-5">
                        <div class="mb-4">
                            <i class="bi bi-check-circle text-success" style="font-size: 5rem;"></i>
                        </div>
                        <h1 class="h2 mb-3">Encomenda Confirmada!</h1>
                        <p class="lead text-muted mb-4">Obrigado pela sua compra,
                            <?php echo htmlspecialchars($order['name']); ?>!
                        </p>

                        <div class="bg-light rounded p-4 mb-4">
                            <h5 class="mb-3">Detalhes da Encomenda #
                                <?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?>
                            </h5>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Livro</th>
                                            <th>Qtd</th>
                                            <th>Preço</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                            <tr>
                                                <td>
                                                    <?php echo htmlspecialchars($item['title']); ?>
                                                </td>
                                                <td>
                                                    <?php echo $item['quantity']; ?>
                                                </td>
                                                <td>
                                                    <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-bold">
                                            <td colspan="2">Total</td>
                                            <td>
                                                <?php echo formatPrice($order['total_amount']); ?>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6>Morada de Entrega:</h6>
                            <p class="text-muted">
                                <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                            </p>
                            <p><strong>Pagamento:</strong>
                                <?php echo htmlspecialchars($order['payment_method']); ?>
                            </p>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <a href="orders.php" class="btn btn-primary"><i class="bi bi-bag me-2"></i>Ver
                                Encomendas</a>
                            <a href="books.php" class="btn btn-outline-primary"><i
                                    class="bi bi-collection me-2"></i>Continuar a Comprar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>