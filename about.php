<?php
/**
 * Página Sobre Nós - Livraria Online
 */
$pageTitle = 'Sobre Nós';
require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Sobre a Livraria Online</h1>
                <p class="lead mb-0">A sua livraria de confiança desde 2024. Conectamos leitores aos melhores livros do
                    mundo.</p>
            </div>
            <div class="col-lg-6 text-center">
                <i class="bi bi-book-half" style="font-size: 8rem; opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Nossa História -->
<section class="section">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <img src="assets/images/about-library.jpg" alt="Nossa Livraria" class="img-fluid rounded shadow"
                    onerror="this.src='https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=600'; this.onerror=null;">
            </div>
            <div class="col-lg-6">
                <h2 class="section-title">Nossa História</h2>
                <p class="lead text-muted">Nascemos do amor pelos livros e da vontade de torná-los acessíveis a todos.
                </p>
                <p>A Livraria Online foi fundada em 2024 por um grupo de apaixonados por literatura. O nosso objetivo
                    sempre foi claro: oferecer uma experiência de compra de livros simples, conveniente e acessível.</p>
                <p>Começámos com apenas algumas centenas de títulos e hoje temos um catálogo com milhares de livros de
                    todas as categorias. Desde ficção científica até literatura infantil, passando por livros técnicos e
                    obras de arte - temos algo para cada leitor.</p>
                <p>A nossa missão é democratizar o acesso à literatura, oferecendo preços justos, entrega rápida e um
                    serviço de excelência.</p>
            </div>
        </div>
    </div>
</section>

<!-- Nossos Valores -->
<section class="section bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Nossos Valores</h2>
            <p class="section-subtitle">O que nos move todos os dias</p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="category-card h-100">
                    <i class="bi bi-heart text-danger"></i>
                    <h5>Paixão pela Leitura</h5>
                    <p class="text-muted">Acreditamos no poder transformador dos livros. Cada página lida é uma nova
                        perspetiva adquirida.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="category-card h-100">
                    <i class="bi bi-people text-primary"></i>
                    <h5>Foco no Cliente</h5>
                    <p class="text-muted">Cada cliente é especial. Trabalhamos para oferecer a melhor experiência de
                        compra possível.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="category-card h-100">
                    <i class="bi bi-shield-check text-success"></i>
                    <h5>Confiança</h5>
                    <p class="text-muted">Segurança nas transações, transparência nos preços e honestidade em tudo o que
                        fazemos.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="category-card h-100">
                    <i class="bi bi-lightning text-warning"></i>
                    <h5>Agilidade</h5>
                    <p class="text-muted">Entrega rápida para que não tenha de esperar muito para começar a sua próxima
                        leitura.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="category-card h-100">
                    <i class="bi bi-globe text-info"></i>
                    <h5>Diversidade</h5>
                    <p class="text-muted">Um catálogo diversificado com livros de todas as culturas, géneros e
                        perspetivas.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="category-card h-100">
                    <i class="bi bi-award text-purple"></i>
                    <h5>Qualidade</h5>
                    <p class="text-muted">Selecionamos cuidadosamente cada título para garantir que só oferecemos o
                        melhor.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Números -->
<section class="section bg-gradient-primary text-white">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-lg-3 col-md-6">
                <div class="p-4">
                    <h2 class="display-4 fw-bold">5000+</h2>
                    <p class="mb-0 opacity-75">Títulos Disponíveis</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="p-4">
                    <h2 class="display-4 fw-bold">10K+</h2>
                    <p class="mb-0 opacity-75">Clientes Satisfeitos</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="p-4">
                    <h2 class="display-4 fw-bold">50+</h2>
                    <p class="mb-0 opacity-75">Categorias</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="p-4">
                    <h2 class="display-4 fw-bold">24/7</h2>
                    <p class="mb-0 opacity-75">Suporte ao Cliente</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Equipa -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Nossa Equipa</h2>
            <p class="section-subtitle">As pessoas por trás da Livraria Online</p>
        </div>

        <div class="row g-4 justify-content-center">
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 100px; height: 100px;">
                            <i class="bi bi-person fs-1"></i>
                        </div>
                        <h5 class="card-title">Maria Silva</h5>
                        <p class="text-muted small mb-2">Fundadora & CEO</p>
                        <p class="small">Apaixonada por livros desde pequena, Maria fundou a Livraria Online para
                            partilhar esse amor.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 100px; height: 100px;">
                            <i class="bi bi-person fs-1"></i>
                        </div>
                        <h5 class="card-title">João Santos</h5>
                        <p class="text-muted small mb-2">Diretor de Operações</p>
                        <p class="small">Garante que cada encomenda chegue ao destino no prazo e em perfeitas condições.
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="rounded-circle bg-warning text-white d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 100px; height: 100px;">
                            <i class="bi bi-person fs-1"></i>
                        </div>
                        <h5 class="card-title">Ana Costa</h5>
                        <p class="text-muted small mb-2">Curadora de Conteúdo</p>
                        <p class="small">Seleciona cuidadosamente cada título que entra no nosso catálogo.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="section bg-light">
    <div class="container text-center">
        <h3 class="mb-3">Pronto para encontrar o seu próximo livro?</h3>
        <p class="text-muted mb-4">Explore o nosso catálogo com milhares de títulos esperando por si.</p>
        <a href="books.php" class="btn btn-primary btn-lg me-2">
            <i class="bi bi-collection me-2"></i>Ver Catálogo
        </a>
        <a href="contact.php" class="btn btn-outline-primary btn-lg">
            <i class="bi bi-envelope me-2"></i>Contacte-nos
        </a>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>