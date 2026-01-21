<?php
/**
 * Homepage - Livraria Online
 */
$pageTitle = 'Início';
require_once 'config/database.php';
require_once 'includes/header.php';

// Obter livros em destaque
$db = getDB();
$featuredBooks = $db->query("SELECT b.*, c.name as category_name 
                             FROM books b 
                             LEFT JOIN categories c ON b.category_id = c.id 
                             WHERE b.featured = 1 
                             ORDER BY b.created_at DESC 
                             LIMIT 8")->fetchAll();

// Obter categorias
$categories = $db->query("SELECT c.*, COUNT(b.id) as book_count 
                          FROM categories c 
                          LEFT JOIN books b ON c.id = b.category_id 
                          GROUP BY c.id 
                          ORDER BY c.name")->fetchAll();

// Obter novos lançamentos
$newBooks = $db->query("SELECT b.*, c.name as category_name 
                        FROM books b 
                        LEFT JOIN categories c ON b.category_id = c.id 
                        ORDER BY b.created_at DESC 
                        LIMIT 4")->fetchAll();
?>

<!-- Hero Slider -->
<section class="hero-slider">
    <div class="slider-container">
        <!-- Slide 1 -->
        <div class="slide active" style="background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);">
            <div class="slide-content">
                <h2>Bem-vindo à Livraria Online</h2>
                <p>Descubra milhares de livros ao melhor preço. Entrega em todo o país!</p>
                <a href="books.php" class="btn btn-light btn-lg">
                    <i class="bi bi-book me-2"></i>Ver Catálogo
                </a>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="slide" style="background: linear-gradient(135deg, #8e44ad 0%, #3498db 100%);">
            <div class="slide-content">
                <h2>Novidades Literárias</h2>
                <p>Os lançamentos mais esperados do ano já disponíveis!</p>
                <a href="books.php?filter=new" class="btn btn-warning btn-lg">
                    <i class="bi bi-stars me-2"></i>Ver Novidades
                </a>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="slide" style="background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);">
            <div class="slide-content">
                <h2>Pagamento na Entrega</h2>
                <p>Compre com segurança e pague apenas quando receber!</p>
                <a href="about.php" class="btn btn-light btn-lg">
                    <i class="bi bi-shield-check me-2"></i>Saiba Mais
                </a>
            </div>
        </div>
    </div>

    <!-- Slider Controls -->
    <button class="slider-arrow prev"><i class="bi bi-chevron-left"></i></button>
    <button class="slider-arrow next"><i class="bi bi-chevron-right"></i></button>

    <div class="slider-controls">
        <span class="slider-dot active"></span>
        <span class="slider-dot"></span>
        <span class="slider-dot"></span>
    </div>
</section>

<!-- Slider JS -->
<script src="assets/js/slider.js"></script>

<!-- Categorias -->
<section class="section bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Explore por Categoria</h2>
            <p class="section-subtitle">Encontre o livro perfeito para si</p>
        </div>

        <div class="row g-4">
            <?php
            $categoryIcons = [
                'Ficção' => 'bi-book',
                'Não-Ficção' => 'bi-journal-text',
                'Infantil' => 'bi-emoji-smile',
                'Técnico' => 'bi-cpu',
                'Arte e Fotografia' => 'bi-palette'
            ];

            foreach ($categories as $category):
                $icon = $categoryIcons[$category['name']] ?? 'bi-bookmark';
                ?>
                <div class="col-lg-4 col-md-6">
                    <a href="books.php?category=<?php echo $category['id']; ?>" class="text-decoration-none">
                        <div class="category-card">
                            <i class="bi <?php echo $icon; ?>"></i>
                            <h5>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </h5>
                            <p>
                                <?php echo $category['book_count']; ?> livros disponíveis
                            </p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Livros em Destaque -->
<section class="section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-0">Livros em Destaque</h2>
                <p class="section-subtitle mb-0">Os mais populares da nossa loja</p>
            </div>
            <a href="books.php?filter=featured" class="btn btn-outline-primary">
                Ver Todos <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($featuredBooks as $book): ?>
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <article class="book-card">
                        <div class="book-card-img">
                            <?php if ($book['image'] && file_exists('assets/images/books/' . $book['image'])): ?>
                                <img src="assets/images/books/<?php echo htmlspecialchars($book['image']); ?>"
                                    alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <?php else: ?>
                                <i class="bi bi-book placeholder-icon"></i>
                            <?php endif; ?>
                            <span class="badge-featured"><i class="bi bi-star-fill me-1"></i>Destaque</span>
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
    </div>
</section>

<!-- Banner Promocional -->
<section class="py-5 bg-gradient-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="fw-bold mb-2">Registe-se e receba 10% de desconto!</h3>
                <p class="mb-lg-0">Crie a sua conta gratuita e aproveite benefícios exclusivos.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="register.php" class="btn btn-light btn-lg">
                    <i class="bi bi-person-plus me-2"></i>Criar Conta
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Novos Lançamentos -->
<section class="section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-0">Novos Lançamentos</h2>
                <p class="section-subtitle mb-0">As últimas adições ao nosso catálogo</p>
            </div>
            <a href="books.php?filter=new" class="btn btn-outline-primary">
                Ver Todos <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        <div class="row g-4">
            <?php foreach ($newBooks as $book): ?>
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
                            <p class="book-card-author">
                                <i class="bi bi-person me-1"></i>
                                <?php echo htmlspecialchars($book['author']); ?>
                            </p>
                            <p class="book-card-category">
                                <i class="bi bi-tag me-1"></i>
                                <?php echo htmlspecialchars($book['category_name'] ?? 'Sem categoria'); ?>
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
    </div>
</section>

<!-- Vantagens -->
<section class="section bg-white">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-lg-3 col-md-6">
                <div class="p-4">
                    <i class="bi bi-truck fs-1 text-primary mb-3"></i>
                    <h5 class="fw-bold">Entrega Rápida</h5>
                    <p class="text-muted mb-0">Entrega em todo o país em 24-48 horas</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="p-4">
                    <i class="bi bi-credit-card fs-1 text-primary mb-3"></i>
                    <h5 class="fw-bold">Pagamento na Entrega</h5>
                    <p class="text-muted mb-0">Pague apenas quando receber o seu livro</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="p-4">
                    <i class="bi bi-arrow-return-left fs-1 text-primary mb-3"></i>
                    <h5 class="fw-bold">Devolução Fácil</h5>
                    <p class="text-muted mb-0">14 dias para devoluções gratuitas</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="p-4">
                    <i class="bi bi-headset fs-1 text-primary mb-3"></i>
                    <h5 class="fw-bold">Suporte 24/7</h5>
                    <p class="text-muted mb-0">Estamos sempre disponíveis para ajudar</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>