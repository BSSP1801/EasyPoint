/* public/js/script-dashboard.js */

document.addEventListener('DOMContentLoaded', () => {

    /* =========================
       1. SIDEBAR & RESPONSIVE
       ========================= */
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebarToggle');

    function applyDefaultState() {
        if (window.innerWidth <= 900) {
            sidebar?.classList.add('collapsed');
        } else {
            sidebar?.classList.remove('collapsed');
        }
    }

    applyDefaultState();

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            sidebar.classList.toggle('collapsed');
        });
    }

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(applyDefaultState, 150);
    });


    /* =========================
       2. CALENDAR WIDGET & FUNCIONES
       ========================= */
    const calendar = document.getElementById('calendar');

    // Función auxiliar para obtener hoy en formato YYYY-MM-DD sin timezone issues
    function getTodayStr() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Función auxiliar para fecha - SIN conversión de timezone
    function formatAppointmentDate(dateStr, timeStr) {
        // dateStr format: YYYY-MM-DD
        // timeStr format: HH:MM:SS
        // Parse without timezone conversion
        const [year, month, day] = dateStr.split('-').map(Number);
        const [hours, minutes] = timeStr.split(':').map(Number);
        
        // Create date WITHOUT timezone conversion - just use the local date values
        const date = new Date(year, month - 1, day, hours, minutes);
        
        const options = { weekday: 'short', month: 'short', day: 'numeric' };
        const timeFormatted = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;
        return date.toLocaleDateString('en-US', options) + ' - ' + timeFormatted;
    }

    // Definimos la función PRIMERO
  window.renderAppointmentsList = function (appointments, titleText) {
        const container = document.getElementById('appointments-container');
        const title = document.getElementById('appointments-title');

        if (title) title.innerText = titleText;
        
        // Si no existe el contenedor (por ejemplo, estamos en una vista que no lo carga), salimos
        if (!container) return; 

        container.innerHTML = '';

        if (!appointments || appointments.length === 0) {
            container.innerHTML = '<div class="appointment"><p style="font-style: italic;">No appointments found.</p></div>';
            return;
        }

        const today = getTodayStr();

        appointments.forEach(appt => {
            let statusClass = 'pending';
            if (appt.status === 'confirmed') statusClass = 'confirmed';
            else if (appt.status === 'cancelled') statusClass = 'cancelled';

            let buttonsHtml = '';
            
            // Detectar si es vista de Usuario (tiene store_name) o Tienda (tiene client_name)
            const isUserView = appt.store_name ? true : false;
            const counterpartName = isUserView ? appt.store_name : appt.client_name;
            const iconClass = isUserView ? 'fas fa-store' : 'far fa-user';

            // Lógica de botones
            if (appt.appointment_date >= today) {
                if (appt.status === 'pending') {
                    if (isUserView) {
                        // El usuario solo puede cancelar
                        buttonsHtml = `
                            <div class="appt-actions">
                                <button onclick="updateStatus(${appt.id}, 'cancelled')" class="btn-action btn-cancel" title="Cancel Booking"><i class="fas fa-times"></i></button>
                            </div>
                        `;
                    } else {
                        // La tienda puede confirmar o cancelar
                        buttonsHtml = `
                            <div class="appt-actions">
                                <button onclick="updateStatus(${appt.id}, 'confirmed')" class="btn-action btn-confirm" title="Confirm"><i class="fas fa-check"></i></button>
                                <button onclick="updateStatus(${appt.id}, 'cancelled')" class="btn-action btn-cancel" title="Cancel"><i class="fas fa-times"></i></button>
                            </div>
                        `;
                    }
                } else if (appt.status === 'confirmed') {
                    // Ambos pueden cancelar una confirmada (según lógica simple)
                    buttonsHtml = `
                        <div class="appt-actions">
                            <button onclick="updateStatus(${appt.id}, 'cancelled')" class="btn-action btn-cancel" title="Cancel"><i class="fas fa-times"></i></button>
                        </div>
                    `;
                }
            }

            const html = `
                <div class="appointment">
                    <div class="appointment-info">
                        <h4>
                            ${appt.service_name} 
                            <span class="status ${statusClass}">${appt.status.charAt(0).toUpperCase() + appt.status.slice(1)}</span>
                        </h4>
                        <p>
                            <i class="${iconClass}"></i> ${counterpartName} | 
                            <i class="far fa-clock"></i> ${formatAppointmentDate(appt.appointment_date, appt.appointment_time)}
                        </p>
                        ${isUserView && appt.store_address ? `<p style="font-size:12px; color:#666;"><i class="fas fa-map-marker-alt"></i> ${appt.store_address}</p>` : ''}
                    </div>
                    ${buttonsHtml}
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    };

    window.updateStatus = function (id, newStatus) {
        if (!confirm('Are you sure you want to change the status to ' + newStatus + '?')) return;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', newStatus);

        fetch('index.php?action=change_status', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // AQUI ESTÁ LA CLAVE: Recargar la página
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                alert('Connection error');
            });
    };

    // Lógica del Calendario
    if (calendar) {
        const monthYear = document.getElementById('monthYear');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');

        let currentDate = new Date();
        let selectedDate = null;

        function renderCalendar() {
            calendar.innerHTML = '';
            const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

            days.forEach(day => {
                const header = document.createElement('div');
                header.className = 'calendar-day-header';
                header.innerText = day;
                calendar.appendChild(header);
            });

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const today = new Date();

            if (monthYear) {
                monthYear.innerText = new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(currentDate);
            }

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            for (let i = 0; i < firstDay; i++) {
                calendar.appendChild(document.createElement('div'));
            }

            for (let i = 1; i <= daysInMonth; i++) {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'calendar-day';
                dayDiv.innerText = i;

                const monthString = String(month + 1).padStart(2, '0');
                const dayString = String(i).padStart(2, '0');
                const fullDate = `${year}-${monthString}-${dayString}`;

                if (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayDiv.classList.add('today');
                }

                if (typeof appointmentDates !== 'undefined' && appointmentDates.includes(fullDate)) {
                    dayDiv.classList.add('has-appointment');
                    const dot = document.createElement('span');
                    dot.className = 'appointment-dot';
                    dayDiv.appendChild(dot);
                }

                if (selectedDate === fullDate) {
                    dayDiv.classList.add('selected');
                }

                dayDiv.addEventListener('click', () => {
                    if (selectedDate === fullDate) {
                        selectedDate = null;
                        renderCalendar();
                        const upcoming = allAppointments.filter(a => a.appointment_date >= getTodayStr());
                        renderAppointmentsList(upcoming, "Upcoming Appointments");
                    }
                    else {
                        selectedDate = fullDate;
                        renderCalendar();
                        const filtered = allAppointments.filter(a => a.appointment_date === fullDate);
                        renderAppointmentsList(filtered, `Appointments for ${fullDate}`);
                    }
                });

                calendar.appendChild(dayDiv);
            }
        }

        prevMonthBtn?.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });

        nextMonthBtn?.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });

        renderCalendar();
    }


    /* =========================
       3. SAVE BUSINESS INFO
       ========================= */
    const businessForm = document.getElementById('business-form');
    if (businessForm) {
        businessForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            btn.disabled = true;

            const formData = new FormData(this);

            fetch('index.php?action=update_business_info', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Information updated successfully!');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Connection error');
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });
        });
    }

    /* =========================
       4. SAVE SCHEDULE
       ========================= */
    const saveScheduleBtn = document.getElementById('save-schedule-btn');
    if (saveScheduleBtn) {
        saveScheduleBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            let schedule = {};

            days.forEach(day => {
                const active = document.getElementById(`${day}-active`);
                const open = document.getElementById(`${day}-open`);
                const close = document.getElementById(`${day}-close`);

                if (active) {
                    schedule[day] = {
                        active: active.checked,
                        open: active.checked ? open?.value : null,
                        close: active.checked ? close?.value : null
                    };
                }
            });

            const originalText = saveScheduleBtn.innerText;
            saveScheduleBtn.innerText = 'Saving...';
            saveScheduleBtn.disabled = true;

            fetch('index.php?action=update_schedule', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ schedule: schedule })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) alert('Schedule updated!');
                    else alert('Error: ' + data.message);
                })
                .finally(() => {
                    saveScheduleBtn.innerText = originalText;
                    saveScheduleBtn.disabled = false;
                });
        });
    }

    /* ========================================================
       5. INICIALIZACIÓN AUTOMÁTICA (MOVIDO AL FINAL)
       ======================================================== */
    // Ahora sí funcionará porque renderAppointmentsList ya existe
    if (typeof allAppointments !== 'undefined') {
        const upcoming = allAppointments.filter(a => a.appointment_date >= getTodayStr());
        renderAppointmentsList(upcoming, "Upcoming Appointments");
    }

    /* ========================================================
          6. BUSCAR HISTORIAL DE CLIENTES
          ======================================================== */
    window.searchClientHistory = function () {
        const email = document.getElementById('client-search-email').value;
        const resultsContainer = document.getElementById('client-history-results');

        if (!email) {
            alert("Please enter an email address");
            return;
        }

        resultsContainer.innerHTML = '<div style="text-align:center;"><i class="fas fa-spinner fa-spin"></i> Searching...</div>';

        fetch(`index.php?action=search_client_history&email=${encodeURIComponent(email)}`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    resultsContainer.innerHTML = `<p style="color: #ff4d4d; text-align: center;">${data.message}</p>`;
                    return;
                }

                if (data.appointments.length === 0) {
                    resultsContainer.innerHTML = '<p style="text-align: center;">No appointments found for this client.</p>';
                    return;
                }

                let html = `
    <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
            <tr style="border-bottom: 2px solid #eee; text-align: left;">
                <th style="padding: 10px;">Client</th> <th style="padding: 10px;">Date</th>
                <th style="padding: 10px;">Service</th>
                <th style="padding: 10px;">Status</th>
            </tr>
        </thead>
        <tbody>`;

                data.appointments.forEach(appt => {
                    const statusClass = appt.status.toLowerCase();
                    html += `
        <tr style="border-bottom: 1px solid #f9f9f9;">
            <td style="padding: 10px;">
                <div style="font-weight:bold; font-size: 14px;">${appt.username || 'User'}</div>
                <div style="font-size: 12px; color: #666;">${appt.email}</div>
            </td>
            <td style="padding: 10px;">${appt.appointment_date}<br><small>${appt.appointment_time}</small></td>
            <td style="padding: 10px;">${appt.service_name}</td>
            <td style="padding: 10px;"><span class="status ${statusClass}">${appt.status}</span></td>
        </tr>`;
                });

                html += `</tbody></table>`;
                resultsContainer.innerHTML = html;
            })
            .catch(err => {
                console.error(err);
                resultsContainer.innerHTML = '<p style="color: red;">Connection error</p>';
            });
    };

    /* =========================
           7. BUSQUEDA ACTIVA DE CLIENTES
           ========================= */
    const searchInput = document.getElementById('client-search-email');
    let debounceTimer;

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const term = this.value.trim();

            // Limpiar el temporizador anterior
            clearTimeout(debounceTimer);

            // Si hay menos de 3 caracteres, limpiamos resultados y no buscamos
            if (term.length < 3) {
                document.getElementById('client-history-results').innerHTML =
                    '<p style="font-style: italic; text-align: center; padding: 20px;">Type at least 3 characters to search...</p>';
                return;
            }

            // Esperar 500ms después de que el usuario deje de escribir para buscar
            debounceTimer = setTimeout(() => {
                window.searchClientHistory(); // Llamamos a tu función existente
            }, 500);
        });
    }
}); // End DOMContentLoaded


/* =========================
   GLOBAL FUNCTIONS
   ========================= */

function switchMainView(evt, viewId) {
    if (evt) evt.preventDefault();

    // 1. GUARDAR: Memorizamos en qué vista estamos
    sessionStorage.setItem('currentView', viewId);

    // Ocultar todas las vistas
    document.querySelectorAll('.main-view').forEach(v => v.style.display = 'none');

    // Quitar active de todos los menús
    document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));

    // Mostrar la vista seleccionada
    const target = document.getElementById(viewId);
    if (target) target.style.display = 'block';

    // 2. RECUPERAR ACTIVE: Si hay evento (click) lo usamos, si no (recarga) buscamos el link
    if (evt && evt.currentTarget) {
        evt.currentTarget.classList.add('active');
    } else {
        // Truco para encontrar el botón del menú correspondiente a esta vista
        const link = document.querySelector(`a[onclick*="${viewId}"]`);
        if (link) link.classList.add('active');
    }
}

function openTab(evt, tabName) {
    if (evt) evt.preventDefault();
    document.querySelectorAll('.tab-content').forEach(c => {
        c.classList.remove('active-content');
    });
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

    const target = document.getElementById(tabName);
    if (target) {
        target.classList.add('active-content');
    }
    if (evt && evt.currentTarget) evt.currentTarget.classList.add('active');
}

function toggleDay(checkbox) {
    const row = checkbox.closest('.schedule-row');
    const inputs = row.querySelector('.time-inputs');
    const label = row.querySelector('.closed-label');

    if (checkbox.checked) {
        inputs.style.display = 'flex';
        label.style.display = 'none';
    } else {
        inputs.style.display = 'none';
        label.style.display = 'block';
    }
}

function submitService(e) {
    e.preventDefault();
    const form = document.getElementById('add-service-form');
    if (!form) return;
    const btn = form.querySelector('button[type="submit"]');
    const originalText = btn ? btn.innerHTML : '';
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...'; }

    const formData = new FormData(form);

    fetch('index.php?action=add_service', {
        method: 'POST',
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.success && data.service) {
                const svc = data.service;
                const list = document.getElementById('services-list');
                // Remove empty message if present
                const noMsg = document.getElementById('no-services-msg');
                if (noMsg) noMsg.remove();

                const div = document.createElement('div');
                div.className = 'service-item';
                div.innerHTML = `
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div><i class="fas fa-cut" style="color: #555;"></i></div>
                    <div>
                        <h4 style="margin: 0; font-size: 16px; color: #333;">${escapeHtml(svc.name)}</h4>
                        <span style="font-size: 13px; color: #333;"><i class="far fa-clock"></i> ${escapeHtml(svc.duration)} min</span>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 20px;">
                    <span style="font-weight: bold; font-size: 18px; color: #000;">${escapeHtml(svc.price)} €</span>
                    <a href="#" onclick="deleteService(${svc.id}, this); return false;" style="color: #ff4d4d; background: #fff0f0; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 6px;"><i class="fas fa-trash-alt"></i></a>
                </div>
            `;
                if (list) list.insertAdjacentElement('afterbegin', div);
                form.reset();
            } else {
                alert('Error: ' + (data.message || 'Could not add service'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Connection error');
        })
        .finally(() => {
            if (btn) { btn.disabled = false; btn.innerHTML = originalText; }
        });
}

function deleteService(id, el) {
    if (!confirm('Delete this service?')) return;
    fetch('index.php?action=delete_service&id=' + id)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const item = el.closest('.service-item');
                if (item) item.remove();
                const list = document.getElementById('services-list');
                if (list && !list.querySelector('.service-item')) {
                    const msg = document.createElement('div');
                    msg.id = 'no-services-msg';
                    msg.style = 'text-align: center; padding: 40px; background: rgba(235, 230, 210, 0.55); border-radius: 10px; color: #000000;';
                    msg.textContent = "You haven't added any services yet.";
                    list.appendChild(msg);
                }
            } else {
                alert('Error deleting service: ' + (data.message || 'Unknown'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Connection error');
        });
}

function escapeHtml(unsafe) {
    return String(unsafe)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/\"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

/* =========================
   6. RESTAURAR VISTA TRAS RECARGA
   ========================= */
const savedView = sessionStorage.getItem('currentView');

if (savedView) {
    // VERIFICACIÓN EXTRA: ¿Existe realmente ese elemento en el HTML actual?
    const targetEl = document.getElementById(savedView);
    
    // Solo cambiamos si el elemento existe (evita que un usuario intente abrir vista de tienda)
    if (targetEl) {
        switchMainView(null, savedView);
    } else {
        // Si no existe (ej. cambio de rol), limpiamos la memoria
        sessionStorage.removeItem('currentView');
    }
}

/* =========================
   8. RESTAURAR VISTA TRAS RECARGA
   ========================= */


window.submitPasswordChange = function(e) {
    e.preventDefault();
    
    const form = document.getElementById('change-password-form');
    const msgDiv = document.getElementById('password-msg');
    const btn = form.querySelector('button[type="submit"]');
    
    // UI Feedback
    const originalText = btn.innerText;
    btn.innerText = 'Updating...';
    btn.disabled = true;
    msgDiv.innerText = '';
    msgDiv.style.color = '#333';

    const formData = new FormData(form);

    fetch('index.php?action=change_password', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            msgDiv.style.color = 'green';
            msgDiv.innerText = data.message;
            form.reset();
        } else {
            msgDiv.style.color = 'red';
            msgDiv.innerText = data.message;
        }
    })
    .catch(err => {
        console.error(err);
        msgDiv.style.color = 'red';
        msgDiv.innerText = 'Connection error. Please try again.';
    })
    .finally(() => {
        btn.innerText = originalText;
        btn.disabled = false;
        
        // Limpiar mensaje de éxito después de 3 segundos
        if (msgDiv.style.color === 'green') {
            setTimeout(() => { msgDiv.innerText = ''; }, 3000);
        }
    });
};


/* =========================
   9. TOGGLE VISIBILIDAD CONTRASEÑA (EL OJITO)
   ========================= */
window.togglePasswordVisibility = function(iconBtn) {
    // Encontramos el input que es el hermano anterior del icono dentro del mismo div .password-group
    const inputField = iconBtn.previousElementSibling;
    
    if (!inputField || (inputField.tagName !== 'INPUT')) return;

    // Verificamos el estado actual y cambiamos
    if (inputField.type === "password") {
        inputField.type = "text";
        // Cambiar icono a ojo tachado (usando Font Awesome 5/6)
        iconBtn.classList.remove("fa-eye");
        iconBtn.classList.add("fa-eye-slash");
    } else {
        inputField.type = "password";
        // Cambiar icono a ojo normal
        iconBtn.classList.remove("fa-eye-slash");
        iconBtn.classList.add("fa-eye");
    }
};