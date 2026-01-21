<?php
/**
 * Página de Registo - Livraria Online
 */
$pageTitle = 'Criar Conta';
require_once 'config/database.php';
require_once 'includes/functions.php';

// Redirecionar se já estiver logado
if (isLoggedIn()) {
    redirect('index.php');
}

$errors = [];
$success = false;

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validações
    if (empty($name)) {
        $errors[] = 'O nome é obrigatório.';
    }

    if (empty($email)) {
        $errors[] = 'O email é obrigatório.';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Por favor, insira um email válido.';
    }

    if (empty($password)) {
        $errors[] = 'A password é obrigatória.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'A password deve ter pelo menos 6 caracteres.';
    }

    if ($password !== $confirmPassword) {
        $errors[] = 'As passwords não coincidem.';
    }

    // Verificar se email já existe
    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = 'Este email já está registado.';
        }
    }

    // Criar utilizador
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (name, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, 'user')");

        try {
            $stmt->execute([$name, $email, $hashedPassword, $phone, $address]);
            $success = true;
            setFlashMessage('Conta criada com sucesso! Faça login para continuar.', 'success');
            redirect('login.php');
        } catch (PDOException $e) {
            $errors[] = 'Erro ao criar conta. Por favor, tente novamente.';
        }
    }
}

require_once 'includes/header.php';
?>

<section class="auth-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="auth-card">
                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill fs-1 text-primary"></i>
                        <h2 class="mt-3">Criar Conta</h2>
                        <p class="subtitle">Registe-se para começar a comprar</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li>
                                        <?php echo $error; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome Completo *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="name" name="name"
                                    value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Telefone</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                    value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Morada</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                <textarea class="form-control" id="address" name="address"
                                    rows="2"><?php echo htmlspecialchars($address ?? ''); ?></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password"
                                        minlength="6" required>
                                </div>
                                <small class="text-muted">Mínimo 6 caracteres</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Password *</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="form-control" id="confirm_password"
                                        name="confirm_password" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" required>
                                <label class="form-check-label" for="terms">
                                    Aceito os <a href="#">Termos e Condições</a>
                                </label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <i class="bi bi-person-plus me-2"></i>Criar Conta
                        </button>
                    </form>

                    <hr class="my-4">

                    <p class="text-center mb-0">
                        Já tem conta? <a href="login.php" class="fw-bold">Faça Login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>