document.addEventListener('DOMContentLoaded', () => {

    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }

    function showToast(title, message, iconClass = 'fa-check-circle') {
        const toastHTML = `
            <div class="toast toast-easypoint align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-3">
                        <i class="fas ${iconClass} fa-lg" style="color: var(--accent);"></i>
                        <div>
                            <div class="toast-title-text">${title}</div>
                            <div class="toast-body-text">${message}</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = toastHTML.trim();
        const toastElement = tempDiv.firstChild;

        toastContainer.appendChild(toastElement);

        const bsToast = new bootstrap.Toast(toastElement, {
            animation: true,
            autohide: true,
            delay: 4000
        });

        bsToast.show();

        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    window.showToast = showToast;

    const pendingToast = localStorage.getItem('easyPointToast');
    if (pendingToast) {
        const { title, message, icon } = JSON.parse(pendingToast);
        showToast(title, message, icon);
        localStorage.removeItem('easyPointToast');
    }

    document.body.addEventListener('click', function (e) {
        const link = e.target.closest('a');
        if (link && link.href.includes('action=logout')) {
            localStorage.setItem('easyPointToast', JSON.stringify({
                title: 'See you soon',
                message: 'You have logged out successfully',
                icon: 'fa-sign-out-alt'
            }));
        }
    });

    const stickyHeader = document.querySelector('.sticky-header');
    if (stickyHeader) {
        window.addEventListener('scroll', function () {
            if (window.scrollY > 400) {
                stickyHeader.classList.add('visible');
            } else {
                stickyHeader.classList.remove('visible');
            }
        });
    }

    const carousel = document.querySelector('.shops-grid');
    const leftBtn = document.querySelector('.left-arrow');
    const rightBtn = document.querySelector('.right-arrow');

    if (carousel && leftBtn && rightBtn) {
        rightBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: 320, behavior: 'smooth' });
        });
        leftBtn.addEventListener('click', () => {
            carousel.scrollBy({ left: -320, behavior: 'smooth' });
        });
    }

    const authModal = document.getElementById('auth-modal');
    const storeModal = document.getElementById('store-modal');

    const loginView = document.getElementById('login-view');
    const registerView = document.getElementById('register-view');

    const goToRegister = document.getElementById('go-to-register');
    const goToLogin = document.getElementById('go-to-login');

    const loginForm = document.getElementById('login-form');
    const loginError = document.getElementById('login-error');

    const registerForm = document.getElementById('register-form');
    const registerError = document.getElementById('register-error');
    const registerSuccess = document.getElementById('register-success');

    const storeRegisterForm = document.getElementById('store-register-form');
    const storeError = document.getElementById('store-error');
    const storeSuccess = document.getElementById('store-success');

    window.openStoreModal = function (e) {
        if (e) e.preventDefault();
        if (storeModal) storeModal.style.display = 'flex';
    };

    function openAuthModal(e) {
        if (e) e.preventDefault();
        if (authModal) {
            authModal.style.display = 'flex';
            if (loginView) {
                loginView.classList.remove('hidden');
                loginView.style.display = 'block';
            }
            if (registerView) {
                registerView.classList.add('hidden');
                registerView.style.display = 'none';
            }
        }
    }

    document.querySelectorAll('.login-link, .sticky-login').forEach(btn => {
        btn.addEventListener('click', openAuthModal);
    });

    const modals = [authModal, storeModal];

    modals.forEach(modal => {
        if (!modal) return;

        let mouseStartedOnOverlay = false;

        modal.addEventListener('mousedown', (e) => {
            if (e.target === modal) {
                mouseStartedOnOverlay = true;
            } else {
                mouseStartedOnOverlay = false;
            }
        });

        modal.addEventListener('mouseup', (e) => {
            if (e.target === modal && mouseStartedOnOverlay) {
                modal.style.display = 'none';
            }
            mouseStartedOnOverlay = false;
        });
    });

    document.querySelectorAll('.close-modal, .close-store-modal').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const modal = e.target.closest('.modal-overlay');
            if (modal) modal.style.display = 'none';
        });
    });

    if (goToRegister) {
        goToRegister.addEventListener('click', () => {
            if (loginView) {
                loginView.classList.add('hidden');
                loginView.style.display = 'none';
            }
            if (registerView) {
                registerView.classList.remove('hidden');
                registerView.style.display = 'block';
            }
        });
    }

    if (goToLogin) {
        goToLogin.addEventListener('click', () => {
            if (registerView) {
                registerView.classList.add('hidden');
                registerView.style.display = 'none';
            }
            if (loginView) {
                loginView.classList.remove('hidden');
                loginView.style.display = 'block';
            }
        });
    }

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('index.php?action=login', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => {
                    if (!response.ok && response.status !== 401) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        localStorage.setItem('easyPointToast', JSON.stringify({
                            title: 'Welcome back',
                            message: 'Login successful',
                            icon: 'fa-check-circle'
                        }));
                        // Check if there's a return URL stored (for booking page)
                        const returnUrl = sessionStorage.getItem('returnUrl');
                        sessionStorage.removeItem('returnUrl');
                        window.location.href = returnUrl || 'index.php';
                    } else {
                        if (loginError) {
                            loginError.textContent = data.message;
                            loginError.style.display = 'block';
                        }
                    }
                })
                .catch(error => {
                    if (loginError) {
                        loginError.textContent = 'An error occurred. Please try again.';
                        loginError.style.display = 'block';
                    }
                });
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (registerError) registerError.style.display = 'none';
            if (registerSuccess) registerSuccess.style.display = 'none';
            
            // Limpiar estilos de error previos
            registerForm.querySelectorAll('.modal-input').forEach(input => {
                input.style.boxShadow = '';
            });

            const formData = new FormData(this);
            formData.append('role', 'user');

            fetch('index.php?action=register', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => {
                    return response.text().then(text => {
                        try { return JSON.parse(text); }
                        catch (e) { 
                            console.error("Raw error:", text);
                            throw new Error('Server returned invalid data.'); 
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        localStorage.setItem('easyPointToast', JSON.stringify({
                            title: 'Account Created',
                            message: 'Welcome to EasyPoint!',
                            icon: 'fa-user-plus'
                        }));
                        setTimeout(() => { window.location.href = 'index.php'; }, 1000);
                    } else {
                        if (registerError) {
                            registerError.textContent = data.message;
                            registerError.style.display = 'block';
                        }
                        // Marcar campo con error y hacer Focus
                        if (data.field) {
                            const fieldEl = registerForm.querySelector(`[name="${data.field}"]`);
                            if (fieldEl) {
                                fieldEl.style.boxShadow = 'inset 0 0 0 2px #f44336';
                                fieldEl.focus();
                                fieldEl.addEventListener('input', function() {
                                    this.style.boxShadow = ''; // Quitar rojo al escribir
                                }, { once: true });
                            }
                        }
                    }
                })
                .catch(error => {
                    if (registerError) {
                        registerError.textContent = error.message;
                        registerError.style.display = 'block';
                    }
                });
        });
    }

    if (storeRegisterForm) {
        storeRegisterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (storeError) storeError.style.display = 'none';
            if (storeSuccess) storeSuccess.style.display = 'none';
            
            // Limpiar estilos de error previos
            storeRegisterForm.querySelectorAll('.modal-input').forEach(input => {
                input.style.boxShadow = '';
            });

            const formData = new FormData(this);
            formData.append('role', 'store');

            fetch('index.php?action=register', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => {
                    return response.text().then(text => {
                        try { return JSON.parse(text); }
                        catch (e) { 
                            console.error("Raw error:", text);
                            throw new Error('Server returned invalid data.'); 
                        }
                    });
                })
                .then(data => {
                    if (data.success) {
                        if (storeSuccess) {
                            storeSuccess.textContent = 'Redirecting...';
                            storeSuccess.style.display = 'block';
                        }
                        setTimeout(() => { window.location.href = 'index.php'; }, 1000);
                    } else {
                        if (storeError) {
                            storeError.textContent = data.message;
                            storeError.style.display = 'block';
                        }
                        // Marcar campo con error y hacer Focus
                        if (data.field) {
                            const fieldEl = storeRegisterForm.querySelector(`[name="${data.field}"]`);
                            if (fieldEl) {
                                fieldEl.style.boxShadow = 'inset 0 0 0 2px #f44336';
                                fieldEl.focus();
                                fieldEl.addEventListener('input', function() {
                                    this.style.boxShadow = ''; // Quitar rojo al escribir
                                }, { once: true });
                            }
                        }
                    }
                })
                .catch(error => {
                    if (storeError) {
                        storeError.textContent = error.message;
                        storeError.style.display = 'block';
                    }
                });
        });
    }
});