/**
 * Slider JavaScript
 * Livraria Online - Slider Automático para Homepage
 */

class HeroSlider {
    constructor(container) {
        this.container = container;
        this.slides = container.querySelectorAll('.slide');
        this.dots = container.querySelectorAll('.slider-dot');
        this.prevBtn = container.querySelector('.slider-arrow.prev');
        this.nextBtn = container.querySelector('.slider-arrow.next');
        this.currentSlide = 0;
        this.totalSlides = this.slides.length;
        this.autoPlayInterval = null;
        this.autoPlayDelay = 5000; // 5 segundos
        
        this.init();
    }
    
    init() {
        // Mostrar primeiro slide
        this.showSlide(0);
        
        // Event listeners para setas
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => this.prevSlide());
        }
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => this.nextSlide());
        }
        
        // Event listeners para dots
        this.dots.forEach((dot, index) => {
            dot.addEventListener('click', () => this.goToSlide(index));
        });
        
        // Auto-play
        this.startAutoPlay();
        
        // Pausar auto-play ao passar o rato por cima
        this.container.addEventListener('mouseenter', () => this.stopAutoPlay());
        this.container.addEventListener('mouseleave', () => this.startAutoPlay());
        
        // Suporte a teclas do teclado
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') this.prevSlide();
            if (e.key === 'ArrowRight') this.nextSlide();
        });
        
        // Suporte a swipe em dispositivos móveis
        this.setupTouchEvents();
    }
    
    showSlide(index) {
        // Remover classe active de todos os slides
        this.slides.forEach(slide => slide.classList.remove('active'));
        this.dots.forEach(dot => dot.classList.remove('active'));
        
        // Adicionar classe active ao slide atual
        this.slides[index].classList.add('active');
        if (this.dots[index]) {
            this.dots[index].classList.add('active');
        }
        
        this.currentSlide = index;
    }
    
    nextSlide() {
        const next = (this.currentSlide + 1) % this.totalSlides;
        this.showSlide(next);
    }
    
    prevSlide() {
        const prev = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        this.showSlide(prev);
    }
    
    goToSlide(index) {
        this.showSlide(index);
    }
    
    startAutoPlay() {
        this.stopAutoPlay();
        this.autoPlayInterval = setInterval(() => this.nextSlide(), this.autoPlayDelay);
    }
    
    stopAutoPlay() {
        if (this.autoPlayInterval) {
            clearInterval(this.autoPlayInterval);
            this.autoPlayInterval = null;
        }
    }
    
    setupTouchEvents() {
        let startX = 0;
        let endX = 0;
        
        this.container.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
        }, { passive: true });
        
        this.container.addEventListener('touchend', (e) => {
            endX = e.changedTouches[0].clientX;
            this.handleSwipe(startX, endX);
        }, { passive: true });
    }
    
    handleSwipe(startX, endX) {
        const threshold = 50; // Distância mínima para considerar swipe
        const diff = startX - endX;
        
        if (Math.abs(diff) > threshold) {
            if (diff > 0) {
                this.nextSlide(); // Swipe para a esquerda
            } else {
                this.prevSlide(); // Swipe para a direita
            }
        }
    }
}

// Inicializar slider quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    const sliderContainer = document.querySelector('.hero-slider');
    if (sliderContainer) {
        new HeroSlider(sliderContainer);
    }
});
