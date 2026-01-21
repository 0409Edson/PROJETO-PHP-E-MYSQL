<?php
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Livraria Online - Encontre os melhores livros ao melhor preço">
    <title>
        <?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Livraria Online
    </title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-book-half me-2"></i>Livraria Online
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="bi bi-house me-1"></i>Início</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="books.php"><i class="bi bi-collection me-1"></i>Catálogo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php"><i class="bi bi-info-circle me-1"></i>Sobre Nós</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.php"><i class="bi bi-envelope me-1"></i>Contacto</a>
                    </li>
                </ul>

                <!-- Search Form -->
                <form class="d-flex me-3" action="search.php" method="GET">
                    <div class="input-group">
                        <input class="form-control" type="search" name="q" placeholder="Pesquisar livros..."
                            aria-label="Pesquisar">
                        <button class="btn btn-light" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <!-- User Menu -->
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="cart.php">
                                <i class="bi bi-cart3"></i> Carrinho
                                <?php $cartCount = getCartCount();
                                if ($cartCount > 0): ?>
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <?php echo $cartCount; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (isAdmin()): ?>
                                    <li><a class="dropdown-item" href="admin/"><i class="bi bi-speedometer2 me-2"></i>Painel
                                            Admin</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="orders.php"><i class="bi bi-bag me-2"></i>Minhas
                                        Encomendas</a></li>
                                <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i>Meu
                                        Perfil</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i
                                            class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right me-1"></i>Entrar</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-outline-light btn-sm ms-2" href="register.php">Registar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="container mt-3">
        <?php echo getFlashMessage(); ?>
    </div>

    <!-- Main Content -->
    <main>