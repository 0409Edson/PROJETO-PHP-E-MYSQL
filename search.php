<?php
/**
 * Pesquisa de Livros - Livraria Online
 */
$pageTitle = 'Pesquisa';
require_once 'config/database.php';
require_once 'includes/header.php';

$db = getDB();
$query = sanitize($_GET['q'] ?? '');
$books = [];

if (!empty($query)) {
    $searchTerm = "%$query%";
    $stmt = $db->prepare("SELECT b.*, c.name as category_name 
                          FROM books b 
                          LEFT JOIN categories c ON b.category_id = c.id 
                          WHERE b.title LIKE ? OR b.author LIKE ? OR b.description LIKE ?
                          ORDER BY b.title");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $books = $stmt->fetchAll();
}
?>

<section class="bg-gradient-primary text-white py-4">
    <div class="container">
        <h1 class="h2 fw-bold mb-1"><i class="bi bi-search me-2"></i>Pesquisa</h1>
        <p class="mb-0 opacity-75">
            <?php echo count($books); ?> resultado(s) para "
            <?php echo htmlspecialchars($query); ?>"
        </p>
    </div>
</section>

<section class="section py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-lg-6 mx-auto">
                <form action="search.php" method="GET">
                    <div class="input-group input-group-lg">
                        <input type="search" class="form-control" name="q"
                            value="<?php echo htmlspecialchars($query); ?>" placeholder="Pesquisar livros...">
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <?php if (empty($query)): ?>
            <div class="text-center py-5">
                <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Pesquise por título, autor ou descrição</h4>
            </div>
        <?php elseif (empty($books)): ?>
            <div class="text-center py-5">
                <i class="bi bi-emoji-frown text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Nenhum resultado encontrado</h4>
                <p class="text-muted">Tente pesquisar por outros termos.</p>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($books as $book): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6">
                        <article class="book-card">
                            <div class="book-card-img">
                                <?php if ($book['image'] && file_exists('assets/images/books/' . $book['image'])): ?>
                                    <img src="assets/images/books/<?php echo htmlspecialchars($book['image']); ?>"
                                        alt="<?php echo htmlspecialchars($book['title']); ?>">
                                <?php else: ?>
                                    <i class="bi bi-book placeholder-icon"></i>
                                <?php endif; ?>
                            </div>
                            <div class="book-card-body">
                                <h3 class="book-card-title">
                                    <?php echo htmlspecialchars($book['title']); ?>
                                </h3>
                                <p class="book-card-author"><i class="bi bi-person me-1"></i>
                                    <?php echo htmlspecialchars($book['author']); ?>
                                </p>
                                <div class="book-card-price">
                                    <?php echo formatPrice($book['price']); ?>
                                </div>
                                <div class="book-card-actions">
                                    <a href="book-details.php?id=<?php echo $book['id']; ?>"
                                        class="btn btn-outline-primary btn-sm">Ver</a>
                                    <?php if (isLoggedIn()): ?>
                                        <button class="btn btn-primary btn-sm add-to-cart"
                                            data-book-id="<?php echo $book['id']; ?>">Carrinho</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>