<?php
/**
 * Header do Painel Admin - Livraria Online
 */
if (!function_exists('isLoggedIn')) {
    require_once __DIR__ . '/../../includes/functions.php';
}
?>
<!DOCTYPE html>
<html lang="pt-PT">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Admin - Livraria Online
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f6f9;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #2c3e50 0%, #1a252f 100%);
            min-height: 100vh;
            position: fixed;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.7);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 4px 12px;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            margin-left: 280px;
            padding: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table-card .card-header {
            background: white;
            border-bottom: 2px solid #f4f6f9;
            padding: 20px;
        }

        @media (max-width: 991px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="p-4">
                <a href="../index.php" class="text-white text-decoration-none d-flex align-items-center mb-4">
                    <i class="bi bi-book-half fs-4 me-2"></i>
                    <span class="fs-5 fw-bold">Livraria Admin</span>
                </a>
            </div>
            <nav class="nav flex-column">
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"
                    href="index.php">
                    <i class="bi bi-speedometer2"></i>Dashboard
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>"
                    href="categories.php">
                    <i class="bi bi-folder"></i>Categorias
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'subcategories.php' ? 'active' : ''; ?>"
                    href="subcategories.php">
                    <i class="bi bi-folder2"></i>Subcategorias
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'books.php' ? 'active' : ''; ?>"
                    href="books.php">
                    <i class="bi bi-book"></i>Livros
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>"
                    href="orders.php">
                    <i class="bi bi-bag"></i>Encomendas
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>"
                    href="users.php">
                    <i class="bi bi-people"></i>Utilizadores
                </a>
                <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>"
                    href="messages.php">
                    <i class="bi bi-envelope"></i>Mensagens
                </a>
                <hr class="text-white-50 mx-3">
                <a class="nav-link" href="../index.php"><i class="bi bi-house"></i>Ver Loja</a>
                <a class="nav-link text-danger" href="../logout.php"><i class="bi bi-box-arrow-right"></i>Sair</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content flex-grow-1">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <?php echo $pageTitle ?? 'Dashboard'; ?>
                </h1>
                <div class="d-flex align-items-center">
                    <span class="me-3 text-muted small">
                        <?php echo date('d M Y, H:i'); ?>
                    </span>
                    <span class="badge bg-primary">
                        <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                    </span>
                </div>
            </div>
            <?php echo getFlashMessage(); ?>