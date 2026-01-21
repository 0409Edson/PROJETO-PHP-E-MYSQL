<?php
/**
 * Página de Contacto - Livraria Online
 */
$pageTitle = 'Contacto';
require_once 'config/database.php';
require_once 'includes/functions.php';

$errors = [];
$success = false;

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    // Validações
    if (empty($name)) {
        $errors[] = 'O nome é obrigatório.';
    }

    if (empty($email)) {
        $errors[] = 'O email é obrigatório.';
    } elseif (!isValidEmail($email)) {
        $errors[] = 'Por favor, insira um email válido.';
    }

    if (empty($message)) {
        $errors[] = 'A mensagem é obrigatória.';
    }

    // Guardar mensagem
    if (empty($errors)) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO contacts (name, email, subject, message) VALUES (?, ?, ?, ?)");

        try {
            $stmt->execute([$name, $email, $subject, $message]);
            $success = true;
            setFlashMessage('Mensagem enviada com sucesso! Entraremos em contacto em breve.', 'success');

            // Limpar campos
            $name = $email = $subject = $message = '';
        } catch (PDOException $e) {
            $errors[] = 'Erro ao enviar mensagem. Por favor, tente novamente.';
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<section class="bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Entre em Contacto</h1>
                <p class="lead mb-0">Estamos aqui para ajudar. Envie-nos a sua mensagem e responderemos o mais rápido
                    possível.</p>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <i class="bi bi-envelope-paper" style="font-size: 6rem; opacity: 0.7;"></i>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5">
            <!-- Formulário de Contacto -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h3 class="mb-4">Envie-nos uma Mensagem</h3>

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

                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                Mensagem enviada com sucesso! Entraremos em contacto em breve.
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Nome *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="subject" class="form-label">Assunto</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-chat-left-text"></i></span>
                                        <input type="text" class="form-control" id="subject" name="subject"
                                            value="<?php echo htmlspecialchars($subject ?? ''); ?>">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="message" class="form-label">Mensagem *</label>
                                    <textarea class="form-control" id="message" name="message" rows="5"
                                        required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-send me-2"></i>Enviar Mensagem
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Informações de Contacto -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Informações de Contacto</h5>

                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-geo-alt fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Morada</h6>
                                <p class="text-muted mb-0">Rua dos Livros, 123<br>1000-001 Lisboa, Portugal</p>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-telephone fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Telefone</h6>
                                <p class="text-muted mb-0">+351 21 123 4567<br>+351 91 234 5678 (WhatsApp)</p>
                            </div>
                        </div>

                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-envelope fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Email</h6>
                                <p class="text-muted mb-0">info@livraria.com<br>suporte@livraria.com</p>
                            </div>
                        </div>

                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 50px; height: 50px;">
                                    <i class="bi bi-clock fs-5"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">Horário de Atendimento</h6>
                                <p class="text-muted mb-0">Segunda a Sexta: 9h - 18h<br>Sábado: 10h - 14h</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Redes Sociais -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-3">Siga-nos</h5>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline-primary rounded-circle"
                                style="width: 45px; height: 45px;">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="btn btn-outline-danger rounded-circle"
                                style="width: 45px; height: 45px;">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-outline-dark rounded-circle" style="width: 45px; height: 45px;">
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary rounded-circle"
                                style="width: 45px; height: 45px;">
                                <i class="bi bi-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mapa (placeholder) -->
<section class="mb-0">
    <div class="container-fluid px-0">
        <div class="bg-secondary" style="height: 400px;">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3113.2234567890!2d-9.1393!3d38.7223!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzjCsDQzJzIwLjMiTiA5wrAwOCcyMS41Ilc!5e0!3m2!1spt-PT!2spt!4v123456789"
                width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>