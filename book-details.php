<?php
/**
 * Detalhes do Livro - Livraria Online
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

$db = getDB();

// Obter ID do livro
$bookId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if (!$bookId) {
    redirect('books.php');
}

// Obter livro
$stmt = $db->prepare("SELECT b.*, c.name as category_name, sc.name as subcategory_name 
                      FROM books b 
                      LEFT JOIN categories c ON b.category_id = c.id 
                      LEFT JOIN subcategories sc ON b.subcategory_id = sc.id 
                      WHERE b.id = ?");
$stmt->execute([$bookId]);
$book = $stmt->fetch();

if (!$book) {
    setFlashMessage('Livro não encontrado.', 'danger');
    redirect('books.php');
}

$pageTitle = $book['title'];

// Obter livros relacionados (mesma categoria)
$relatedStmt = $db->prepare("SELECT b.*, c.name as category_name 
                             FROM books b 
                             LEFT JOIN categories c ON b.category_id = c.id 
                             WHERE b.category_id = ? AND b.id != ? 
                             ORDER BY RAND() 
                             LIMIT 4");
$relatedStmt->execute([$book['category_id'], $bookId]);
$relatedBooks = $relatedStmt->fetchAll();

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<section class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="index.php">Início</a></li>
                <li class="breadcrumb-item"><a href="books.php">Catálogo</a></li>
                <?php if ($book['category_name']): ?>
                    <li class="breadcrumb-item">
                        <a href="books.php?category=<?php echo $book['category_id']; ?>">
                            <?php echo htmlspecialchars($book['category_name']); ?>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="breadcrumb-item active">
                    <?php echo htmlspecialchars($book['title']); ?>
                </li>
            </ol>
        </nav>
    </div>
</section>

<section class="section py-5">
    <div class="container">
        <div class="book-details">
            <div class="row g-5">
                <!-- Imagem do Livro -->
                <div class="col-lg-5">
                    <div class="book-details-img">
                        <?php if ($book['image'] && file_exists('assets/images/books/' . $book['image'])): ?>
                            <img src="assets/images/books/<?php echo htmlspecialchars($book['image']); ?>"
                                alt="<?php echo htmlspecialchars($book['title']); ?>">
                        <?php else: ?>
                            <i class="bi bi-book placeholder-icon"></i>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Informações do Livro -->
                <div class="col-lg-7">
                    <?php if ($book['featured']): ?>
                        <span class="badge bg-warning text-dark mb-3">
                            <i class="bi bi-star-fill me-1"></i>Livro em Destaque
                        </span>
                    <?php endif; ?>

                    <h1>
                        <?php echo htmlspecialchars($book['title']); ?>
                    </h1>

                    <p class="author fs-5">
                        <i class="bi bi-person me-2"></i>
                        <span class="text-muted">Por</span>
                        <strong>
                            <?php echo htmlspecialchars($book['author']); ?>
                        </strong>
                    </p>

                    <div class="d-flex gap-2 mb-4">
                        <?php if ($book['category_name']): ?>
                            <a href="books.php?category=<?php echo $book['category_id']; ?>"
                                class="badge bg-primary text-decoration-none">
                                <?php echo htmlspecialchars($book['category_name']); ?>
                            </a>
                        <?php endif; ?>
                        <?php if ($book['subcategory_name']): ?>
                            <a href="books.php?category=<?php echo $book['category_id']; ?>&subcategory=<?php echo $book['subcategory_id']; ?>"
                                class="badge bg-secondary text-decoration-none">
                                <?php echo htmlspecialchars($book['subcategory_name']); ?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="price mb-4">
                        <?php echo formatPrice($book['price']); ?>
                    </div>

                    <div class="description mb-4">
                        <h5 class="mb-3">Descrição</h5>
                        <p>
                            <?php echo nl2br(htmlspecialchars($book['description'] ?? 'Sem descrição disponível.')); ?>
                        </p>
                    </div>

                    <!-- Stock -->
                    <div class="mb-4">
                        <?php if ($book['stock'] > 0): ?>
                            <span class="text-success fw-bold">
                                <i class="bi bi-check-circle me-1"></i>Em stock (
                                <?php echo $book['stock']; ?> disponíveis)
                            </span>
                        <?php else: ?>
                            <span class="text-danger fw-bold">
                                <i class="bi bi-x-circle me-1"></i>Esgotado
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Adicionar ao Carrinho -->
                    <?php if ($book['stock'] > 0): ?>
                        <div class="d-flex gap-3 align-items-center mb-4">
                            <div class="input-group" style="width: 130px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="decreaseQty()">-</button>
                                <input type="number" class="form-control text-center quantity-input" id="quantity" value="1"
                                    min="1" max="<?php echo $book['stock']; ?>">
                                <button class="btn btn-outline-secondary" type="button"
                                    onclick="increaseQty(<?php echo $book['stock']; ?>)">+</button>
                            </div>

                            <?php if (isLoggedIn()): ?>
                                <button class="btn btn-primary btn-lg add-to-cart" data-book-id="<?php echo $book['id']; ?>">
                                    <i class="bi bi-cart-plus me-2"></i>Adicionar ao Carrinho
                                </button>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Entrar para Comprar
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Informações Adicionais -->
                    <div class="border-top pt-4 mt-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-truck text-primary fs-4 me-3"></i>
                                    <div>
                                        <strong>Entrega Rápida</strong>
                                        <p class="text-muted small mb-0">2-3 dias úteis</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-credit-card text-primary fs-4 me-3"></i>
                                    <div>
                                        <strong>Pagamento na Entrega</strong>
                                        <p class="text-muted small mb-0">Pague quando receber</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-arrow-return-left text-primary fs-4 me-3"></i>
                                    <div>
                                        <strong>Devoluções</strong>
                                        <p class="text-muted small mb-0">14 dias para devolver</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-shield-check text-primary fs-4 me-3"></i>
                                    <div>
                                        <strong>Compra Segura</strong>
                                        <p class="text-muted small mb-0">Dados protegidos</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Livros Relacionados -->
<?php if (!empty($relatedBooks)): ?>
    <section class="section bg-light py-5">
        <div class="container">
            <h3 class="section-title mb-4">Livros Relacionados</h3>

            <div class="row g-4">
                <?php foreach ($relatedBooks as $related): ?>
                    <div class="col-xl-3 col-md-6">
                        <article class="book-card">
                            <div class="book-card-img">
                                <?php if ($related['image'] && file_exists('assets/images/books/' . $related['image'])): ?>
                                    <img src="assets/images/books/<?php echo htmlspecialchars($related['image']); ?>"
                                        alt="<?php echo htmlspecialchars($related['title']); ?>">
                                <?php else: ?>
                                    <i class="bi bi-book placeholder-icon"></i>
                                <?php endif; ?>
                            </div>
                            <div class="book-card-body">
                                <h3 class="book-card-title">
                                    <?php echo htmlspecialchars($related['title']); ?>
                                </h3>
                                <p class="book-card-author">
                                    <i class="bi bi-person me-1"></i>
                                    <?php echo htmlspecialchars($related['author']); ?>
                                </p>
                                <div class="book-card-price">
                                    <?php echo formatPrice($related['price']); ?>
                                </div>
                                <div class="book-card-actions">
                                    <a href="book-details.php?id=<?php echo $related['id']; ?>"
                                        class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye"></i> Ver
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<script>
    function decreaseQty() {
        const input = document.getElementById('quantity');
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    function increaseQty(max) {
        const input = document.getElementById('quantity');
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>