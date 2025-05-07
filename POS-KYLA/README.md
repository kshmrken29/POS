# Restaurant POS (Point of Sale) System

A PHP-based restaurant point of sale system for managing menu items, tracking sales, and managing cashiers.

## Features

- **Menu Management**:
  - Add daily menu items with pricing and servings info
  - Edit existing menu details
  - Monitor menu sales with visual indicators

- **Sales Reporting**:
  - Track total sales per menu
  - Overall daily sales reports with charts
  - Performance analytics comparing expected vs. actual sales

- **Cashier Management**:
  - Add, edit, and remove cashier accounts
  - Track cashier status and information

## Setup Instructions

### Requirements
- PHP 7.0 or higher
- MySQL 5.7 or higher
- Web server (Apache or Nginx)
- XAMPP, WAMP, or similar local development stack

### Installation

1. Clone or download this repository to your web server's document root or a subdirectory.

2. Set up the database connection:
   - Open `connection.php` and update the database connection details if needed.
   ```php
   $servername = "localhost";
   $username = "root";  // Your database username
   $password = "";      // Your database password
   $dbname = "restaurantpos";  // Your database name
   ```

3. Create the database:
   - Create a new MySQL database named `restaurantpos` (or use the name you specified in `connection.php`).
   - You can do this using phpMyAdmin or with the following SQL command:
   ```sql
   CREATE DATABASE restaurantpos;
   ```

4. Run the database setup script:
   - Open your web browser and navigate to `http://localhost/path-to-your-installation/create_tables.php`
   - This will create all necessary tables and a default admin user.

5. Access the system:
   - Navigate to `http://localhost/path-to-your-installation/admin/MenuManagement/index.php` to access the admin dashboard.
   - Default admin credentials:
     - Username: `admin`
     - Password: `admin123`

## Usage Guide

### 1. Input Daily Menu
- Add new menu items including name, cost, number of servings, and price per serving.
- The system will automatically calculate expected sales based on servings and price.

### 2. Edit Menu Details
- Select and modify existing menu items.
- Update pricing, servings, or other details as needed.

### 3. Monitor Menu Sales
- View a summary table of all menu items with sales progress.
- Update the number of servings sold for each item.
- See visual indicators of sales performance.

### 4. Sales Reporting
- View detailed sales reports with charts and graphs.
- Compare expected vs. actual sales.
- Filter reports by date.

### 5. Manage Cashiers
- Add new cashier accounts with contact information.
- Edit existing cashier details or update status.
- Delete cashier accounts if needed.

## Security Considerations

- Change the default admin password immediately after installation.
- Implement proper access controls for cashier accounts.
- Consider implementing additional security features like IP restrictions for admin access.

## License

This project is open-source and available under the [MIT License](LICENSE).

## Support

For issues, questions, or contributions, please create an issue in the project repository.