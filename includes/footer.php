</main>

<!-- Footer -->
<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="fw-bold mb-3">
                    <i class="bi bi-book-half me-2"></i>Livraria Online
                </h5>
                <p class="text-muted">
                    A sua livraria online de confiança. Encontre os melhores livros ao melhor preço,
                    com entrega rápida em todo o país.
                </p>
                <div class="social-icons">
                    <a href="#" class="text-light me-3"><i class="bi bi-facebook fs-5"></i></a>
                    <a href="#" class="text-light me-3"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="#" class="text-light me-3"><i class="bi bi-twitter-x fs-5"></i></a>
                    <a href="#" class="text-light"><i class="bi bi-linkedin fs-5"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-6">
                <h6 class="fw-bold mb-3">Navegação</h6>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-muted text-decoration-none">Início</a></li>
                    <li class="mb-2"><a href="books.php" class="text-muted text-decoration-none">Catálogo</a></li>
                    <li class="mb-2"><a href="about.php" class="text-muted text-decoration-none">Sobre Nós</a></li>
                    <li class="mb-2"><a href="contact.php" class="text-muted text-decoration-none">Contacto</a></li>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-3">Categorias</h6>
                <ul class="list-unstyled">
                    <?php
                    $footerCategories = getCategories();
                    foreach (array_slice($footerCategories, 0, 5) as $cat):
                        ?>
                        <li class="mb-2">
                            <a href="books.php?category=<?php echo $cat['id']; ?>" class="text-muted text-decoration-none">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-lg-3 col-md-6">
                <h6 class="fw-bold mb-3">Contacto</h6>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2">
                        <i class="bi bi-geo-alt me-2"></i>Rua dos Livros, 123<br>
                        <span class="ms-4">1000-001 Lisboa</span>
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-telephone me-2"></i>+351 21 123 4567
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-envelope me-2"></i>info@livraria.com
                    </li>
                    <li>
                        <i class="bi bi-clock me-2"></i>Seg-Sex: 9h-18h
                    </li>
                </ul>
            </div>
        </div>

        <hr class="my-4 border-secondary">

        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-muted mb-0">
                    &copy;
                    <?php echo date('Y'); ?> Livraria Online. Todos os direitos reservados.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <img src="assets/images/payment-methods.png" alt="Métodos de Pagamento" class="payment-icons"
                    style="max-height: 30px;">
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="assets/js/main.js"></script>
</body>

</html>