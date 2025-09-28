// Carousel functionality
let currentSlide = 0;
let carouselInterval;

// Initialize carousel when page loads
document.addEventListener('DOMContentLoaded', function() {
    initCarousel();
});

function initCarousel() {
    const carouselWrapper = document.getElementById('carousel-wrapper');
    if (!carouselWrapper) return;
    
    const slides = carouselWrapper.querySelectorAll('.carousel-slide');
    if (slides.length <= 1) return;
    
    startCarouselRotation();
}

function nextSlide() {
    const carouselWrapper = document.getElementById('carousel-wrapper');
    const slides = carouselWrapper.querySelectorAll('.carousel-slide');
    const indicators = document.querySelectorAll('.carousel-dot');
    
    currentSlide = (currentSlide + 1) % slides.length;
    updateCarousel();
}

function prevSlide() {
    const carouselWrapper = document.getElementById('carousel-wrapper');
    const slides = carouselWrapper.querySelectorAll('.carousel-slide');
    
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    updateCarousel();
}

function goToSlide(index) {
    currentSlide = index;
    updateCarousel();
}

function updateCarousel() {
    const carouselWrapper = document.getElementById('carousel-wrapper');
    const indicators = document.querySelectorAll('.carousel-dot');
    
    if (!carouselWrapper) return;
    
    carouselWrapper.style.transform = `translateX(-${currentSlide * 100}%)`;
    
    indicators.forEach((dot, index) => {
        dot.classList.toggle('active', index === currentSlide);
    });
    
    startCarouselRotation();
}

function startCarouselRotation() {
    clearInterval(carouselInterval);
    carouselInterval = setInterval(() => {
        nextSlide();
    }, 5000); // Auto-advance every 5 seconds
}

// Newsletter subscription
function subscribeNewsletter() {
    const emailInput = document.getElementById('newsletter-email');
    const email = emailInput.value.trim();
    
    if (email && email.includes('@')) {
        alert('Welcome to the network, operative. Your secure briefings will begin shortly.');
        emailInput.value = '';
    } else {
        alert('Please enter a valid email address for secure communications.');
    }
}

// Smooth scrolling for better UX
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling to all anchor links
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Add fade-in animation to blog cards
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    const blogCards = document.querySelectorAll('.blog-card, .archive-card');
    blogCards.forEach(card => {
        observer.observe(card);
    });
});