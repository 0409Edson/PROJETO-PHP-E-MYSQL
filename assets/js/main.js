/**
 * Main JavaScript
 * Livraria Online - Funções JavaScript Globais
 */

document.addEventListener('DOMContentLoaded', function () {

    // Inicializar tooltips do Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Adicionar ao carrinho via AJAX
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const bookId = this.dataset.bookId;
            const quantity = document.querySelector('.quantity-input')?.value || 1;

            addToCart(bookId, quantity);
        });
    });

    // Atualizar quantidade no carrinho
    const quantityInputs = document.querySelectorAll('.cart-quantity');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function () {
            const cartId = this.dataset.cartId;
            const quantity = this.value;

            updateCartQuantity(cartId, quantity);
        });
    });

    // Remover item do carrinho
    const removeButtons = document.querySelectorAll('.remove-from-cart');
    removeButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const cartId = this.dataset.cartId;

            if (confirm('Tem a certeza que deseja remover este item?')) {
                removeFromCart(cartId);
            }
        });
    });

    // Scroll suave para âncoras
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Animação de fade-in ao scroll
    const fadeElements = document.querySelectorAll('.fade-on-scroll');
    const fadeObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    }, { threshold: 0.1 });

    fadeElements.forEach(el => fadeObserver.observe(el));

});

/**
 * Adicionar livro ao carrinho
 */
function addToCart(bookId, quantity = 1) {
    fetch('ajax/add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `book_id=${bookId}&quantity=${quantity}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Livro adicionado ao carrinho!', 'success');
                updateCartBadge(data.cartCount);
            } else {
                showNotification(data.message || 'Erro ao adicionar ao carrinho', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro de conexão', 'error');
        });
}

/**
 * Atualizar quantidade no carrinho
 */
function updateCartQuantity(cartId, quantity) {
    fetch('ajax/update-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `cart_id=${cartId}&quantity=${quantity}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Recarregar para atualizar totais
            } else {
                showNotification(data.message || 'Erro ao atualizar', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro de conexão', 'error');
        });
}

/**
 * Remover item do carrinho
 */
function removeFromCart(cartId) {
    fetch('ajax/remove-from-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `cart_id=${cartId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showNotification(data.message || 'Erro ao remover', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showNotification('Erro de conexão', 'error');
        });
}

/**
 * Atualizar badge do carrinho no navbar
 */
function updateCartBadge(count) {
    const badge = document.querySelector('.cart-badge');
    if (badge) {
        badge.textContent = count;
        badge.style.display = count > 0 ? 'inline' : 'none';
    }
}

/**
 * Mostrar notificação temporária
 */
function showNotification(message, type = 'info') {
    const bgColor = {
        'success': '#27ae60',
        'error': '#e74c3c',
        'warning': '#f39c12',
        'info': '#3498db'
    };

    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        padding: 15px 25px;
        background: ${bgColor[type]};
        color: white;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 9999;
        animation: slideIn 0.3s ease;
        font-weight: 500;
    `;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Adicionar estilos de animação
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

/**
 * Validação de formulários
 */
function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');

    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });

    // Validar email
    const emailInputs = form.querySelectorAll('input[type="email"]');
    emailInputs.forEach(input => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (input.value && !emailRegex.test(input.value)) {
            input.classList.add('is-invalid');
            isValid = false;
        }
    });

    return isValid;
}

/**
 * Formatar preço
 */
function formatPrice(price) {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price);
}

/**
 * Debounce para pesquisa
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Pesquisa em tempo real (debounced)
const searchInput = document.querySelector('.search-input');
if (searchInput) {
    searchInput.addEventListener('input', debounce(function (e) {
        const query = e.target.value;
        if (query.length >= 2) {
            // Implementar pesquisa em tempo real se necessário
            console.log('Pesquisar:', query);
        }
    }, 300));
}
