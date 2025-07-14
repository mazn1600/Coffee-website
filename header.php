<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database functions if not already included
if (!function_exists('authenticateUser')) {
    require_once 'db_functions.php';
}

// Debug session
error_log('Current session: ' . print_r($_SESSION, true));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($page_title) ? $page_title . ' | ' : ''; ?>قهوتنا</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Dropdown Menu Styles */
        .user-dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-toggle {
            cursor: pointer;
            display: flex;
            align-items: center;
            color: #5C3D2E;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .dropdown-toggle:hover {
            background-color: rgba(92, 61, 46, 0.1);
        }

        .dropdown-toggle i {
            margin-right: 6px;
            transition: transform 0.3s ease;
        }

        .dropdown-toggle.active i {
            transform: rotate(180deg);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            min-width: 180px;
            padding: 0.5rem 0;
            margin: 0.125rem 0 0;
            background-color: #fff;
            border-radius: 4px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.5rem 1rem;
            clear: both;
            font-weight: 500;
            color: #5C3D2E;
            text-align: inherit;
            text-decoration: none;
            white-space: nowrap;
            background-color: transparent;
            border: 0;
            transition: background-color 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
            color: #4a3122;
        }

        .dropdown-item i {
            margin-left: 8px;
            width: 16px;
        }

        .dropdown-divider {
            height: 0;
            margin: 0.5rem 0;
            overflow: hidden;
            border-top: 1px solid #e9ecef;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">قهوتنا</div>
        <nav>
            <ul>
                <li><a href="index.php">الرئيسية</a></li>
                <li><a href="about.php">من نحن</a></li>
                <li><a href="products.php">منتجاتنا</a></li>
                <li><a href="locations.php">مواقعنا</a></li>
                <li><a href="contacts.php">تواصل معنا</a></li>
                <li><a href="faq.php">الأسئلة الشائعة</a></li>
                <li><a href="cart.php">عربة التسوق</a></li>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin_panel.php">لوحة التحكم</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="auth-buttons">
            <?php if (isset($_SESSION['user_id']) && isset($_SESSION['name'])): ?>
                <div class="user-dropdown">
                    <div class="dropdown-toggle" id="userDropdown">
                        <i class="fas fa-user-circle"></i>
                        <span class="welcome-message">مرحباً، <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        <i class="fas fa-chevron-down" style="font-size: 0.8rem; margin-right: 5px;"></i>
                    </div>
                    <div class="dropdown-menu" id="userDropdownMenu">
                        <a href="last_purchase.php" class="dropdown-item">آخر عملية شراء</a>
                        <a href="my_orders.php" class="dropdown-item">طلباتي</a>
                        <div class="dropdown-divider"></div>
                        <a href="logout.php" class="dropdown-item">تسجيل خروج</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="register.php" class="signup">حساب جديد</a>
                <a href="login.php" class="login">تسجيل دخول</a>
            <?php endif; ?>
        </div>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggle = document.getElementById('userDropdown');
            const dropdownMenu = document.getElementById('userDropdownMenu');
            
            if (dropdownToggle && dropdownMenu) {
                // Toggle dropdown on click
                dropdownToggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdownToggle.classList.toggle('active');
                    dropdownMenu.classList.toggle('show');
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownToggle.classList.remove('active');
                        dropdownMenu.classList.remove('show');
                    }
                });
            }
        });
    </script>
</body>
</html> 