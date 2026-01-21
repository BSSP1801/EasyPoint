document.addEventListener("DOMContentLoaded", function() {
        const stickyHeader = document.querySelector('.sticky-header');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 400) {
                stickyHeader.classList.add('visible');
            } else {
                stickyHeader.classList.remove('visible');
            }
        });

        const modal = document.getElementById('auth-modal');
        const openBtns = document.querySelectorAll('.login-link, .sticky-login');
        const closeBtn = document.querySelector('.close-modal');
        const loginView = document.getElementById('login-view');
        const registerView = document.getElementById('register-view');
        const goToRegister = document.getElementById('go-to-register');
        const goToLogin = document.getElementById('go-to-login');

        openBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                modal.style.display = 'flex';
                loginView.classList.remove('hidden');
                registerView.classList.add('hidden');
            });
        });

        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });

        goToRegister.addEventListener('click', () => {
            loginView.classList.add('hidden');
            registerView.classList.remove('hidden');
        });

        goToLogin.addEventListener('click', () => {
            registerView.classList.add('hidden');
            loginView.classList.remove('hidden');
        });
    });