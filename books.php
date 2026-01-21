<?php
/**
 * Catálogo de Livros - Livraria Online
 */
$pageTitle = 'Catálogo';
require_once 'config/database.php';
require_once 'includes/header.php';

$db = getDB();

// Filtros
$categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
$subcategoryId = isset($_GET['subcategory']) ? (int) $_GET['subcategory'] : null;
$minPrice = isset($_GET['min_price']) ? (float) $_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) ? (float) $_GET['max_price'] : null;
$filter = $_GET['filter'] ?? null;
$sort = $_GET['sort'] ?? 'newest';

// Paginação
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Construir query
$where = [];
$params = [];

if ($categoryId) {
    $where[] = "b.category_id = ?";
    $params[] = $categoryId;
}

if ($subcategoryId) {
    $where[] = "b.subcategory_id = ?";
    $params[] = $subcategoryId;
}

if ($minPrice !== null) {
    $where[] = "b.price >= ?";
    $params[] = $minPrice;
}

if ($maxPrice !== null) {
    $where[] = "b.price <= ?";
    $params[] = $maxPrice;
}

if ($filter === 'featured') {
    $where[] = "b.featured = 1";
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Ordenação
$orderBy = match ($sort) {
    'price_asc' => 'b.price ASC',
    'price_desc' => 'b.price DESC',
    'title' => 'b.title ASC',
    'oldest' => 'b.created_at ASC',
    default => 'b.created_at DESC'
};

// Total de livros
$countQuery = "SELECT COUNT(*) FROM books b $whereClause";
$countStmt = $db->prepare($countQuery);
$countStmt->execute($params);
$totalBooks = $countStmt->fetchColumn();
$totalPages = ceil($totalBooks / $perPage);

// Obter livros
$query = "SELECT b.*, c.name as category_name, sc.name as subcategory_name 
          FROM books b 
          LEFT JOIN categories c ON b.category_id = c.id 
          LEFT JOIN subcategories sc ON b.subcategory_id = sc.id 
          $whereClause 
          ORDER BY $orderBy 
          LIMIT $perPage OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$books = $stmt->fetchAll();

// Obter categorias para filtros
$categories = $db->query("SELECT c.*, COUNT(b.id) as book_count 
                          FROM categories c 
                          LEFT JOIN books b ON c.id = b.category_id 
                          GROUP BY c.id 
                          ORDER BY c.name")->fetchAll();

// Obter subcategorias da categoria selecionada
$subcategories = [];
if ($categoryId) {
    $subStmt = $db->prepare("SELECT * FROM subcategories WHERE category_id = ? ORDER BY name");
    $subStmt->execute([$categoryId]);
    $subcategories = $subStmt->fetchAll();
}

// Nome da categoria atual
$currentCategory = null;
if ($categoryId) {
    $catStmt = $db->prepare("SELECT name FROM categories WHERE id = ?");
    $catStmt->execute([$categoryId]);
    $currentCategory = $catStmt->fetchColumn();
}
?>

<!-- Hero Section -->
<section class="bg-gradient-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="h2 fw-bold mb-1">
                    <?php if ($currentCategory): ?>
                        <?php echo htmlspecialchars($currentCategory); ?>
                    <?php elseif ($filter === 'featured'): ?>
                        Livros em Destaque
                    <?php else: ?>
                        Catálogo de Livros
                    <?php endif; ?>
                </h1>
                <p class="mb-0 opacity-75">
                    <?php echo $totalBooks; ?> livros encontrados
                </p>
            </div>
            <div class="col-lg-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-lg-end mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Início</a></li>
                        <li class="breadcrumb-item active text-white">Catálogo</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="section py-4">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar de Filtros -->
            <div class="col-lg-3">
                <div class="filter-sidebar">
                    <h5><i class="bi bi-funnel me-2"></i>Filtros</h5>

                    <form id="filterForm" action="books.php" method="GET">
                        <!-- Categorias -->
                        <div class="filter-group">
                            <h6 class="fw-bold">Categorias</h6>
                            <div class="list-group list-group-flush">
                                <a href="books.php"
                                    class="list-group-item list-group-item-action <?php echo !$categoryId ? 'active' : ''; ?>">
                                    Todas as Categorias
                                </a>
                                <?php foreach ($categories as $cat): ?>
                                    <a href="books.php?category=<?php echo $cat['id']; ?>"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $categoryId == $cat['id'] ? 'active' : ''; ?>">
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                        <span class="badge bg-primary rounded-pill">
                                            <?php echo $cat['book_count']; ?>
                                        </span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Subcategorias -->
                        <?php if (!empty($subcategories)): ?>
                            <div class="filter-group">
                                <h6 class="fw-bold">Subcategorias</h6>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($subcategories as $subcat): ?>
                                        <a href="books.php?category=<?php echo $categoryId; ?>&subcategory=<?php echo $subcat['id']; ?>"
                                            class="list-group-item list-group-item-action <?php echo $subcategoryId == $subcat['id'] ? 'active' : ''; ?>">
                                            <?php echo htmlspecialchars($subcat['name']); ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Preço -->
                        <div class="filter-group">
                            <h6 class="fw-bold">Preço</h6>
                            <div class="price-range">
                                <input type="number" class="form-control form-control-sm" name="min_price"
                                    placeholder="Min" value="<?php echo $minPrice; ?>" min="0" step="0.01">
                                <span>-</span>
                                <input type="number" class="form-control form-control-sm" name="max_price"
                                    placeholder="Max" value="<?php echo $maxPrice; ?>" min="0" step="0.01">
                            </div>
                            <?php if ($categoryId): ?>
                                <input type="hidden" name="category" value="<?php echo $categoryId; ?>">
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary btn-sm w-100 mt-2">
                                <i class="bi bi-filter me-1"></i>Aplicar
                            </button>
                        </div>

                        <!-- Limpar Filtros -->
                        <?php if ($categoryId || $minPrice || $maxPrice): ?>
                            <a href="books.php" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-x-circle me-1"></i>Limpar Filtros
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Lista de Livros -->
            <div class="col-lg-9">
                <!-- Toolbar -->
                <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-3 rounded shadow-sm">
                    <span class="text-muted">
                        Mostrando
                        <?php echo min($offset + 1, $totalBooks); ?>-
                        <?php echo min($offset + $perPage, $totalBooks); ?>
                        de
                        <?php echo $totalBooks; ?> livros
                    </span>

                    <div class="d-flex gap-2 align-items-center">
                        <label class="form-label mb-0 me-2">Ordenar:</label>
                        <select class="form-select form-select-sm" style="width: auto;"
                            onchange="window.location.href=this.value">
                            <option value="?sort=newest<?php echo $categoryId ? "&category=$categoryId" : ''; ?>"
                                <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Mais Recentes
                            </option>
                            <option value="?sort=price_asc<?php echo $categoryId ? "&category=$categoryId" : ''; ?>"
                                <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Preço: Menor → Maior
                            </option>
                            <option value="?sort=price_desc<?php echo $categoryId ? "&category=$categoryId" : ''; ?>"
                                <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Preço: Maior → Menor
                            </option>
                            <option value="?sort=title<?php echo $categoryId ? "&category=$categoryId" : ''; ?>"
                                <?php echo $sort === 'title' ? 'selected' : ''; ?>>Título A-Z
                            </option>
                        </select>
                    </div>
                </div>

                <?php if (empty($books)): ?>
                    <div class="text-center py-5">
                        <i class="bi bi-search fs-1 text-muted"></i>
                        <h4 class="mt-3">Nenhum livro encontrado</h4>
                        <p class="text-muted">Tente ajustar os filtros ou explore outras categorias.</p>
                        <a href="books.php" class="btn btn-primary">Ver Todos os Livros</a>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($books as $book): ?>
                            <div class="col-xl-4 col-md-6">
                                <article class="book-card">
                                    <div class="book-card-img">
                                        <?php if ($book['image'] && file_exists('assets/images/books/' . $book['image'])): ?>
                                            <img src="assets/images/books/<?php echo htmlspecialchars($book['image']); ?>"
                                                alt="<?php echo htmlspecialchars($book['title']); ?>">
                                        <?php else: ?>
                                            <i class="bi bi-book placeholder-icon"></i>
                                        <?php endif; ?>
                                        <?php if ($book['featured']): ?>
                                            <span class="badge-featured"><i class="bi bi-star-fill me-1"></i>Destaque</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="book-card-body">
                                        <h3 class="book-card-title">
                                            <?php echo htmlspecialchars($book['title']); ?>
                                        </h3>
                                        <p class="book-card-author">
                                            <i class="bi bi-person me-1"></i>
                                            <?php echo htmlspecialchars($book['author']); ?>
                                        </p>
                                        <p class="book-card-category">
                                            <i class="bi bi-tag me-1"></i>
                                            <?php echo htmlspecialchars($book['category_name'] ?? 'Sem categoria'); ?>
                                            <?php if ($book['subcategory_name']): ?>
                                                /
                                                <?php echo htmlspecialchars($book['subcategory_name']); ?>
                                            <?php endif; ?>
                                        </p>
                                        <div class="book-card-price">
                                            <?php echo formatPrice($book['price']); ?>
                                        </div>
                                        <div class="book-card-actions">
                                            <a href="book-details.php?id=<?php echo $book['id']; ?>"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> Ver
                                            </a>
                                            <?php if (isLoggedIn()): ?>
                                                <button class="btn btn-primary btn-sm add-to-cart"
                                                    data-book-id="<?php echo $book['id']; ?>">
                                                    <i class="bi bi-cart-plus"></i> Carrinho
                                                </button>
                                            <?php else: ?>
                                                <a href="login.php" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-cart-plus"></i> Carrinho
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Paginação -->
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-5" aria-label="Navegação de páginas">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo $categoryId ? "&category=$categoryId" : ''; ?>
                                <?php echo $sort !== 'newest' ? "&sort=$sort" : ''; ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>

                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <?php if ($i === 1 || $i === $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?><?php echo $categoryId ? "&category=$categoryId" : ''; ?>
                                <?php echo $sort !== 'newest' ? "&sort=$sort" : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php elseif ($i === $page - 3 || $i === $page + 3): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endfor; ?>

                                <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo $categoryId ? "&category=$categoryId" : ''; ?>
                                <?php echo $sort !== 'newest' ? "&sort=$sort" : ''; ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>