/* =========================================
   VARIABLES GLOBALES Y FUNCIONES DE NAVEGACIÓN
   (Deben estar fuera del DOMContentLoaded para ser accesibles desde el HTML)
   ========================================= */

// Función para cambiar vistas principales (Sidebar)
function switchMainView(evt, viewId) {
    if(evt) evt.preventDefault();
    
    document.querySelectorAll('.main-view').forEach(view => view.style.display = 'none');
    document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));

    const selectedView = document.getElementById(viewId);
    if(selectedView) selectedView.style.display = 'block';

    if(evt && evt.currentTarget) evt.currentTarget.classList.add('active');
}

// Función para cambiar pestañas (Settings/Services)
function openTab(evt, tabName) {
    if(evt) evt.preventDefault();

    // 1. GUARDAR EN MEMORIA (Clave para que recuerde dónde estás)
    localStorage.setItem('activeDashboardTab', tabName);

    // 2. Ocultar todos los contenidos y desactivar botones
    const tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = "none";
        tabContents[i].classList.remove("active-content");
    }

    const tabLinks = document.getElementsByClassName("tab-btn");
    for (let i = 0; i < tabLinks.length; i++) {
        tabLinks[i].classList.remove("active");
    }

    // 3. Mostrar el seleccionado
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.style.display = "block";
        // Pequeño timeout para permitir transición CSS si la hubiera
        setTimeout(() => selectedTab.classList.add("active-content"), 10);
    }
    
    // 4. Activar visualmente el botón clicado
    if (evt && evt.currentTarget) {
        evt.currentTarget.classList.add("active");
    } else {
        // Si no hay evento (carga automática), buscamos el botón por el onclick
        const btn = document.querySelector(`.tab-btn[onclick*="'${tabName}'"]`);
        if (btn) btn.classList.add("active");
    }
}

// Función para togglear días en el horario
function toggleDay(checkbox) {
    const row = checkbox.closest('.schedule-row');
    const timeInputs = row.querySelector('.time-inputs');
    const closedLabel = row.querySelector('.closed-label');

    if (checkbox.checked) {
        timeInputs.style.display = 'flex';
        closedLabel.style.display = 'none';
    } else {
        timeInputs.style.display = 'none';
        closedLabel.style.display = 'block';
    }
}

// Función para AÑADIR SERVICIO
function submitService(e) {
    e.preventDefault(); 

    const form = document.getElementById('add-service-form');
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('button');
    const originalText = submitBtn.textContent;

    submitBtn.disabled = true;
    submitBtn.textContent = "Saving...";

    // Ajusta la ruta ../index.php según dónde esté tu JS relativo al PHP
    // Si dashboard.php carga este JS desde public/js/, la ruta a index es ../index.php
    fetch('../index.php?action=add_service', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Esperamos JSON directo
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;

        if (data.success) {
            form.reset();
            
            // Agregar el servicio a la lista dinámicamente sin recargar
            const servicesList = document.getElementById('services-list');
            const noServicesMsg = document.getElementById('no-services-msg');
            
            // Crear el HTML del nuevo servicio
            const serviceHTML = `
                <div class="service-item" style="background: white; border: 1px solid #eee; padding: 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                    
                    <div style="display: flex; align-items: center; gap: 15px;">
                        <div style="background: #f0f0f0; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-cut" style="color: #555;"></i>
                        </div>
                        <div>
                            <h4 style="margin: 0; font-size: 16px; color: #333;">${data.service.name}</h4>
                            <span style="font-size: 13px; color: #777;">
                                <i class="far fa-clock"></i> ${data.service.duration} min
                            </span>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 20px;">
                        <span style="font-weight: bold; font-size: 18px; color: #000;">
                            ${data.service.price} €
                        </span>
                        
                        <a href="#" 
                           onclick="deleteService(${data.service.id}, this); return false;" 
                           style="color: #ff4d4d; background: #fff0f0; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 6px; text-decoration: none;">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </div>

                </div>
            `;
            
            // Si estaba el mensaje de "no services", ocultarlo
            if (noServicesMsg) {
                noServicesMsg.style.display = 'none';
            }
            
            // Agregar el nuevo servicio a la lista
            servicesList.insertAdjacentHTML('beforeend', serviceHTML);
            
            // Mostrar mensaje de éxito
            alert('Service added successfully!');
        } else {
            alert('Error: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Hubo un error al guardar. Revisa la consola.');
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    });
}

// Función para BORRAR SERVICIO
function deleteService(id, element) {
    if (!confirm('Are you sure?')) return;

    const row = element.closest('.service-item'); 

    fetch('../index.php?action=delete_service&id=' + id)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (row) {
                row.remove();
                // Si borras, también nos aseguramos de quedarnos en la pestaña
                localStorage.setItem('activeDashboardTab', 'notifications');
            } else {
                localStorage.setItem('activeDashboardTab', 'notifications');
                window.location.reload();
            }
        } else {
            alert('Error deleting service: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión.');
    });
}


/* =========================================
   LÓGICA QUE SE EJECUTA AL CARGAR LA PÁGINA (DOM READY)
   ========================================= */
document.addEventListener('DOMContentLoaded', () => {

    // 0. CAMBIAR VISTA PRINCIPAL AL DASHBOARD (Business)
    switchMainView(null, 'view-dashboard');
    
    // Activar el menu item correcto
    document.querySelectorAll('.menu-item').forEach(item => item.classList.remove('active'));
    const dashboardMenuItem = document.querySelector('.menu-item[onclick*="view-dashboard"]');
    if (dashboardMenuItem) {
        dashboardMenuItem.classList.add('active');
    }

    // 1. ESTABLECER PESTAÑA POR DEFECTO A BUSINESS
    localStorage.setItem('activeDashboardTab', 'business');
    openTab(null, 'business');

    // 2. CALENDARIO
    const calendar = document.getElementById('calendar');
    if (calendar) {
        const monthYear = document.getElementById('monthYear');
        const prevMonthBtn = document.getElementById('prevMonth'); // IDs corregidos
        const nextMonthBtn = document.getElementById('nextMonth');
        
        let currentDate = new Date(); 

        function renderCalendar() {
            calendar.innerHTML = '';
          //  const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const days=['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            
            days.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.classList.add('calendar-day-header');
                dayHeader.innerText = day;
                calendar.appendChild(dayHeader);
            });

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const today = currentDate.getDate();
            
            if(monthYear) {
                monthYear.innerText = new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(currentDate);
            }

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                calendar.appendChild(document.createElement('div'));
            }

            for (let i = 1; i <= daysInMonth; i++) {
                const day = document.createElement('div');
                day.classList.add('calendar-day');
                day.innerText = i;
                if (i === today && month === new Date().getMonth() && year === new Date().getFullYear()) {
                    day.classList.add('today');
                }
                calendar.appendChild(day);
            }
        }

        if(prevMonthBtn) {
            prevMonthBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            });
        }
        if(nextMonthBtn) {
            nextMonthBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            });
        }
        renderCalendar();
    }
});


/* 
   5. Sidebar toggle (responsive)
    */
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (!sidebar || !toggleBtn) return;

    // Apply default state based on width: solo colapsa en tablet/móvil
    function applyDefaultState() {
        if (window.innerWidth <= 900) {
            sidebar.classList.add('collapsed');
            sidebar.classList.remove('open');
        } else {
            // En desktop, siempre abierto por defecto
            sidebar.classList.remove('collapsed');
            sidebar.classList.add('open');
        }
    }

    applyDefaultState();

    // Toggle on button click: funciona en todas las vistas
    toggleBtn.addEventListener('click', function(e) {
        e.preventDefault();
        if (sidebar.classList.contains('collapsed')) {
            sidebar.classList.remove('collapsed');
            sidebar.classList.add('open');
        } else {
            sidebar.classList.add('collapsed');
            sidebar.classList.remove('open');
        }
    });

    // Adjust on resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            applyDefaultState();
        }, 120);
    });
});

/* =========================================
   2. NAVIGATION LOGIC (Global Functions)
   ========================================= */

// Function to switch between Dashboard and Settings (Sidebar Menu)
function switchMainView(evt, viewId) {
    if(evt) evt.preventDefault();

    // Hide all main views
    const views = document.querySelectorAll('.main-view');
    views.forEach(view => {
        view.style.display = 'none';
    });

    // Remove 'active' class from sidebar menu
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.classList.remove('active');
    });

    // Show selected view
    const selectedView = document.getElementById(viewId);
    if(selectedView) {
        selectedView.style.display = 'block';
    }

    // Activate the clicked menu button
    if(evt) {
        evt.currentTarget.classList.add('active');
    }
}

// Function for tabs within Settings
function openTab(evt, tabName) {
    if(evt) evt.preventDefault();

    // Hide tab contents
    const tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = "none";
        tabContents[i].classList.remove("active-content");
    }

    // Deactivate tab buttons
    const tabLinks = document.getElementsByClassName("tab-btn");
    for (let i = 0; i < tabLinks.length; i++) {
        tabLinks[i].classList.remove("active");
    }

    // Show selected content
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.style.display = "block";
        setTimeout(() => {
            selectedTab.classList.add("active-content");
        }, 10);
    }
    
    // Activate clicked button
    if(evt) {
        evt.currentTarget.classList.add("active");
    }
}



function toggleDay(checkbox) {
    const row = checkbox.closest('.schedule-row');
    const timeInputs = row.querySelector('.time-inputs');
    const closedLabel = row.querySelector('.closed-label');

    if (checkbox.checked) {
        timeInputs.style.display = 'flex';
        closedLabel.style.display = 'none';
    } else {
        timeInputs.style.display = 'none';
        closedLabel.style.display = 'block';
    }
}

/* =========================================
   3. SCHEDULE SAVING LOGIC (AJAX)
   ========================================= */
document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('save-schedule-btn');

    if (saveBtn) {
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent form reload

    // 3. GUARDAR HORARIO (SCHEDULE)
    const saveScheduleBtn = document.getElementById('save-schedule-btn');
    if (saveScheduleBtn) {
        saveScheduleBtn.addEventListener('click', function(e) {
            e.preventDefault();

            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            let schedule = {};

            // Loop through each day to build JSON object
            days.forEach(day => {
                const activeCheck = document.getElementById(`${day}-active`);
                const openInput = document.getElementById(`${day}-open`);
                const closeInput = document.getElementById(`${day}-close`);

                // Only add day if we find its inputs in HTML
                if (activeCheck) {
                    schedule[day] = {
                        active: activeCheck.checked,
                        // If active, save time; if not, null
                if (activeCheck) {
                    schedule[day] = {
                        active: activeCheck.checked,
                        open: activeCheck.checked ? (openInput ? openInput.value : null) : null,
                        close: activeCheck.checked ? (closeInput ? closeInput.value : null) : null
                    };
                }
            });

            // Visual feedback "Saving..."
            const originalText = saveBtn.innerText;
            saveBtn.innerText = 'Saving...';
            saveBtn.disabled = true;

            // AJAX request to server
            fetch('index.php?action=update_schedule', {
            const originalText = saveScheduleBtn.innerText;
            saveScheduleBtn.innerText = 'Saving...';
            saveScheduleBtn.disabled = true;

            fetch('../index.php?action=update_schedule', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ schedule: schedule })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) alert('Schedule updated successfully!');
                else alert('Error: ' + (data.message || 'Unknown error'));
            })
            .catch(error => { console.error(error); alert('Connection error'); })
            .finally(() => {
                // Restore button
                saveBtn.innerText = originalText;
                saveBtn.disabled = false;
            });
        });
    }
});


/* =========================================
   4. Update Business Info Logic
   ========================================= */

document.addEventListener('DOMContentLoaded', function() {
    
    // ... your calendar code ...

    // BUSINESS PROFILE SAVE HANDLING
                saveScheduleBtn.innerText = originalText;
                saveScheduleBtn.disabled = false;
            });
        });
    }

    // 4. GUARDAR INFORMACIÓN DE NEGOCIO
    const businessForm = document.getElementById('business-form');
    if (businessForm) {
        businessForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = businessForm.querySelector('.btn-save');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            submitBtn.disabled = true;

            // Use FormData to send text and IMAGES
            const formData = new FormData(businessForm);

            fetch('../index.php?action=update_business_info', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Business information updated successfully!');
                    // Optional: Reload to see new images
                    // location.reload(); 
                    alert('Business information updated!');
                    localStorage.setItem('activeDashboardTab', 'business'); // Mantenemos en business
                    window.location.reload(); 
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => { console.error(error); alert('Connection error'); })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});