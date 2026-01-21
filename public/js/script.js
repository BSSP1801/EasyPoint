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
        const storeModal = document.getElementById('store-modal');
        const openBtns = document.querySelectorAll('.login-link, .sticky-login');
        const closeBtn = document.querySelector('.close-modal');
        const closeStoreBtn = document.querySelector('.close-store-modal');
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

        closeStoreBtn.addEventListener('click', () => {
            storeModal.style.display = 'none';
        });

        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
            if (e.target === storeModal) {
                storeModal.style.display = 'none';
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

        // Handle login form submission
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                fetch('index.php?action=login', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    if (!response.ok && response.status !== 401) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        window.location.href = 'index.php';
                    } else {
                        loginError.textContent = data.message;
                        loginError.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loginError.textContent = 'An error occurred. Please try again.';
                    loginError.style.display = 'block';
                });
            });
        }

        // Handle user registration form submission
        if (registerForm) {
            registerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                registerError.style.display = 'none';
                registerSuccess.style.display = 'none';
                
                const formData = new FormData(this);
                formData.append('role', 'user'); // Always register as user in this modal
                
                fetch('index.php?action=register', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Register response status:', response.status);
                    if (!response.ok && response.status !== 400) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.text().then(text => {
                        console.log('Register response text:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Failed to parse JSON:', text);
                            throw new Error('Invalid response format: ' + text);
                        }
                    });
                })
                .then(data => {
                    console.log('Register parsed data:', data);
                    if (data.success) {
                        registerSuccess.textContent = 'Redirecting...';
                        registerSuccess.style.display = 'block';
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 1000);
                    } else {
                        registerError.textContent = 'Error: ' + data.message;
                        registerError.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Register Error:', error);
                    registerError.textContent = 'Error: ' + error.message;
                    registerError.style.display = 'block';
                });
            });
        }

        // Handle store registration form submission
        if (storeRegisterForm) {
            storeRegisterForm.addEventListener('submit', function(e) {
                e.preventDefault();
                storeError.style.display = 'none';
                storeSuccess.style.display = 'none';
                
                const formData = new FormData(this);
                formData.append('role', 'store'); // Always register as store
                
                fetch('index.php?action=register', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Store register response status:', response.status);
                    if (!response.ok && response.status !== 400) {
                        throw new Error('Network response was not ok: ' + response.status);
                    }
                    return response.text().then(text => {
                        console.log('Store register response text:', text);
                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            console.error('Failed to parse JSON:', text);
                            throw new Error('Invalid response format: ' + text);
                        }
                    });
                })
                .then(data => {
                    console.log('Store register parsed data:', data);
                    if (data.success) {
                        storeSuccess.textContent = 'Redirecting...';
                        storeSuccess.style.display = 'block';
                        setTimeout(() => {
                            window.location.href = 'index.php';
                        }, 1000);
                    } else {
                        storeError.textContent = 'Error: ' + data.message;
                        storeError.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Store register Error:', error);
                  
                });
            });
        }
    });

// Global function to open store registration modal
function openStoreModal(e) {
    e.preventDefault();
    const storeModal = document.getElementById('store-modal');
    storeModal.style.display = 'flex';
}

