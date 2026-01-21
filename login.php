<?php
/**
 * Página de Login - Livraria Online
 */
$pageTitle = 'Entrar';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirecionar se já estiver logado
if (isLoggedIn()) {
    redirect('index.php');
}

$errors = [];

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validações
    if (empty($email)) {
        $errors[] = 'O email é obrigatório.';
    }
    
    if (empty($password)) {
        $errors[] = 'A password é obrigatória.';
    }
    
    // Verificar credenciais
    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login bem-sucedido
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Cookie de "lembrar-me" (30 dias)
            if ($remember) {
                setcookie('user_email', $email, time() + (30 * 24 * 60 * 60), '/');
            }
            
            setFlashMessage('Bem-vindo(a), ' . $user['name'] . '!', 'success');
            
            // Redirecionar para admin se for admin
            if ($user['role'] === 'admin') {
                redirect('admin/index.php');
            } else {
                redirect('index.php');
            }
        } else {
            $errors[] = 'Email ou password incorretos.';
        }
    }
}

// Obter email do cookie se existir
$rememberedEmail = $_COOKIE['user_email'] ?? '';

require_once 'includes/header.php';
?>

<section class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-box-arrow-in-right fs-1 text-primary"></i>
                        <h2 class="mt-3">Entrar</h2>
                        <p class="subtitle">Aceda à sua conta</p>
                    </div>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($rememberedEmail ?: ($email ?? '')); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        
                        <div class="mb-4 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember"
                                       <?php echo $rememberedEmail ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="remember">
                                    Lembrar-me
                                </label>
                            </div>
                            <a href="#" class="text-decoration-none small">Esqueceu a password?</a>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                        </button>
                    </form>
                    
                    <hr class="my-4">
                    
                    <p class="text-center mb-0">
                        Não tem conta? <a href="register.php" class="fw-bold">Registe-se</a>
                    </p>
                    
                    <!-- Demo Login Info -->
                    <div class="mt-4 p-3 bg-light rounded">
                        <small class="text-muted">
                            <strong>Demo Admin:</strong><br>
                            Email: admin@livraria.com<br>
                            Password: admin123
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
