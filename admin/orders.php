<?php
/**
 * Gestão de Encomendas - Admin
 */
$pageTitle = 'Encomendas';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('Acesso restrito a administradores.', 'danger');
    redirect('../login.php');
}

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $stmt = $db->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([sanitize($_POST['status']), (int) $_POST['id']]);
    setFlashMessage('Status atualizado!', 'success');
    redirect('orders.php');
    exit;
}

require_once 'includes/header.php';

$orders = $db->query("SELECT o.*, u.name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0"><?php echo count($orders); ?> encomendas</p>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Pagamento</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o):
                    $statusClass = match ($o['status']) { 'pending' => 'warning', 'confirmed' => 'info', 'shipped' => 'primary', 'delivered' => 'success', 'cancelled' => 'danger', default => 'secondary'};
                    $itemsStmt = $db->prepare("SELECT oi.*, b.title FROM order_items oi JOIN books b ON oi.book_id = b.id WHERE oi.order_id = ?");
                    $itemsStmt->execute([$o['id']]);
                    $items = $itemsStmt->fetchAll();
                    ?>
                    <tr>
                        <td><strong>#<?php echo str_pad($o['id'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                        <td><?php echo htmlspecialchars($o['name']); ?><br><small
                                class="text-muted"><?php echo htmlspecialchars($o['email']); ?></small></td>
                        <td><strong><?php echo formatPrice($o['total_amount']); ?></strong></td>
                        <td><span class="badge bg-<?php echo $statusClass; ?>"><?php echo ucfirst($o['status']); ?></span>
                        </td>
                        <td><small><?php echo htmlspecialchars($o['payment_method']); ?></small></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($o['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#modal<?php echo $o['id']; ?>"><i class="bi bi-eye"></i></button>
                        </td>
                    </tr>

                    <!-- Modal Detalhes -->
                    <div class="modal fade" id="modal<?php echo $o['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Encomenda
                                        #<?php echo str_pad($o['id'], 6, '0', STR_PAD_LEFT); ?></h5><button type="button"
                                        class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Cliente:</strong> <?php echo htmlspecialchars($o['name']); ?>
                                        (<?php echo htmlspecialchars($o['email']); ?>)</p>
                                    <p><strong>Morada:</strong> <?php echo htmlspecialchars($o['shipping_address']); ?></p>
                                    <hr>
                                    <h6>Itens:</h6>
                                    <ul>
                                        <?php foreach ($items as $i): ?>
                                            <li><?php echo htmlspecialchars($i['title']); ?> x<?php echo $i['quantity']; ?> -
                                                <?php echo formatPrice($i['price'] * $i['quantity']); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <p class="fw-bold">Total: <?php echo formatPrice($o['total_amount']); ?></p>
                                    <hr>
                                    <form method="POST">
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="id" value="<?php echo $o['id']; ?>">
                                        <label class="form-label">Atualizar Status:</label>
                                        <select class="form-select mb-2" name="status">
                                            <option value="pending" <?php echo $o['status'] == 'pending' ? 'selected' : ''; ?>>Pendente</option>
                                            <option value="confirmed" <?php echo $o['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmada</option>
                                            <option value="shipped" <?php echo $o['status'] == 'shipped' ? 'selected' : ''; ?>>Enviada</option>
                                            <option value="delivered" <?php echo $o['status'] == 'delivered' ? 'selected' : ''; ?>>Entregue</option>
                                            <option value="cancelled" <?php echo $o['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelada</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary w-100">Atualizar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>