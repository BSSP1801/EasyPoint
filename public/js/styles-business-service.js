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

    // ==========================================
    // 2. REFERENCIAS DOM
    // ==========================================
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

    // ==========================================
    // 3. APERTURA DE MODALES
    // ==========================================

    window.openStoreModal = function(e) {
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

    // ==========================================
    // 4. CIERRE DE MODALES (CORREGIDO CON MOUSEUP)
    // ==========================================
    
    const modals = [authModal, storeModal];

    modals.forEach(modal => {
        if (!modal) return;

        let mouseStartedOnOverlay = false;

        // A. Detectar dónde se PRESIONA el botón
        modal.addEventListener('mousedown', (e) => {
            if (e.target === modal) {
                mouseStartedOnOverlay = true;
            } else {
                mouseStartedOnOverlay = false;
            }
        });

        // B. Detectar dónde se SUELTA el botón (USAR MOUSEUP, NO CLICK)
        modal.addEventListener('mouseup', (e) => {
            // Solo cerramos si:
            // 1. Se soltó en el fondo (e.target === modal)
            // 2. Y ADEMÁS se había presionado inicialmente en el fondo
            if (e.target === modal && mouseStartedOnOverlay) {
                modal.style.display = 'none';
            }
            // Reiniciamos la variable
            mouseStartedOnOverlay = false;
        });
    });

    document.querySelectorAll('.close-modal, .close-store-modal').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const modal = e.target.closest('.modal-overlay');
            if (modal) modal.style.display = 'none';
        });
    });

    // ==========================================
    // 5. SWITCH LOGIN / REGISTER
    // ==========================================
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

    // ==========================================
    // 6. ENVÍO DE FORMULARIOS (AJAX/FETCH)
    // ==========================================

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('../index.php?action=login', {
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
                    window.location.reload();
                } else {
                    if(loginError) {
                        loginError.textContent = data.message;
                        loginError.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                if(loginError) {
                    loginError.textContent = 'An error occurred. Please try again.';
                    loginError.style.display = 'block';
                }
            });
        });
    }

    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if(registerError) registerError.style.display = 'none';
            if(registerSuccess) registerSuccess.style.display = 'none';
            
            const formData = new FormData(this);
            formData.append('role', 'user');
            
            fetch('../index.php?action=register', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                if (!response.ok && response.status !== 400) throw new Error('Err: ' + response.status);
                return response.text().then(text => {
                    try { return JSON.parse(text); } 
                    catch (e) { throw new Error('Invalid JSON'); }
                });
            })
            .then(data => {
                if (data.success) {
                    if(registerSuccess) {
                        registerSuccess.textContent = 'Redirecting...';
                        registerSuccess.style.display = 'block';
                    }
                    setTimeout(() => { window.location.href = 'index.php'; }, 1000);
                } else {
                    if(registerError) {
                        registerError.textContent = 'Error: ' + data.message;
                        registerError.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                if(registerError) {
                    registerError.textContent = 'Error: ' + error.message;
                    registerError.style.display = 'block';
                }
            });
        });
    }

    if (storeRegisterForm) {
        storeRegisterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if(storeError) storeError.style.display = 'none';
            if(storeSuccess) storeSuccess.style.display = 'none';
            
            const formData = new FormData(this);
            formData.append('role', 'store');
            
            fetch('../index.php?action=register', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => {
                if (!response.ok && response.status !== 400) throw new Error('Err: ' + response.status);
                return response.text().then(text => {
                    try { return JSON.parse(text); } 
                    catch (e) { throw new Error('Invalid JSON'); }
                });
            })
            .then(data => {
                if (data.success) {
                    if(storeSuccess) {
                        storeSuccess.textContent = 'Redirecting...';
                        storeSuccess.style.display = 'block';
                    }
                    setTimeout(() => { window.location.href = 'index.php'; }, 1000);
                } else {
                    if(storeError) {
                        storeError.textContent = 'Error: ' + data.message;
                        storeError.style.display = 'block';
                    }
                }
            })
            .catch(error => {
                if(storeError) {
                    storeError.textContent = 'Error: ' + error.message;
                    storeError.style.display = 'block';
                }
            });
        });
    }
});



