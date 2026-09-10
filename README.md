# 🧋 CadienTea Shop Website

A fully functional e-commerce website for CadienTea bubble tea shop with customer ordering, pickup/delivery options, and a complete admin panel.

---

## 📋 Features

### 👤 Customer Features
- 🧋 Browse products with categories and sizes
- 🛒 Shopping cart system with quantity management
- 👤 User registration and secure login
- 📦 Order placement with **Pickup** or **Delivery** option
- 💬 Send messages to the seller and view replies
- 📜 View order history and purchase details
- 📱 Fully responsive design

### 👑 Admin Features
- 📊 Dashboard with statistics (revenue, orders, products, users, messages)
- 📦 Order management with status updates
- 🧋 Product management with sizes and prices (CRUD)
- 🏷️ Category management
- 👤 User management with role control
- 💬 Message inbox with reply system
- 👁 Quick "View Store" button to switch to customer view

---

## 🛠️ Technologies Used

- **PHP** - Backend logic
- **MySQL** - Database
- **HTML/CSS** - Frontend design
- **JavaScript** - Interactive features
- **XAMPP** - Local development server

---

## 📂 Project Structure
cadientea/
├── admin/ # Admin panel files
│ ├── index.php # Dashboard with stats
│ ├── login.php # Admin login
│ ├── login_function.php # Admin login handler
│ ├── orders.php # Order management
│ ├── order_detail.php # View single order
│ ├── update_status.php # Update order status
│ ├── products.php # Product management
│ ├── add_product.php # Add product with sizes
│ ├── save_product.php # Save product handler
│ ├── edit_product.php # Edit product
│ ├── update_product.php # Update product handler
│ ├── delete_product.php # Delete product
│ ├── categories.php # Category management
│ ├── add_category.php # Add category
│ ├── delete_category.php # Delete category
│ ├── users.php # User management
│ ├── edit_user.php # Edit user
│ ├── update_user.php # Update user handler
│ └── messages.php # Message inbox with replies
├── images/ # Product images
├── uploads/ # Uploaded images
├── index.php # Landing page
├── menu.php # Full menu page
├── about.php # About page
├── community.php # Community/testimonials page
├── contact.php # Contact page
├── login.php # Customer login
├── login_function.php # Customer login handler
├── register.php # Customer registration
├── register_function.php # Registration handler
├── logout.php # Logout handler
├── success.php # Post-login success page
├── info.php # User account page
├── purchases.php # Order history
├── messages.php # Customer message center
├── cart.php # Shopping cart
├── add_to_cart.php # Add to cart handler
├── remove_from_cart.php # Remove from cart
├── update_cart.php # Update cart quantity
├── checkout.php # Checkout with pickup/delivery
├── process_order.php # Order processor
├── order_success.php # Order confirmation
├── function.php # Core functions
├── validation.php # Validation functions
├── db.php # Database connection
├── style.css # Main stylesheet
├── javascript.js # Main JavaScript
├── navbar.php # Reusable navbar
├── footer.php # Reusable footer
├── admin_bar.php # Admin bar (for logged-in admins)
└── README.md # Project documentation

text

---

## 🚀 Installation

1. **Clone the repository:**
```bash
git clone https://github.com/JODEAN13/cadientea-shop.git
Import the database:

Open phpMyAdmin
Create a database named cadientea_db
Import the SQL file from the database/ folder (or run the SQL manually)
Configure database:
Update db.php with your database credentials
Run the project:
Start XAMPP (Apache and MySQL)
Visit: http://localhost/cadientea/

🗄️ Database Tables
    Table	                  Purpose
users	                  Customer and admin accounts
categories	              Product categories
products	              Product listings
product_sizes   	      Sizes and prices per product
orders	                  Order records
order_items	              Items in each order
messages	              Customer-admin messages

✨ Key Features
🛒 Shopping Cart & Checkout
Add products with size selection
Update quantities or remove items
Choose between Pickup (free) or Delivery (₱50 fee)
Payment method selection (Cash, GCash, PayMaya, Bank Transfer)

📦 Order Management
Order statuses: pending → confirmed → preparing → ready → out_for_delivery → completed
Cancellation option
Order type tracking (Pickup/Delivery)

💬 Messaging System
Customers can send messages with subjects
Admin sees messages with unread badges
Two-way conversation thread
Contact info displayed for both parties

🧋 Product Management
Add products with multiple sizes and prices
Upload product images
Mark as available/unavailable
Tag products (Best Seller, New, etc.)

🔑 Default Users
⚠️ For security reasons, default credentials are not listed here.
Please contact the developer for test credentials.

📝 License
This project is for educational purposes only.

👨‍💻 Developer
Jode-An P. Cadiente
📧 cadientejodean4@gmail.com
🐙 GitHub: JODEAN13

Sip the glow, love the flow! 🧋