<?php
/**
 * Meu Perfil - Livraria Online
 */
$pageTitle = 'Meu Perfil';
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$db = getDB();
$userId = $_SESSION['user_id'];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    if (empty($name)) {
        $errors[] = 'O nome é obrigatório.';
    }

    if (empty($errors)) {
        $stmt = $db->prepare("UPDATE users SET name = ?, phone = ?, address = ? WHERE id = ?");
        $stmt->execute([$name, $phone, $address, $userId]);
        $_SESSION['user_name'] = $name;

        if (!empty($currentPassword) && !empty($newPassword)) {
            $userStmt = $db->prepare("SELECT password FROM users WHERE id = ?");
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch();

            if (password_verify($currentPassword, $user['password'])) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $db->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hashedPassword, $userId]);
            } else {
                $errors[] = 'Password atual incorreta.';
            }
        }

        if (empty($errors)) {
            setFlashMessage('Perfil atualizado com sucesso!', 'success');
            redirect('profile.php');
        }
    }
}

$userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch();

require_once 'includes/header.php';
?>

<section class="bg-gradient-primary text-white py-4">
    <div class="container">
        <h1 class="h2 fw-bold mb-1"><i class="bi bi-person-circle me-2"></i>Meu Perfil</h1>
    </div>
</section>

<section class="section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php echo implode('<br>', $errors); ?>
                    </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST">
                            <h5 class="mb-4"><i class="bi bi-person me-2"></i>Dados Pessoais</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Nome *</label>
                                    <input type="text" class="form-control" name="name"
                                        value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control"
                                        value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Telefone</label>
                                    <input type="tel" class="form-control" name="phone"
                                        value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Morada</label>
                                    <textarea class="form-control" name="address"
                                        rows="2"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <h5 class="mb-4"><i class="bi bi-lock me-2"></i>Alterar Password</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Password Atual</label>
                                    <input type="password" class="form-control" name="current_password">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Nova Password</label>
                                    <input type="password" class="form-control" name="new_password">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary"><i class="bi bi-check me-2"></i>Guardar
                                Alterações</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>