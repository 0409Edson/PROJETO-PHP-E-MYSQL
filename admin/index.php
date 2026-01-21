<?php
/**
 * Dashboard Admin - Livraria Online
 */
$pageTitle = 'Dashboard';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('Acesso restrito a administradores.', 'danger');
    redirect('../login.php');
}

$db = getDB();

require_once 'includes/header.php';

$totalBooks = $db->query("SELECT COUNT(*) FROM books")->fetchColumn();
$totalUsers = $db->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalOrders = $db->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalCategories = $db->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$pendingOrders = $db->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
$totalRevenue = $db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$unreadMessages = $db->query("SELECT COUNT(*) FROM contacts WHERE is_read = 0")->fetchColumn();

$recentOrders = $db->query("SELECT o.*, u.name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
$recentBooks = $db->query("SELECT * FROM books ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Total Livros</p>
                    <h3 class="fw-bold mb-0"><?php echo $totalBooks; ?></h3>
                </div>
                <div class="icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-book"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Utilizadores</p>
                    <h3 class="fw-bold mb-0"><?php echo $totalUsers; ?></h3>
                </div>
                <div class="icon bg-success bg-opacity-10 text-success"><i class="bi bi-people"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Encomendas</p>
                    <h3 class="fw-bold mb-0"><?php echo $totalOrders; ?></h3>
                    <?php if ($pendingOrders > 0): ?>
                        <small class="text-warning"><?php echo $pendingOrders; ?> pendentes</small>
                    <?php endif; ?>
                </div>
                <div class="icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-bag"></i></div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between">
                <div>
                    <p class="text-muted mb-1">Receita Total</p>
                    <h3 class="fw-bold mb-0"><?php echo formatPrice($totalRevenue); ?></h3>
                </div>
                <div class="icon bg-info bg-opacity-10 text-info"><i class="bi bi-currency-euro"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Encomendas Recentes</h5>
                <a href="orders.php" class="btn btn-sm btn-primary">Ver Todas</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentOrders as $order):
                            $statusClass = match ($order['status']) { 'pending' => 'warning', 'confirmed' => 'info', 'delivered' => 'success', 'cancelled' => 'danger', default => 'secondary'};
                            ?>
                            <tr>
                                <td>#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo htmlspecialchars($order['name']); ?></td>
                                <td><?php echo formatPrice($order['total_amount']); ?></td>
                                <td><span
                                        class="badge bg-<?php echo $statusClass; ?>"><?php echo ucfirst($order['status']); ?></span>
                                </td>
                                <td><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="table-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Livros Recentes</h5>
                <a href="books.php" class="btn btn-sm btn-primary">Ver Todos</a>
            </div>
            <div class="list-group list-group-flush">
                <?php foreach ($recentBooks as $book): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars(truncateText($book['title'], 25)); ?></h6>
                            <small class="text-muted"><?php echo formatPrice($book['price']); ?></small>
                        </div>
                        <span class="badge bg-secondary"><?php echo $book['stock']; ?> un.</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>