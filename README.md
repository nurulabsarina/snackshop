# SNACK SHOP MANAGEMENT SYSTEM

This is a web-based admin panel system that allows administrators to manage and control product content based on customer orders.

## Description

The Snack Shop Management System is a comprehensive admin dashboard designed for managing products, inventory, and customer orders efficiently. Administrators can track stock levels, update product details, process orders, and monitor out-of-stock items that require restocking or fulfilment.

Features include:
- Secure authentication (Admin login)
- Interactive dashboard with KPIs and charts
- Product management with stock indicators
- Order management with real-time validation
- Responsive layout for smooth user experience
- Clean UI with modern design principles

This system reflects modern web development practices, focusing on usability, content management, and responsive interface design for IMS566 coursework.

## Getting Started

### Dependencies

- Operating System: Windows 10 or higher (recommended), or any OS supporting XAMPP
- Web Server: XAMPP (includes Apache, MySQL, PHP) or similar PHP/MySQL server
- PHP: Version 8.0 or higher
- MySQL: Version 5.7 or higher
- Web Browser: Google Chrome (recommended), Firefox, or Edge

### Installing

1. Download/Clone the Project:
   - Download the ZIP file from the repository

2. Setup in XAMPP:
   - Extract/copy the project folder to: `C:/xampp/htdocs/`
   - Rename the folder if desired (e.g., `snackshop`)

3. Database Setup:
   - Start XAMPP and ensure Apache and MySQL are running
   - Open phpMyAdmin at `http://localhost/phpmyadmin`
   - Create a new database named `snackshopdb`
   - Import the SQL file if provided, or create tables manually:
     ```sql
     CREATE TABLE admins (
         id INT PRIMARY KEY AUTO_INCREMENT,
         username VARCHAR(50) NOT NULL UNIQUE,
         password VARCHAR(255) NOT NULL
     );

     CREATE TABLE products (
         product_id INT PRIMARY KEY AUTO_INCREMENT,
         product_name VARCHAR(100) NOT NULL,
         price DECIMAL(10,2) NOT NULL,
         original_stock INT NOT NULL,
         image_url VARCHAR(255) DEFAULT 'default.jpg'
     );

     CREATE TABLE orders (
         order_id INT PRIMARY KEY AUTO_INCREMENT,
         customer_name VARCHAR(100) NOT NULL,
         product_name VARCHAR(100) NOT NULL,
         quantity INT NOT NULL,
         total DECIMAL(10,2) NOT NULL,
         order_date DATE NOT NULL
     );
     ```

### Executing program

1. Start Apache & MySQL through XAMPP

2. Open browser and go to:
http://localhost/snackshop/login.php

3. Login using:
- **Username:** admin
- **Password:** admin123

### Help

- Common Issues:
  - If database connection fails, check MySQL service is running and credentials in `conn/db.php`
  - If images don't load, ensure `images/` folder exists and has proper permissions
  - For mobile testing, use browser developer tools to simulate mobile devices

## Authors

**Nurul Absarina Binti Adanan**
- Course: IMS566 - Advanced Web Design Development and Content Management

## Version History

- **1.0** (Current)
  - Initial full release
  - Includes:
    - Admin authentication
    - Dashboard (KPIs + charts)
    - Product management
    - Order management
    - Technologies used: PHP 8+, MySQL, Bootstrap 5, Chart.js

## License

This project is developed for educational purposes as part of IMS566 coursework.

## Acknowledgments

- Bootstrap 5 for responsive design
- Chart.js for data visualizations

- Inspiration from modern admin dashboard structures

