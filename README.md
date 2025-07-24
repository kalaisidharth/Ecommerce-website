Step-by-Step Process to Run the Project Locally and Upload to GitHub
🔧 1. Setup Local Development Environment
Required Tools:
XAMPP or WAMP

A code editor (VS Code, Sublime Text, etc.)

Git

Steps:
Install XAMPP

Start Apache and MySQL from the XAMPP Control Panel

📁 2. Project Directory Structure
Create a folder inside the htdocs directory of XAMPP (e.g., ecommerce_site) and place all your project files there.

Example structure:

cpp
Copy
Edit
htdocs/
└── ecommerce_site/
    ├── index.php
    ├── login.php
    ├── register.php
    ├── logout.php
    ├── db.php
    ├── dashboard.php
    ├── add_product.php
    ├── manage_products.php
    ├── view_orders.php
    ├── view_users.php
    ├── uploads/
    │   └── (product images will be stored here)
    ├── css/
    ├── js/
    └── ...
🗄️ 3. Create the MySQL Database
Open your browser → Go to http://localhost/phpmyadmin

Click New, name your database: ecommerce_db

Run the SQL script to create tables:

sql code:
-- USERS
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('admin', 'user') DEFAULT 'user'
);

-- PRODUCTS
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    description TEXT,
    price DECIMAL(10,2),
    image VARCHAR(255),
    stock INT DEFAULT 0
);

-- ORDERS
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total_amount DECIMAL(10,2),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ORDER_ITEMS
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
⚙️ 4. Set Up the Database Connection
Edit your db.php:

php code:
<?php
$conn = new mysqli("localhost", "root", "", "ecommerce_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
🧪 5. Testing
Visit your site: http://localhost/ecommerce_site

Register as a user or admin (role can be updated manually in phpMyAdmin)

Login → Try adding products, managing them, viewing users and orders

🆙 6. Uploading to GitHub
Step 1: Initialize Git
Open terminal or Git Bash in the project folder:


>cd path/to/htdocs/ecommerce_site
>git init

Step 2: Create .gitignore and write the following inside it

/uploads/
*.log

Step 3: Commit Code

>git add .
>git commit -m "Initial commit - E-commerce Website"
Step 4: Push to GitHub
Go to GitHub → Create a new repository

Copy the URL from GitHub, then push:

>git remote add origin https://github.com/yourusername/ecommerce-site.git
>git branch -M main
>git push -u origin main

🔐 7. (Optional) Add Admin User in DB
Go to phpMyAdmin → users table → Insert a row manually:


name: Admin
email: admin@example.com
password: [Hash this using PHP password_hash()]
role: admin
Use PHP to generate a hashed password:

php code:
<?php echo password_hash("admin123", PASSWORD_DEFAULT); ?>
🎉 Done!
Now your eCommerce website:

Works locally via localhost/ecommerce_site

Has a full admin dashboard

Is uploaded to GitHub

