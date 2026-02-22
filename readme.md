# EasyPoint - Beauty & Wellness Appointment System

EasyPoint is a comprehensive web-based platform designed to connect clients with local beauty and wellness professionals. Whether you are looking for a quick haircut, a relaxing massage, or want to manage your own salon's bookings, EasyPoint makes the process simple, fast, and free.

## 🚀 Features

### For Clients (Users)
* **Discover Professionals:** Search for health and beauty businesses by service, location, or category (Hair Salon, Barbershop, Nail Salon, Skincare, etc.).
* **Book Appointments:** Easily schedule appointments with your favorite professionals.
* **Manage Bookings:** View, track, and cancel your upcoming appointments through a personalized dashboard.
* **Verified Reviews:** Read and write reviews to make informed decisions based on real experiences.
* **Secure Authentication:** Secure login, registration with email verification, and password recovery.

### For Businesses (Stores)
* **Business Profile Management:** Create and customize a public business page with a logo, banner, and description.
* **Service Menu:** Add, edit, and manage the services offered, including pricing and duration.
* **Opening Hours:** Define and update your business's weekly schedule.
* **Portfolio/Gallery:** Upload images to showcase your work and attract more clients.
* **Appointment Tracking:** Receive and manage customer bookings directly from your dashboard.

## 🛠️ Built With

* **Frontend:** HTML5, CSS3, JavaScript, [Bootstrap 5.3](https://getbootstrap.com/), [FontAwesome 6](https://fontawesome.com/)
* **Backend:** PHP 8 (Custom MVC Architecture)
* **Database:** MySQL
* **Libraries & APIs:** * [EmailJS](https://www.emailjs.com/) (for client-side email notifications)
  * [PHPMailer](https://github.com/PHPMailer/PHPMailer) (for secure backend email handling)
* **Environment:** Docker support available (`docker-compose.yml`)

## 📂 Project Structure

```text
EasyPoint/
├── config/             # Database and environment configurations
├── controllers/        # PHP Controllers (Auth, Booking, User, Review)
├── models/             # Database interaction logic (User, Service, Database)
├── public/             # Static assets (CSS, JS, Images, Uploads)
├── sql/                # Database schema and mock data (easy_point.sql)
├── views/              # UI templates and layout files
├── index.php           # Main entry point and router
└── docker-compose.yml  # Docker environment configuration
```
## ⚙️ Installation & Setup
 ### Option 1: Using XAMPP / WAMP / MAMP
Clone the repository to your local server directory (e.g., htdocs or www).

Database Setup: * Open phpMyAdmin.

Create a new database named easy_point.

Import the provided SQL file located at sql/easy_point.sql.

 Configuration:

Navigate to config/config.php.

Update the database credentials (DB_HOST, DB_USER, DB_PASS) to match your local environment.

Run the App: Open your browser and go to http://localhost/EasyPoint.

### Option 2: Using Docker
Ensure you have Docker and Docker Compose installed.

Navigate to the project root directory in your terminal.

Run the following command to build and start the containers:

Bash<- docker-compose up -d ->

Access the application at http://localhost. (Note: Database credentials inside config.php should use 'db' as the host when running via Docker).

## 🔒 Environment Variables & Credentials
For full functionality (like email sending), ensure you configure your SMTP credentials and EmailJS public keys.

EmailJS: Replace the public key in index.php and script.js with your actual key.

PHPMailer: Update the SMTP credentials inside controllers/UserController.php (sendEmail function).

## 🤝 Contributing
Contributions, issues, and feature requests are welcome! Feel free to check the issues page.

## 📝 License
This project is for educational and portfolio purposes.
