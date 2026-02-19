// Global state
let currentMonth = new Date();
let selectedDate = null;
let selectedTime = null;
let selectedEmployee = null;

// Helper function to convert date to YYYY-MM-DD without timezone issues
function dateToYMD(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    renderCalendar();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    document.getElementById('prevMonth').addEventListener('click', previousMonth);
    document.getElementById('nextMonth').addEventListener('click', nextMonth);
}

// CALENDAR FUNCTIONS
function renderCalendar() {
    const year = currentMonth.getFullYear();
    const month = currentMonth.getMonth();

    // Update month display
    const monthName = new Date(year, month).toLocaleDateString('es-ES', { month: 'long', year: 'numeric' });
    document.getElementById('currentMonth').textContent = 
        monthName.charAt(0).toUpperCase() + monthName.slice(1);

    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay() || 7; // 1-7, where 1 is Monday
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const daysInPrevMonth = new Date(year, month, 0).getDate();

    const daysGrid = document.getElementById('daysGrid');
    daysGrid.innerHTML = '';

    // Previous month's days
    for (let i = firstDay - 1; i > 0; i--) {
        const dayBtn = createDayButton(null, daysInPrevMonth - i + 1, false);
        dayBtn.classList.add('disabled');
        daysGrid.appendChild(dayBtn);
    }

    // Current month's days
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        const isToday = date.getTime() === new Date(today.getFullYear(), today.getMonth(), today.getDate()).getTime();
        const isPast = date < new Date(today.getFullYear(), today.getMonth(), today.getDate());
        const isOpen = isDayOpen(date);

        const dayBtn = createDayButton(date, day, isOpen);
        
        if (isPast) {
            dayBtn.classList.add('unavailable');
        } else if (isOpen) {
            dayBtn.classList.add('available');
        } else {
            dayBtn.classList.add('unavailable');
        }

        if (selectedDate && selectedDate.getTime() === date.getTime()) {
            dayBtn.classList.add('selected');
        }

        dayBtn.addEventListener('click', () => selectDate(date, dayBtn));
        daysGrid.appendChild(dayBtn);
    }

    // Next month's days
    const totalCells = daysGrid.children.length;
    const cellsToAdd = 42 - totalCells;
    for (let i = 1; i <= cellsToAdd; i++) {
        const dayBtn = createDayButton(null, i, false);
        dayBtn.classList.add('disabled');
        daysGrid.appendChild(dayBtn);
    }
}

function createDayButton(date, day, isOpen) {
    const btn = document.createElement('button');
    btn.className = 'day-btn';
    btn.textContent = day;
    btn.type = 'button';
    return btn;
}

function isDayOpen(date) {
    const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    const dayName = days[date.getDay()];

    if (!STORE_DATA.opening_hours || !STORE_DATA.opening_hours[dayName]) {
        return false;
    }

    const dayHours = STORE_DATA.opening_hours[dayName];
    return dayHours.active && dayHours.open && dayHours.close;
}

function previousMonth() {
    currentMonth.setMonth(currentMonth.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentMonth.setMonth(currentMonth.getMonth() + 1);
    renderCalendar();
}

function selectDate(date, button) {
    document.querySelectorAll('.day-btn.selected').forEach(btn => {
        btn.classList.remove('selected');
    });

    button.classList.add('selected');
    selectedDate = date;
    selectedTime = null; 
    selectedEmployee = null;

    const dateStr = date.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('selectedDate').textContent = dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
    document.getElementById('selectedTime').textContent = '-';

    // Make a request to the API to see which times the whole store booked
    const dateFormatted = dateToYMD(date);
    
    fetch('index.php?action=get-booked-slots', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ service_id: SERVICE_DATA.id, date: dateFormatted })
    })
    .then(res => res.json())
    .then(data => {
        let bookedTimes = [];
        if (data.success && data.booked_times) {
        // Convert DB data "10:30:00" to button format "10:30"
            bookedTimes = data.booked_times.map(t => t.substring(0, 5));
        }
        generateTimeSlots(date, bookedTimes);
        
        document.getElementById('timeSlotsSection').style.display = 'block';
        document.getElementById('selectedInfo').style.display = 'block';
    })
    .catch(err => {
        console.error('Error fetching slots:', err);
        generateTimeSlots(date, []); 
        document.getElementById('timeSlotsSection').style.display = 'block';
        document.getElementById('selectedInfo').style.display = 'block';
    });
}

function generateTimeSlots(date, bookedTimes = []) {
    const days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
    const dayName = days[date.getDay()];
    const dayHours = STORE_DATA.opening_hours[dayName];

    document.getElementById('morningSlots').innerHTML = '';
    document.getElementById('afternoonSlots').innerHTML = '';
    document.getElementById('eveningSlots').innerHTML = '';

    if (!dayHours.active) return;

    const [openHour, openMinute] = dayHours.open.split(':').map(Number);
    const [closeHour, closeMinute] = dayHours.close.split(':').map(Number);
    const duration = SERVICE_DATA.duration; // Duration of the service the client wants to book

    const openTotalMinutes = openHour * 60 + openMinute;
    const closeTotalMinutes = closeHour * 60 + closeMinute;

    for (let totalMinutes = openTotalMinutes; totalMinutes + duration <= closeTotalMinutes; totalMinutes += 30) {
        const hours = Math.floor(totalMinutes / 60);
        const minutes = totalMinutes % 60;
        const timeStr = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`;

    // Check if any fragment of the minutes we will use overlaps with booked times.
        let isOverlapping = false;
        for (let i = 0; i < duration; i += 30) {
            let checkMin = totalMinutes + i;
            let chH = Math.floor(checkMin / 60);
            let chM = checkMin % 60;
            let chStr = `${String(chH).padStart(2, '0')}:${String(chM).padStart(2, '0')}`;
            
            if (bookedTimes.includes(chStr)) {
                isOverlapping = true;
                break;
            }
        }

        const slot = createTimeSlot(timeStr, date, isOverlapping);
        const hour = hours;

        if (hour < 12) {
            document.getElementById('morningSlots').appendChild(slot);
        } else if (hour < 18) {
            document.getElementById('afternoonSlots').appendChild(slot);
        } else {
            document.getElementById('eveningSlots').appendChild(slot);
        }
    }
}

function createTimeSlot(time, date, isBooked) {
    const btn = document.createElement('button');
    btn.className = 'time-slot';
    btn.type = 'button';
    btn.textContent = time;

    const [hours, minutes] = time.split(':').map(Number);
    const slotDate = new Date(date);
    slotDate.setHours(hours, minutes, 0, 0);

    // If the slot time has already passed OR it collides with booked times, disable it
    if (slotDate < new Date() || isBooked) {
        btn.classList.add('disabled');
        btn.disabled = true;
    } else {
        btn.addEventListener('click', () => selectTime(time, btn, date));
    }

    return btn;
}
function selectTime(time, button, date) {
    // Remove previous selection
    document.querySelectorAll('.time-slot.selected').forEach(btn => {
        btn.classList.remove('selected');
    });

    button.classList.add('selected');
    selectedTime = time;

    // Update display
    document.getElementById('selectedTime').textContent = time;

    // Show employees (mock)
    showEmployeesSelection();
}

function showEmployeesSelection() {
    const employeeSection = document.getElementById('employeeSection');
    const employeeGrid = document.getElementById('employeeGrid');

    // Mock employees - in real app, fetch from API
    const employees = [
        { id: 1, name: 'Peluquero 1' },
        { id: 2, name: 'Peluquero 2' },
        { id: 3, name: 'Peluquero 1' }
    ];

    employeeGrid.innerHTML = '';

    employees.forEach((employee, index) => {
        const btn = document.createElement('button');
        btn.className = 'employee-btn';
        btn.type = 'button';
        btn.innerHTML = `
            <div class="employee-avatar">${employee.name.charAt(0)}</div>
            <span>${employee.name}</span>
        `;

        btn.addEventListener('click', () => selectEmployee(employee.id, btn));
        employeeGrid.appendChild(btn);
    });

    employeeSection.style.display = 'block';
}

function selectEmployee(id, button) {
    document.querySelectorAll('.employee-btn.selected').forEach(btn => {
        btn.classList.remove('selected');
    });

    button.classList.add('selected');
    selectedEmployee = id;
}

// BOOKING FLOW
function proceedToConfirmation() {
    if (!selectedDate || !selectedTime) {
        showToast('Please select a date and time', 'warning');
        return;
    }

    // Check if user is logged in
    if (!USER_LOGGED) {
        // Store current URL to return after login
        sessionStorage.setItem('returnUrl', window.location.href);
        // Open auth modal using the existing function from script.js
        const authModal = document.getElementById('auth-modal');
        if (authModal) {
            authModal.style.display = 'flex';
        }
        return;
    }

    // Show confirmation modal
    updateConfirmationModal();
    document.getElementById('confirmationModal').style.display = 'flex';
}

function updateConfirmationModal() {
    const dateStr = selectedDate.toLocaleDateString('es-ES', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    document.getElementById('confirmDateTime').textContent = 
        dateStr.charAt(0).toUpperCase() + dateStr.slice(1) + ' at ' + selectedTime;
}

function closeConfirmationModal() {
    document.getElementById('confirmationModal').style.display = 'none';
}

function cancelBooking() {
    // Show cancel confirmation modal
    document.getElementById('confirmationModal').style.display = 'none';
    document.getElementById('cancelConfirmModal').style.display = 'flex';
}

function confirmCancel() {
    // Reset selection and close modal
    selectedDate = null;
    selectedTime = null;
    selectedEmployee = null;
    document.getElementById('cancelConfirmModal').style.display = 'none';
    document.getElementById('timeSlotsSection').style.display = 'none';
    document.getElementById('selectedInfo').style.display = 'none';
    document.getElementById('employeeSection').style.display = 'none';
    renderCalendar();
}

function continueProceedBooking() {
    document.getElementById('cancelConfirmModal').style.display = 'none';
    document.getElementById('confirmationModal').style.display = 'flex';
}

function confirmBooking() {
    // Validate user role - only clients can book appointments
    if (USER_ROLE === 'store') {
        document.getElementById('confirmationModal').style.display = 'none';
        showToast('Businesses cannot book appointments. Only clients can make reservations.', 'error');
        return;
    }

    // Show spinner
    document.getElementById('loadingSpinner').style.display = 'block';
    document.getElementById('confirmationModal').style.display = 'none';

    // Prepare data
    const [hours, minutes] = selectedTime.split(':').map(Number);
    const appointmentTime = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:00`;

    const data = {
        user_id: USER_ID,
        service_id: SERVICE_DATA.id,
        appointment_date: dateToYMD(selectedDate),
        appointment_time: appointmentTime,
        status: 'pending',
        notes: ''
    };

    // Send to server
    fetch('index.php?action=create-appointment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        document.getElementById('loadingSpinner').style.display = 'none';

        if (result.success) {
            showToast('Your appointment has been successfully booked!', 'success');
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);
        } else {
            showToast('Error booking appointment: ' + result.message, 'error');
        }
    })
    .catch(error => {
        document.getElementById('loadingSpinner').style.display = 'none';
        console.error('Error:', error);
        showToast('An error occurred while booking', 'error');
    });
}

// Toast and closing functions are handled by script.js

// TOAST NOTIFICATION
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.className = 'toast show ' + type;

    setTimeout(() => {
        toast.classList.remove('show');
    }, 3000);
}

// Close modals on outside click
document.addEventListener('click', function (event) {
    const confirmModal = document.getElementById('confirmationModal');
    if (confirmModal && event.target === confirmModal) {
        closeConfirmationModal();
    }

    const cancelModal = document.getElementById('cancelConfirmModal');
    if (cancelModal && event.target === cancelModal) {
        confirmCancel();
    }
});
