<?php
/**
 * Gestão de Subcategorias - Admin
 */
$pageTitle = 'Subcategorias';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('Acesso restrito a administradores.', 'danger');
    redirect('../login.php');
}

$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $stmt = $db->prepare("INSERT INTO subcategories (category_id, name, description) VALUES (?, ?, ?)");
                $stmt->execute([(int) $_POST['category_id'], sanitize($_POST['name']), sanitize($_POST['description'])]);
                setFlashMessage('Subcategoria adicionada!', 'success');
                break;
            case 'edit':
                $stmt = $db->prepare("UPDATE subcategories SET category_id = ?, name = ?, description = ? WHERE id = ?");
                $stmt->execute([(int) $_POST['category_id'], sanitize($_POST['name']), sanitize($_POST['description']), (int) $_POST['id']]);
                setFlashMessage('Subcategoria atualizada!', 'success');
                break;
            case 'delete':
                $db->prepare("DELETE FROM subcategories WHERE id = ?")->execute([(int) $_POST['id']]);
                setFlashMessage('Subcategoria eliminada!', 'success');
                break;
        }
        redirect('subcategories.php');
        exit;
    }
}

require_once 'includes/header.php';

$subcategories = $db->query("SELECT s.*, c.name as category_name FROM subcategories s LEFT JOIN categories c ON s.category_id = c.id ORDER BY c.name, s.name")->fetchAll();
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0"><?php echo count($subcategories); ?> subcategorias</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i
            class="bi bi-plus me-2"></i>Nova Subcategoria</button>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subcategories as $sub): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($sub['name']); ?></strong></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($sub['category_name']); ?></span>
                        </td>
                        <td class="text-muted"><?php echo htmlspecialchars(truncateText($sub['description'], 40)); ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary"
                                onclick="editSub(<?php echo htmlspecialchars(json_encode($sub)); ?>)"><i
                                    class="bi bi-pencil"></i></button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Eliminar?')">
                                <input type="hidden" name="action" value="delete"><input type="hidden" name="id"
                                    value="<?php echo $sub['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger"><i
                                        class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Subcategoria</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                <div class="mb-3"><label class="form-label">Categoria *</label><select class="form-select"
                        name="category_id" required>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="mb-3"><label class="form-label">Nome *</label><input type="text" class="form-control"
                        name="name" required></div>
                <div class="mb-3"><label class="form-label">Descrição</label><textarea class="form-control"
                        name="description" rows="2"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button type="submit"
                    class="btn btn-primary">Adicionar</button></div>
        </form>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Subcategoria</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="edit_id">
                <div class="mb-3"><label class="form-label">Categoria *</label><select class="form-select"
                        name="category_id" id="edit_category_id" required>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="mb-3"><label class="form-label">Nome *</label><input type="text" class="form-control"
                        name="name" id="edit_name" required></div>
                <div class="mb-3"><label class="form-label">Descrição</label><textarea class="form-control"
                        name="description" id="edit_description" rows="2"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button type="submit"
                    class="btn btn-primary">Guardar</button></div>
        </form>
    </div>
</div>

<script>
    function editSub(s) {
        document.getElementById('edit_id').value = s.id;
        document.getElementById('edit_category_id').value = s.category_id;
        document.getElementById('edit_name').value = s.name;
        document.getElementById('edit_description').value = s.description || '';
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>

<?php require_once 'includes/footer.php'; ?>