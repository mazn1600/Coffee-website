# قهوتنا - Coffee Shop Website

This is a fully functional coffee shop website with database integration. The website allows users to browse products, register accounts, log in, add items to cart, and complete purchases.

## Features

- User registration and authentication
- Product browsing and filtering
- Shopping cart functionality
- Order processing and confirmation
- Responsive design with Arabic RTL support

## Technical Implementation

- PHP for server-side processing
- MySQL database for data storage
- Session management for user authentication and cart functionality
- Responsive CSS for mobile and desktop compatibility

## Files Structure

- **PHP Files**: All main pages converted to PHP with database integration
- **CSS**: styles.css for website styling
- **Database**: MySQL database with tables for products, users, orders, etc.
- **Images**: Directory for product images

## Setup Instructions

1. **Database Setup**:
   - Create a MySQL database named `coffee_shop`
   - Import the database schema and sample data from the SQL files
   - Verify database connection settings in `db_functions.php`

2. **Web Server Setup**:
   - Copy all files to your web server directory
   - Ensure PHP and MySQL are properly configured
   - Access the website through your web browser

3. **Testing**:
   - Use `test_database.php` to verify database connectivity
   - Test user registration and login functionality
   - Test product browsing and cart operations
   - Test checkout process

## Default Login Credentials

- Username: mohammed
- Password: pass123

## Database Structure

The database includes the following tables:
- `product`: Stores product information
- `beans`: Stores coffee bean specific details
- `machine`: Stores coffee machine specific details
- `user`: Stores user account information
- `order`: Stores order information
- `ordered_item`: Stores items in each order
- `admin`: Stores admin account information

## Contact

For any questions or support, please contact the website administrator.
