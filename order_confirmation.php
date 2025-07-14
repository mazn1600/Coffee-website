<?php
// Include database functions
require_once 'db_functions.php';

// Start session
session_start();

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;

// Check if order ID is provided
if (!isset($_GET['id']) || !$is_logged_in) {
    // Redirect to home page if no order ID or not logged in
    header('Location: index.php');
    exit;
}

$order_id = (int)$_GET['id'];

// Get order details
$order_details = getOrderDetails($order_id, $user_id);

// If order not found or doesn't belong to user, redirect to home
if (!$order_details) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>تأكيد الطلب | قهوتنا</title>
  <link rel="stylesheet" href="styles.css" />
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
            </ul>
        </nav>
        <div class="auth-buttons">
            <?php if ($is_logged_in): ?>
                <span class="welcome-message">مرحباً <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                <a href="logout.php" class="logout">تسجيل خروج</a>
            <?php else: ?>
                <a href="register.php" class="signup">حساب جديد</a>
                <a href="login.php" class="login">تسجيل دخول</a>
            <?php endif; ?>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>تأكيد الطلب</h1>
            <p>شكراً لطلبك! تم استلام طلبك بنجاح.</p>
        </div>
    </section>

    <section class="order-confirmation">
        <div class="order-details">
            <h2>تفاصيل الطلب</h2>
            <p><strong>رقم الطلب:</strong> <?php echo htmlspecialchars($order_details['order']['id']); ?></p>
            <p><strong>تاريخ الطلب:</strong> <?php echo htmlspecialchars($order_details['order']['date']); ?></p>
            <p><strong>حالة الطلب:</strong> <?php echo htmlspecialchars($order_details['order']['status']); ?></p>
            <p><strong>المجموع الكلي:</strong> <?php echo htmlspecialchars($order_details['order']['total_price']); ?> ريال</p>
        </div>

        <div class="order-items">
            <h2>المنتجات المطلوبة</h2>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>السعر</th>
                        <th>الكمية</th>
                        <th>المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_details['items'] as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo htmlspecialchars($item['price']); ?> ريال</td>
                            <td><?php echo htmlspecialchars($item['amount']); ?></td>
                            <td><?php echo htmlspecialchars($item['subtotal']); ?> ريال</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="order-actions">
            <a href="products.php" class="btn">متابعة التسوق</a>
        </div>
    </section>

    <footer>
        <div class="copyright">
            <p>© 2023 قهوتنا. جميع الحقوق محفوظة.</p>
        </div>
    </footer>
</body>
</html>
