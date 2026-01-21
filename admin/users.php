<?php
/**
 * Lista de Utilizadores - Admin
 */
$pageTitle = 'Utilizadores';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('Acesso restrito a administradores.', 'danger');
    redirect('../login.php');
}

$db = getDB();

require_once 'includes/header.php';

$users = $db->query("SELECT u.*, (SELECT COUNT(*) FROM orders WHERE user_id = u.id) as order_count FROM users ORDER BY created_at DESC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0"><?php echo count($users); ?> utilizadores registados</p>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Encomendas</th>
                    <th>Tipo</th>
                    <th>Registo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($u['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo htmlspecialchars($u['phone'] ?? '-'); ?></td>
                        <td><span class="badge bg-primary"><?php echo $u['order_count']; ?></span></td>
                        <td><span
                                class="badge bg-<?php echo $u['role'] == 'admin' ? 'danger' : 'secondary'; ?>"><?php echo ucfirst($u['role']); ?></span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>