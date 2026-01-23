document.addEventListener('DOMContentLoaded', () => {
    
    /* =========================================
       1. LÓGICA DEL CALENDARIO
       ========================================= */
    const calendar = document.getElementById('calendar');
    
    // Solo ejecutamos esto si existe el contenedor del calendario
    if (calendar) {
        const monthYear = document.getElementById('monthYear');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');
        
        // Fecha inicial: Enero 2026
        let currentDate = new Date(2026, 0, 1); 

        function renderCalendar() {
            calendar.innerHTML = '';
            const days = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
            
            // 1. Dibujar cabeceras (Lun, Mar, etc.)
            days.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.classList.add('calendar-day-header');
                dayHeader.innerText = day;
                calendar.appendChild(dayHeader);
            });

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            
            // 2. Actualizar título del mes
            if(monthYear) {
                // Truco para que salga en inglés o español según prefieras. 
                // Usa 'es-ES' para español o 'en-US' para inglés.
                monthYear.innerText = new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(currentDate);
            }

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // 3. Celdas vacías antes del día 1
            for (let i = 0; i < firstDay; i++) {
                calendar.appendChild(document.createElement('div'));
            }

            // 4. Días del mes
            for (let i = 1; i <= daysInMonth; i++) {
                const day = document.createElement('div');
                day.classList.add('calendar-day');
                day.innerText = i;
                
                // Ejemplo: Marcar día 20 como hoy
                if (i === 20 && month === 0 && year === 2026) {
                    day.classList.add('today');
                }
                calendar.appendChild(day);
            }
        }

        // Eventos de los botones anterior/siguiente
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

        // Renderizar por primera vez
        renderCalendar();
    }
});

/* =========================================
   2. LÓGICA DE NAVEGACIÓN (Funciones Globales)
   ========================================= */

// Función para cambiar entre Dashboard y Settings (Menú Lateral)
function switchMainView(evt, viewId) {
    if(evt) evt.preventDefault();

    // Ocultar todas las vistas principales
    const views = document.querySelectorAll('.main-view');
    views.forEach(view => {
        view.style.display = 'none';
    });

    // Quitar clase 'active' del menú lateral
    const menuItems = document.querySelectorAll('.menu-item');
    menuItems.forEach(item => {
        item.classList.remove('active');
    });

    // Mostrar la vista seleccionada
    const selectedView = document.getElementById(viewId);
    if(selectedView) {
        selectedView.style.display = 'block';
    }

    // Activar el botón del menú pulsado
    if(evt) {
        evt.currentTarget.classList.add('active');
    }
}

// Función para las pestañas dentro de Settings
function openTab(evt, tabName) {
    if(evt) evt.preventDefault();

    // Ocultar contenidos de pestañas
    const tabContents = document.getElementsByClassName("tab-content");
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = "none";
        tabContents[i].classList.remove("active-content");
    }

    // Desactivar botones de pestañas
    const tabLinks = document.getElementsByClassName("tab-btn");
    for (let i = 0; i < tabLinks.length; i++) {
        tabLinks[i].classList.remove("active");
    }

    // Mostrar contenido seleccionado
    const selectedTab = document.getElementById(tabName);
    if (selectedTab) {
        selectedTab.style.display = "block";
        setTimeout(() => {
            selectedTab.classList.add("active-content");
        }, 10);
    }
    
    // Activar botón pulsado
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
   3. LÓGICA DE GUARDADO DE HORARIO (AJAX)
   ========================================= */
document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('save-schedule-btn');

    if (saveBtn) {
        saveBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Evitar que el formulario recargue la página

            const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
            let schedule = {};

            // Recorrer cada día para construir el objeto JSON
            days.forEach(day => {
                const activeCheck = document.getElementById(`${day}-active`);
                const openInput = document.getElementById(`${day}-open`);
                const closeInput = document.getElementById(`${day}-close`);

                // Solo añadimos el día si encontramos sus inputs en el HTML
                if (activeCheck) {
                    schedule[day] = {
                        active: activeCheck.checked,
                        // Si está activo, guardamos la hora; si no, null
                        open: activeCheck.checked ? (openInput ? openInput.value : null) : null,
                        close: activeCheck.checked ? (closeInput ? closeInput.value : null) : null
                    };
                }
            });

            // Feedback visual de "Guardando..."
            const originalText = saveBtn.innerText;
            saveBtn.innerText = 'Saving...';
            saveBtn.disabled = true;

            // Petición AJAX al servidor
            fetch('index.php?action=update_schedule', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ schedule: schedule })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Schedule updated successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('A connection error occurred.');
            })
            .finally(() => {
                // Restaurar botón
                saveBtn.innerText = originalText;
                saveBtn.disabled = false;
            });
        });
    }
});