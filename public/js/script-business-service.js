function showToast(message) {
    const toast = document.getElementById("toast");
    // Add a warning icon next to the message
    toast.innerHTML = `<i class="fa-solid fa-circle-exclamation" style="color: #a58668; font-size: 1.2em;"></i> <span>${message}</span>`;
    
    // Add the class to show it
    toast.classList.add("show");

    // Remove it after 3 seconds
    setTimeout(function() {
        toast.classList.remove("show");
    }, 3000);
}

document.addEventListener('DOMContentLoaded', () => {

    const stars = document.querySelectorAll('.star-rating i');
    const ratingInput = document.getElementById('rating-value');

    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            ratingInput.value = rating;
            
            stars.forEach(s => {
                if(s.getAttribute('data-rating') <= rating) {
                    s.classList.replace('far', 'fas');
                } else {
                    s.classList.replace('fas', 'far');
                }
            });
        });
    });

    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(reviewForm);
            
            if (formData.get('rating') === "0") {
                showToast("Please, select a rating."); // Use the toast
                return;
            }

            try {
                const response = await fetch('../index.php?action=add_review', {
                    method: 'POST',
                    body: JSON.stringify(Object.fromEntries(formData)),
                    headers: { 'Content-Type': 'application/json' }
                });

                const result = await response.json();
                if (result.success) {
                    location.reload(); 
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }

    // 1. Create the Bootstrap toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
    // Bootstrap classes to position it at the top-right
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(toastContainer);
    }

    // 2. Function to show a toast using Bootstrap
    function showToast(title, message, iconClass = 'fa-check-circle') {

    // HTML structure required by Bootstrap
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

    // Convert string to DOM element
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = toastHTML.trim();
        const toastElement = tempDiv.firstChild;

    // Append to the container
        toastContainer.appendChild(toastElement);

    // Initialize with the Bootstrap API
        const bsToast = new bootstrap.Toast(toastElement, {
            animation: true,
            autohide: true,
            delay: 4000
        });

        bsToast.show();

        // Remove from DOM when hidden to avoid accumulating garbage
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }

    // 3. Check LocalStorage on load (for successful Login/Register)
    const pendingToast = localStorage.getItem('easyPointToast');
    if (pendingToast) {
        const { title, message, icon } = JSON.parse(pendingToast);
        showToast(title, message, icon);
        localStorage.removeItem('easyPointToast'); // Clear so it doesn't reappear on reload
    }

    // 4. Detectar Logout (Interceptar clics en enlaces de logout)
    document.body.addEventListener('click', function (e) {
        // Detect if the click happened inside a link with 'action=logout'
        const link = e.target.closest('a');
        if (link && link.href.includes('action=logout')) {
            // Save the message to show on the destination page (Home)
            localStorage.setItem('easyPointToast', JSON.stringify({
                title: 'See you soon',
                message: 'You have logged out successfully',
                icon: 'fa-sign-out-alt'
            }));
        }
    });

    // ==========================================
    // 1. STICKY HEADER & CARRUSEL
    // ==========================================
    const stickyHeader = document.querySelector('.sticky-header');
    if (stickyHeader) {
        window.addEventListener('scroll', function () {
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

    // ==========================================
    // 4. CIERRE DE MODALES (CORREGIDO CON MOUSEUP)
    // ==========================================

    const modals = [authModal, storeModal];

    modals.forEach(modal => {
        if (!modal) return;

        let mouseStartedOnOverlay = false;

        // A. Detect where the mouse is PRESSED down
        modal.addEventListener('mousedown', (e) => {
            if (e.target === modal) {
                mouseStartedOnOverlay = true;
            } else {
                mouseStartedOnOverlay = false;
            }
        });

        // B. Detect where the mouse is RELEASED (USE MOUSEUP, NOT CLICK)
        modal.addEventListener('mouseup', (e) => {
            // Only close if:
            // 1. It was released on the overlay (e.target === modal)
            // 2. And it was initially pressed on the overlay
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
        loginForm.addEventListener('submit', function (e) {
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
                        localStorage.setItem('easyPointToast', JSON.stringify({
                            title: 'Welcome back',
                            message: 'Login successful',
                            icon: 'fa-check-circle'
                        }));
                        window.location.href = 'index.php';
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
                        localStorage.setItem('easyPointToast', JSON.stringify({
                            title: 'Account Created',
                            message: 'Welcome to EasyPoint!',
                            icon: 'fa-user-plus'
                        }));
                        setTimeout(() => { window.location.href = 'index.php'; }, 1000);
                    } else {
                        if (registerError) {
                            registerError.textContent = 'Error: ' + data.message;
                            registerError.style.display = 'block';
                        }
                    }
                })
                .catch(error => {
                    if (registerError) {
                        registerError.textContent = 'Error: ' + error.message;
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
                        if (storeSuccess) {
                            storeSuccess.textContent = 'Redirecting...';
                            storeSuccess.style.display = 'block';
                        }
                        setTimeout(() => { window.location.href = 'index.php'; }, 1000);
                    } else {
                        if (storeError) {
                            storeError.textContent = 'Error: ' + data.message;
                            storeError.style.display = 'block';
                        }
                    }
                })
                .catch(error => {
                    if (storeError) {
                        storeError.textContent = 'Error: ' + error.message;
                        storeError.style.display = 'block';
                    }
                });
        });
    }



    // ==========================================
    // 7. CAROUSEL LOGIC
    // ==========================================

    const track = document.getElementById('carouselTrack');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const dotsContainer = document.getElementById('carouselDots');

    if (track && btnPrev && btnNext) {

        // --- Functions ---

        // Function to scroll the carousel left or right
        const moveCarousel = (direction) => {
            const width = track.offsetWidth;
            track.scrollBy({
                left: direction * width,
                behavior: 'smooth'
            });
            // We update dots after a short delay to match scroll end
            setTimeout(updateDots, 300);
        };

        // Function to jump to a specific slide
        const currentSlide = (index) => {
            const width = track.offsetWidth;
            track.scrollTo({
                left: index * width,
                behavior: 'smooth'
            });
            setTimeout(updateDots, 300);
        };

        // Function to update active dot based on scroll position
        const updateDots = () => {
            // Calculate current index based on scroll position
            const index = Math.round(track.scrollLeft / track.offsetWidth);
            const dots = document.querySelectorAll('.dot');

            dots.forEach((dot, i) => {
                if (i === index) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        };

        // --- Initialization ---

        // Generate dots dynamically based on number of slides
        const slides = track.querySelectorAll('.carousel-slide');
        if (dotsContainer && slides.length > 0) {
            dotsContainer.innerHTML = ''; // Clear existing
            slides.forEach((_, i) => {
                const dot = document.createElement('div');
                dot.classList.add('dot');
                if (i === 0) dot.classList.add('active');

                // Add click event to jump to slide
                dot.addEventListener('click', () => currentSlide(i));

                dotsContainer.appendChild(dot);
            });
        }

        // --- Event Listeners ---

        btnPrev.addEventListener('click', () => moveCarousel(-1));
        btnNext.addEventListener('click', () => moveCarousel(1));

        // Update dots on manual scroll
        track.addEventListener('scroll', () => {
            // Debounce or simple check could be added here for performance
            updateDots();
        });
    }

}); // End of DOMContentLoaded

