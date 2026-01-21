<?php
/**
 * Gestão de Categorias - Admin
 */
$pageTitle = 'Categorias';
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('Acesso restrito a administradores.', 'danger');
    redirect('../login.php');
}

$db = getDB();

// Processar ações ANTES do header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = sanitize($_POST['name']);
                $description = sanitize($_POST['description']);
                if (!empty($name)) {
                    $stmt = $db->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
                    $stmt->execute([$name, $description]);
                    setFlashMessage('Categoria adicionada!', 'success');
                }
                break;
            case 'edit':
                $id = (int) $_POST['id'];
                $name = sanitize($_POST['name']);
                $description = sanitize($_POST['description']);
                $stmt = $db->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
                $stmt->execute([$name, $description, $id]);
                setFlashMessage('Categoria atualizada!', 'success');
                break;
            case 'delete':
                $id = (int) $_POST['id'];
                $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
                $stmt->execute([$id]);
                setFlashMessage('Categoria eliminada!', 'success');
                break;
        }
        redirect('categories.php');
        exit;
    }
}

require_once 'includes/header.php';

$categories = $db->query("SELECT c.*, COUNT(b.id) as book_count FROM categories c LEFT JOIN books b ON c.id = b.category_id GROUP BY c.id ORDER BY c.name")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0"><?php echo count($categories); ?> categorias</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i
            class="bi bi-plus me-2"></i>Nova Categoria</button>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Descrição</th>
                    <th>Livros</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                        <td class="text-muted"><?php echo htmlspecialchars(truncateText($cat['description'], 50)); ?></td>
                        <td><span class="badge bg-primary"><?php echo $cat['book_count']; ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary"
                                onclick="editCategory(<?php echo htmlspecialchars(json_encode($cat)); ?>)"><i
                                    class="bi bi-pencil"></i></button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('Eliminar esta categoria?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
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

<!-- Modal Adicionar -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nova Categoria</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="add">
                <div class="mb-3"><label class="form-label">Nome *</label><input type="text" class="form-control"
                        name="name" required></div>
                <div class="mb-3"><label class="form-label">Descrição</label><textarea class="form-control"
                        name="description" rows="3"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button type="submit"
                    class="btn btn-primary">Adicionar</button></div>
        </form>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Categoria</h5><button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" id="edit_id">
                <div class="mb-3"><label class="form-label">Nome *</label><input type="text" class="form-control"
                        name="name" id="edit_name" required></div>
                <div class="mb-3"><label class="form-label">Descrição</label><textarea class="form-control"
                        name="description" id="edit_description" rows="3"></textarea></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">Cancelar</button><button type="submit"
                    class="btn btn-primary">Guardar</button></div>
        </form>
    </div>
</div>

<script>
    function editCategory(cat) {
        document.getElementById('edit_id').value = cat.id;
        document.getElementById('edit_name').value = cat.name;
        document.getElementById('edit_description').value = cat.description || '';
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }
</script>

<?php require_once 'includes/footer.php'; ?>