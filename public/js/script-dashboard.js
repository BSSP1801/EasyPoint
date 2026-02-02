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
    
    // Función auxiliar para fecha
    function formatAppointmentDate(dateStr, timeStr) {
        const date = new Date(dateStr + 'T' + timeStr);
        const options = { weekday: 'short', month: 'short', day: 'numeric' };
        const timeParts = timeStr.split(':');
        const timeFormatted = `${timeParts[0]}:${timeParts[1]}`;
        return date.toLocaleDateString('en-US', options) + ' - ' + timeFormatted;
    }

    // Definimos la función PRIMERO
    window.renderAppointmentsList = function(appointments, titleText) {
        const container = document.getElementById('appointments-container');
        const title = document.getElementById('appointments-title');
        
        if(title) title.innerText = titleText;
        container.innerHTML = ''; 

        if (!appointments || appointments.length === 0) {
            container.innerHTML = '<div class="appointment"><p style="color: #888; font-style: italic;">No appointments found.</p></div>';
            return;
        }

        const today = new Date().toISOString().split('T')[0];

        appointments.forEach(appt => {
            let statusClass = 'pending';
            if(appt.status === 'confirmed') statusClass = 'confirmed';
            else if(appt.status === 'cancelled') statusClass = 'cancelled';
            
            let buttonsHtml = '';
            
            // Lógica de botones
            if (appt.appointment_date >= today) {
                if (appt.status === 'pending') {
                    buttonsHtml = `
                        <div class="appt-actions">
                            <button onclick="updateStatus(${appt.id}, 'confirmed')" class="btn-action btn-confirm" title="Confirm"><i class="fas fa-check"></i></button>
                            <button onclick="updateStatus(${appt.id}, 'cancelled')" class="btn-action btn-cancel" title="Cancel"><i class="fas fa-times"></i></button>
                        </div>
                    `;
                } else if (appt.status === 'confirmed') {
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
                            <i class="far fa-user"></i> ${appt.client_name} | 
                            <i class="far fa-clock"></i> ${formatAppointmentDate(appt.appointment_date, appt.appointment_time)}
                        </p>
                    </div>
                    ${buttonsHtml}
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
        });
    };

    window.updateStatus = function(id, newStatus) {
    if(!confirm('Are you sure you want to change the status to ' + newStatus + '?')) return;

    const formData = new FormData();
    formData.append('id', id);
    formData.append('status', newStatus);

    fetch('index.php?action=change_status', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
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

            if(monthYear) {
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
                        const todayStr = new Date().toISOString().split('T')[0];
                        const upcoming = allAppointments.filter(a => a.appointment_date >= todayStr);
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
        businessForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('.btn-save');
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
        saveScheduleBtn.addEventListener('click', function(e) {
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
                headers: {'Content-Type': 'application/json'},
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
        const todayStr = new Date().toISOString().split('T')[0];
        const upcoming = allAppointments.filter(a => a.appointment_date >= todayStr);
        renderAppointmentsList(upcoming, "Upcoming Appointments");
    }

}); // End DOMContentLoaded


/* =========================
   GLOBAL FUNCTIONS
   ========================= */

function switchMainView(evt, viewId) {
    if(evt) evt.preventDefault();
    
    // 1. GUARDAR: Memorizamos en qué vista estamos
    sessionStorage.setItem('currentView', viewId);

    // Ocultar todas las vistas
    document.querySelectorAll('.main-view').forEach(v => v.style.display = 'none');
    
    // Quitar active de todos los menús
    document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
    
    // Mostrar la vista seleccionada
    const target = document.getElementById(viewId);
    if(target) target.style.display = 'block';

    // 2. RECUPERAR ACTIVE: Si hay evento (click) lo usamos, si no (recarga) buscamos el link
    if(evt && evt.currentTarget) {
        evt.currentTarget.classList.add('active');
    } else {
        // Truco para encontrar el botón del menú correspondiente a esta vista
        const link = document.querySelector(`a[onclick*="${viewId}"]`);
        if(link) link.classList.add('active');
    }
}

function openTab(evt, tabName) {
    if(evt) evt.preventDefault();
    document.querySelectorAll('.tab-content').forEach(c => {
        c.classList.remove('active-content');
    });
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));

    const target = document.getElementById(tabName);
    if(target) {
        target.classList.add('active-content');
    }
    if(evt && evt.currentTarget) evt.currentTarget.classList.add('active');
}

function toggleDay(checkbox) {
    const row = checkbox.closest('.schedule-row');
    const inputs = row.querySelector('.time-inputs');
    const label = row.querySelector('.closed-label');
    
    if(checkbox.checked) {
        inputs.style.display = 'flex';
        label.style.display = 'none';
    } else {
        inputs.style.display = 'none';
        label.style.display = 'block';
    }
}

function submitService(e) {
    // (Tu función submitService existente...)
    e.preventDefault();
    const form = document.getElementById('add-service-form');
    // ... resto del código ...
    // (Manten el código que ya tenías para submitService y deleteService)
    // Para simplificar la respuesta he acortado estas dos funciones finales 
    // ya que no afectan al problema principal.
}

function deleteService(id, el) {
    // (Tu función deleteService existente...)
    if(!confirm('Delete this service?')) return;
    fetch('index.php?action=delete_service&id=' + id)
    // ... resto del código ...
}

/* =========================
       6. RESTAURAR VISTA TRAS RECARGA
       ========================= */
    // Verificamos si hay una vista guardada en memoria
    const savedView = sessionStorage.getItem('currentView');
    if (savedView) {
        // Si existe, forzamos esa vista
        switchMainView(null, savedView);
    } else {
        // (Opcional) Si quieres forzar una por defecto si no hay nada guardado
        // switchMainView(null, 'view-calendar');
    }

