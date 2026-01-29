document.addEventListener('DOMContentLoaded', () => {
    
    /* =========================================
       1. Calendar Widget Logic
       ========================================= */
    const calendar = document.getElementById('calendar');
    
    // only run if calendar element exists
    if (calendar) {
        const monthYear = document.getElementById('monthYear');
        const prevMonthBtn = document.getElementById('prevMonth');
        const nextMonthBtn = document.getElementById('nextMonth');
        
   
        let currentDate = new Date(); 

        function renderCalendar() {
            calendar.innerHTML = '';
          //  const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            const days=['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            
            // Draw day headers
            days.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.classList.add('calendar-day-header');
                dayHeader.innerText = day;
                calendar.appendChild(dayHeader);
            });

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const today = currentDate.getDate();
            // Update month and year display
            if(monthYear) {

                // Use es-ES for Spanish month names and en-US for English month names
                monthYear.innerText = new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(currentDate);
            }

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            // empyy cells before first day
            for (let i = 0; i < firstDay; i++) {
                calendar.appendChild(document.createElement('div'));
            }

            // month days
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

        // Event listeners for navigation buttons
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

        // Render the calendar for the first time
        renderCalendar();
    }
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

            fetch('index.php?action=update_business_info', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Business information updated successfully!');
                    // Optional: Reload to see new images
                    // location.reload(); 
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Connection error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }
});