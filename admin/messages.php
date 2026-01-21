<?php
/**
 * Mensagens de Contacto - Admin
 */
$pageTitle = 'Mensagens';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('Acesso restrito a administradores.', 'danger');
    redirect('../login.php');
}

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'mark_read') {
        $db->prepare("UPDATE contacts SET is_read = 1 WHERE id = ?")->execute([(int) $_POST['id']]);
    } elseif ($_POST['action'] === 'delete') {
        $db->prepare("DELETE FROM contacts WHERE id = ?")->execute([(int) $_POST['id']]);
        setFlashMessage('Mensagem eliminada!', 'success');
    }
    redirect('messages.php');
    exit;
}

require_once 'includes/header.php';

$messages = $db->query("SELECT * FROM contacts ORDER BY created_at DESC")->fetchAll();
$unread = array_filter($messages, fn($m) => !$m['is_read']);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0"><?php echo count($messages); ?> mensagens (<?php echo count($unread); ?> não lidas)</p>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th></th>
                    <th>De</th>
                    <th>Assunto</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($messages as $m): ?>
                    <tr class="<?php echo !$m['is_read'] ? 'fw-bold' : ''; ?>">
                        <td><?php echo !$m['is_read'] ? '<span class="badge bg-primary">Novo</span>' : ''; ?></td>
                        <td><?php echo htmlspecialchars($m['name']); ?><br><small
                                class="text-muted"><?php echo htmlspecialchars($m['email']); ?></small></td>
                        <td><?php echo htmlspecialchars($m['subject'] ?: '(Sem assunto)'); ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($m['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                data-bs-target="#msg<?php echo $m['id']; ?>"><i class="bi bi-eye"></i></button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Eliminar?')">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id"
                                    value="<?php echo $m['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>

                    <div class="modal fade" id="msg<?php echo $m['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <?php echo htmlspecialchars($m['subject'] ?: '(Sem assunto)'); ?></h5><button
                                        type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>De:</strong> <?php echo htmlspecialchars($m['name']); ?>
                                        &lt;<?php echo htmlspecialchars($m['email']); ?>&gt;</p>
                                    <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($m['created_at'])); ?>
                                    </p>
                                    <hr>
                                    <p><?php echo nl2br(htmlspecialchars($m['message'])); ?></p>
                                </div>
                                <div class="modal-footer">
                                    <?php if (!$m['is_read']): ?>
                                        <form method="POST"><input type="hidden" name="action" value="mark_read"><input
                                                type="hidden" name="id" value="<?php echo $m['id']; ?>"><button type="submit"
                                                class="btn btn-primary">Marcar como Lida</button></form>
                                    <?php endif; ?>
                                    <a href="mailto:<?php echo htmlspecialchars($m['email']); ?>"
                                        class="btn btn-outline-primary">Responder</a>
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