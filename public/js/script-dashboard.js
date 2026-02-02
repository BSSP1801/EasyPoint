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
    
    // Inicializar estado
    applyDefaultState();

    // Toggle manual
    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            sidebar.classList.toggle('collapsed');
        });
    }

    // Resize listener
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(applyDefaultState, 150);
    });


    /* =========================
       2. CALENDAR WIDGET
       ========================= */
    const calendar = document.getElementById('calendar');
    if (calendar) {
        const monthYear = document.getElementById('monthYear');
        const prevMonthBtn = document.getElementById('prevMonth'); // IDs corregidos
        const nextMonthBtn = document.getElementById('nextMonth');
        
        let currentDate = new Date(); 

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
                
                if (i === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
                    dayDiv.classList.add('today');
                }
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
       3. SAVE BUSINESS INFO (AJAX)
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

            fetch('../index.php?action=update_business_info', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Information updated successfully!');
                    // Recargar solo si hay nuevas imágenes para ver cambios, o manejarlo por DOM
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
       4. SAVE SCHEDULE (AJAX)
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

}); // End DOMContentLoaded


/* =========================
   GLOBAL FUNCTIONS (Called from HTML)
   ========================= */

// Navigation
function switchMainView(evt, viewId) {
    if(evt) evt.preventDefault();
    document.querySelectorAll('.main-view').forEach(v => v.style.display = 'none');
    
    // Ocultar todos los active del sidebar
    document.querySelectorAll('.menu-item').forEach(m => m.classList.remove('active'));
    
    const target = document.getElementById(viewId);
    if(target) target.style.display = 'block';

    if(evt && evt.currentTarget) evt.currentTarget.classList.add('active');
}

// Tabs
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

// Toggle Schedule Day
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

// AJAX: Add Service
function submitService(e) {
    e.preventDefault();
    const form = document.getElementById('add-service-form');
    const formData = new FormData(form);
    const btn = form.querySelector('button');
    const originalText = btn.textContent;
    
    btn.textContent = 'Adding...';
    btn.disabled = true;

    fetch('index.php?action=add_service', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            form.reset();
            const list = document.getElementById('services-list');
            const noMsg = document.getElementById('no-services-msg');
            if(noMsg) noMsg.style.display = 'none';

            const html = `
            <div class="service-item" style="background: white; border: 1px solid #eee; padding: 20px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="background: #f0f0f0; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-cut" style="color: #555;"></i>
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 16px; color: #333;">${data.service.name}</h4>
                        <span style="font-size: 13px; color: #777;"><i class="far fa-clock"></i> ${data.service.duration} min</span>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 20px;">
                    <span style="font-weight: bold; font-size: 18px; color: #000;">${data.service.price} €</span>
                    <a href="#" onclick="deleteService(${data.service.id}, this); return false;" style="color: #ff4d4d; background: #fff0f0; width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; border-radius: 6px;"><i class="fas fa-trash-alt"></i></a>
                </div>
            </div>`;
            
            list.insertAdjacentHTML('beforeend', html);
            alert('Service added!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => alert('Connection Error'))
    .finally(() => {
        btn.textContent = originalText;
        btn.disabled = false;
    });
}

// AJAX: Delete Service
function deleteService(id, el) {
    if(!confirm('Delete this service?')) return;
    
    fetch('index.php?action=delete_service&id=' + id)
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            el.closest('.service-item').remove();
        } else {
            alert('Error deleting');
        }
    });
}