<?php
/**
 * Gestão de Livros - Admin
 */
$pageTitle = 'Livros';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Verificar acesso admin
if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('Acesso restrito a administradores.', 'danger');
    redirect('../login.php');
}

$db = getDB();

// Processar ações ANTES de qualquer output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $image = '';
                if (!empty($_FILES['image']['name'])) {
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $image = uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/books/' . $image);
                }
                $stmt = $db->prepare("INSERT INTO books (title, author, description, price, image, category_id, subcategory_id, stock, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([sanitize($_POST['title']), sanitize($_POST['author']), sanitize($_POST['description']), (float)$_POST['price'], $image, (int)$_POST['category_id'] ?: null, (int)$_POST['subcategory_id'] ?: null, (int)$_POST['stock'], isset($_POST['featured']) ? 1 : 0]);
                setFlashMessage('Livro adicionado!', 'success');
                break;
            case 'edit':
                $image = $_POST['current_image'];
                if (!empty($_FILES['image']['name'])) {
                    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $image = uniqid() . '.' . $ext;
                    move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/books/' . $image);
                }
                $stmt = $db->prepare("UPDATE books SET title=?, author=?, description=?, price=?, image=?, category_id=?, subcategory_id=?, stock=?, featured=? WHERE id=?");
                $stmt->execute([sanitize($_POST['title']), sanitize($_POST['author']), sanitize($_POST['description']), (float)$_POST['price'], $image, (int)$_POST['category_id'] ?: null, (int)$_POST['subcategory_id'] ?: null, (int)$_POST['stock'], isset($_POST['featured']) ? 1 : 0, (int)$_POST['id']]);
                setFlashMessage('Livro atualizado!', 'success');
                break;
            case 'delete':
                $db->prepare("DELETE FROM books WHERE id = ?")->execute([(int)$_POST['id']]);
                setFlashMessage('Livro eliminado!', 'success');
                break;
        }
        redirect('books.php');
        exit;
    }
}

// Agora incluir o header (que gera output HTML)
require_once 'includes/header.php';

$books = $db->query("SELECT b.*, c.name as cat_name FROM books b LEFT JOIN categories c ON b.category_id = c.id ORDER BY b.created_at DESC")->fetchAll();
$categories = $db->query("SELECT * FROM categories ORDER BY name")->fetchAll();
$subcategories = $db->query("SELECT * FROM subcategories ORDER BY name")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0"><?php echo count($books); ?> livros</p>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal"><i class="bi bi-plus me-2"></i>Novo Livro</button>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Livro</th><th>Categoria</th><th>Preço</th><th>Stock</th><th>Destaque</th><th>Ações</th></tr></thead>
            <tbody>
                <?php foreach ($books as $b): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <?php if ($b['image']): ?><img src="../assets/images/books/<?php echo $b['image']; ?>" alt="" style="width:40px;height:50px;object-fit:cover;border-radius:4px" class="me-2"><?php endif; ?>
                            <div><strong><?php echo htmlspecialchars(truncateText($b['title'], 30)); ?></strong><br><small class="text-muted"><?php echo htmlspecialchars($b['author']); ?></small></div>
                        </div>
                    </td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($b['cat_name'] ?? '-'); ?></span></td>
                    <td><?php echo formatPrice($b['price']); ?></td>
                    <td><span class="badge bg-<?php echo $b['stock'] > 0 ? 'success' : 'danger'; ?>"><?php echo $b['stock']; ?></span></td>
                    <td><?php echo $b['featured'] ? '<i class="bi bi-star-fill text-warning"></i>' : '-'; ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick="editBook(<?php echo htmlspecialchars(json_encode($b)); ?>)"><i class="bi bi-pencil"></i></button>
                        <form method="POST" class="d-inline" onsubmit="return confirm('Eliminar?')">
                            <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Adicionar -->
<div class="modal fade" id="addModal" tabindex="-1"><div class="modal-dialog modal-lg"><form method="POST" enctype="multipart/form-data" class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Novo Livro</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input type="hidden" name="action" value="add">
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Título *</label><input type="text" class="form-control" name="title" required></div>
            <div class="col-md-4"><label class="form-label">Autor *</label><input type="text" class="form-control" name="author" required></div>
            <div class="col-12"><label class="form-label">Descrição</label><textarea class="form-control" name="description" rows="3"></textarea></div>
            <div class="col-md-4"><label class="form-label">Preço (€) *</label><input type="number" class="form-control" name="price" step="0.01" min="0" required></div>
            <div class="col-md-4"><label class="form-label">Stock</label><input type="number" class="form-control" name="stock" value="0" min="0"></div>
            <div class="col-md-4"><label class="form-label">Imagem</label><input type="file" class="form-control" name="image" accept="image/*"></div>
            <div class="col-md-4"><label class="form-label">Categoria</label><select class="form-select" name="category_id"><option value="">-- Nenhuma --</option><?php foreach ($categories as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Subcategoria</label><select class="form-select" name="subcategory_id"><option value="">-- Nenhuma --</option><?php foreach ($subcategories as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="featured" id="add_featured"><label class="form-check-label" for="add_featured">Destaque</label></div></div>
        </div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Adicionar</button></div>
</form></div></div>

<!-- Modal Editar -->
<div class="modal fade" id="editModal" tabindex="-1"><div class="modal-dialog modal-lg"><form method="POST" enctype="multipart/form-data" class="modal-content">
    <div class="modal-header"><h5 class="modal-title">Editar Livro</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input type="hidden" name="action" value="edit"><input type="hidden" name="id" id="e_id"><input type="hidden" name="current_image" id="e_img">
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label">Título *</label><input type="text" class="form-control" name="title" id="e_title" required></div>
            <div class="col-md-4"><label class="form-label">Autor *</label><input type="text" class="form-control" name="author" id="e_author" required></div>
            <div class="col-12"><label class="form-label">Descrição</label><textarea class="form-control" name="description" id="e_desc" rows="3"></textarea></div>
            <div class="col-md-4"><label class="form-label">Preço (€) *</label><input type="number" class="form-control" name="price" id="e_price" step="0.01" min="0" required></div>
            <div class="col-md-4"><label class="form-label">Stock</label><input type="number" class="form-control" name="stock" id="e_stock" min="0"></div>
            <div class="col-md-4"><label class="form-label">Nova Imagem</label><input type="file" class="form-control" name="image" accept="image/*"></div>
            <div class="col-md-4"><label class="form-label">Categoria</label><select class="form-select" name="category_id" id="e_cat"><option value="">-- Nenhuma --</option><?php foreach ($categories as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Subcategoria</label><select class="form-select" name="subcategory_id" id="e_subcat"><option value="">-- Nenhuma --</option><?php foreach ($subcategories as $s): ?><option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['name']); ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4 d-flex align-items-end"><div class="form-check"><input class="form-check-input" type="checkbox" name="featured" id="e_featured"><label class="form-check-label" for="e_featured">Destaque</label></div></div>
        </div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary">Guardar</button></div>
</form></div></div>

<script>
function editBook(b) {
    document.getElementById('e_id').value = b.id;
    document.getElementById('e_img').value = b.image || '';
    document.getElementById('e_title').value = b.title;
    document.getElementById('e_author').value = b.author;
    document.getElementById('e_desc').value = b.description || '';
    document.getElementById('e_price').value = b.price;
    document.getElementById('e_stock').value = b.stock;
    document.getElementById('e_cat').value = b.category_id || '';
    document.getElementById('e_subcat').value = b.subcategory_id || '';
    document.getElementById('e_featured').checked = b.featured == 1;
    new bootstrap.Modal(document.getElementById('editModal')).show();
}
</script>

<?php require_once 'includes/footer.php'; ?>