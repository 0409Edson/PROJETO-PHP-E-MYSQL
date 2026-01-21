<?php
/**
 * Carrinho de Compras - Livraria Online
 */
$pageTitle = 'Carrinho de Compras';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Verificar se está logado
if (!isLoggedIn()) {
    setFlashMessage('Por favor, faça login para ver o seu carrinho.', 'warning');
    redirect('login.php');
}

$db = getDB();
$userId = $_SESSION['user_id'];

// Processar ações do carrinho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update':
                $cartId = (int)$_POST['cart_id'];
                $quantity = max(1, (int)$_POST['quantity']);
                
                // Verificar stock
                $stockStmt = $db->prepare("SELECT b.stock FROM cart c 
                                           JOIN books b ON c.book_id = b.id 
                                           WHERE c.id = ? AND c.user_id = ?");
                $stockStmt->execute([$cartId, $userId]);
                $stock = $stockStmt->fetchColumn();
                
                if ($quantity <= $stock) {
                    $updateStmt = $db->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                    $updateStmt->execute([$quantity, $cartId, $userId]);
                    setFlashMessage('Quantidade atualizada.', 'success');
                } else {
                    setFlashMessage('Quantidade indisponível em stock.', 'danger');
                }
                break;
                
            case 'remove':
                $cartId = (int)$_POST['cart_id'];
                $deleteStmt = $db->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
                $deleteStmt->execute([$cartId, $userId]);
                setFlashMessage('Item removido do carrinho.', 'success');
                break;
                
            case 'clear':
                $clearStmt = $db->prepare("DELETE FROM cart WHERE user_id = ?");
                $clearStmt->execute([$userId]);
                setFlashMessage('Carrinho limpo.', 'success');
                break;
        }
        redirect('cart.php');
    }
}

// Obter itens do carrinho
$cartStmt = $db->prepare("SELECT c.*, b.title, b.author, b.price, b.image, b.stock 
                          FROM cart c 
                          JOIN books b ON c.book_id = b.id 
                          WHERE c.user_id = ? 
                          ORDER BY c.created_at DESC");
$cartStmt->execute([$userId]);
$cartItems = $cartStmt->fetchAll();

// Calcular totais
$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

$shipping = $subtotal >= 25 ? 0 : 3.99; // Portes grátis acima de 25€
$total = $subtotal + $shipping;

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="bg-gradient-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="h2 fw-bold mb-1"><i class="bi bi-cart3 me-2"></i>Carrinho de Compras</h1>
                <p class="mb-0 opacity-75"><?php echo count($cartItems); ?> item(s) no carrinho</p>
            </div>
            <div class="col-lg-4">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-lg-end mb-0">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white-50">Início</a></li>
                        <li class="breadcrumb-item active text-white">Carrinho</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<section class="section py-5">
    <div class="container">
        <?php if (empty($cartItems)): ?>
            <div class="text-center py-5">
                <i class="bi bi-cart-x fs-1 text-muted" style="font-size: 5rem !important;"></i>
                <h3 class="mt-4">O seu carrinho está vazio</h3>
                <p class="text-muted mb-4">Adicione alguns livros ao carrinho para continuar.</p>
                <a href="books.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-collection me-2"></i>Explorar Catálogo
                </a>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <!-- Lista de Itens -->
                <div class="col-lg-8">
                    <div class="cart-table">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Livro</th>
                                        <th class="text-center">Preço</th>
                                        <th class="text-center">Quantidade</th>
                                        <th class="text-center">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($item['image'] && file_exists('assets/images/books/' . $item['image'])): ?>
                                                    <img src="assets/images/books/<?php echo htmlspecialchars($item['image']); ?>" 
                                                         alt="<?php echo htmlspecialchars($item['title']); ?>" class="me-3">
                                                <?php else: ?>
                                                    <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                                                         style="width: 80px; height: 100px; border-radius: 8px;">
                                                        <i class="bi bi-book text-muted fs-2"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-1">
                                                        <a href="book-details.php?id=<?php echo $item['book_id']; ?>" class="text-dark text-decoration-none">
                                                            <?php echo htmlspecialchars($item['title']); ?>
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted"><?php echo htmlspecialchars($item['author']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center align-middle">
                                            <?php echo formatPrice($item['price']); ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                <div class="input-group input-group-sm" style="width: 100px; margin: 0 auto;">
                                                    <input type="number" class="form-control text-center" name="quantity" 
                                                           value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>"
                                                           onchange="this.form.submit()">
                                                </div>
                                            </form>
                                        </td>
                                        <td class="text-center align-middle fw-bold text-success">
                                            <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                        </td>
                                        <td class="text-center align-middle">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="remove">
                                                <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" 
                                                        onclick="return confirm('Remover este item?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="books.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Continuar a Comprar
                        </a>
                        <form method="POST">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Limpar todo o carrinho?')">
                                <i class="bi bi-trash me-2"></i>Limpar Carrinho
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Resumo -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4><i class="bi bi-receipt me-2"></i>Resumo</h4>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span><?php echo formatPrice($subtotal); ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Portes de Envio:</span>
                            <span>
                                <?php if ($shipping === 0): ?>
                                    <span class="text-success">Grátis</span>
                                <?php else: ?>
                                    <?php echo formatPrice($shipping); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                        
                        <?php if ($subtotal < 25): ?>
                        <div class="alert alert-info small py-2 mb-3">
                            <i class="bi bi-info-circle me-1"></i>
                            Faltam <?php echo formatPrice(25 - $subtotal); ?> para portes grátis!
                        </div>
                        <?php endif; ?>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold fs-5">Total:</span>
                            <span class="total"><?php echo formatPrice($total); ?></span>
                        </div>
                        
                        <a href="checkout.php" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-credit-card me-2"></i>Finalizar Compra
                        </a>
                        
                        <div class="text-center mt-4">
                            <p class="small text-muted mb-2">Pagamento Seguro</p>
                            <i class="bi bi-shield-check text-success fs-4"></i>
                        </div>
                        
                        <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="mb-2"><i class="bi bi-truck me-2"></i>Informações de Entrega</h6>
                            <ul class="small text-muted mb-0">
                                <li>Entrega em 2-3 dias úteis</li>
                                <li>Portes grátis acima de 25€</li>
                                <li>Pagamento na entrega</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
