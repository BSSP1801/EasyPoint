document.addEventListener('DOMContentLoaded', () => {

    // ==========================================
    // 1. STICKY HEADER & CARRUSEL
    // ==========================================
    const stickyHeader = document.querySelector('.sticky-header');
    if (stickyHeader) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                stickyHeader.classList.add('visible');
            } else {
                stickyHeader.classList.remove('visible');
            }
        });
    }
});