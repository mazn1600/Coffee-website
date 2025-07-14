<?php
// Include database functions
require_once 'db_functions.php';

// Copy the db_functions.php file to the modified_files directory
// This is a helper file to test database connectivity
// It will display the results of various database functions to verify they work correctly

// Start session
session_start();

// Set error reporting for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>اختبار قاعدة البيانات | قهوتنا</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    .test-section {
      margin: 20px;
      padding: 20px;
      border: 1px solid #ddd;
      border-radius: 5px;
    }
    .success {
      color: green;
      font-weight: bold;
    }
    .error {
      color: red;
      font-weight: bold;
    }
    pre {
      background-color: #f5f5f5;
      padding: 10px;
      border-radius: 5px;
      overflow-x: auto;
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
            </ul>
        </nav>
        <div class="auth-buttons">
            <a href="register.php" class="signup">حساب جديد</a>
            <a href="login.php" class="login">تسجيل دخول</a>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>اختبار قاعدة البيانات</h1>
            <p>هذه الصفحة تختبر اتصال قاعدة البيانات ووظائفها.</p>
        </div>
    </section>

    <div class="test-section">
        <h2>اختبار الاتصال بقاعدة البيانات</h2>
        <?php
        global $conn;
        if ($conn && !$conn->connect_error) {
            echo '<p class="success">تم الاتصال بقاعدة البيانات بنجاح!</p>';
            echo '<p>معلومات الاتصال: ' . $conn->host_info . '</p>';
            echo '<p>إصدار MySQL: ' . $conn->server_info . '</p>';
        } else {
            echo '<p class="error">فشل الاتصال بقاعدة البيانات: ' . $conn->connect_error . '</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>اختبار استعلام المنتجات</h2>
        <?php
        $products = getAllProducts();
        if (!empty($products)) {
            echo '<p class="success">تم استرجاع ' . count($products) . ' منتج بنجاح!</p>';
            echo '<pre>';
            print_r(array_slice($products, 0, 3)); // Show first 3 products only
            echo '</pre>';
        } else {
            echo '<p class="error">لم يتم العثور على منتجات. تأكد من إعداد قاعدة البيانات بشكل صحيح.</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>اختبار استعلام منتج محدد</h2>
        <?php
        // Try to get the first product if available
        $product_id = !empty($products) ? $products[0]['id'] : 1;
        $product = getProductById($product_id);
        if ($product) {
            echo '<p class="success">تم استرجاع المنتج رقم ' . $product_id . ' بنجاح!</p>';
            echo '<pre>';
            print_r($product);
            echo '</pre>';
        } else {
            echo '<p class="error">لم يتم العثور على المنتج رقم ' . $product_id . '. تأكد من وجود المنتج في قاعدة البيانات.</p>';
        }
        ?>
    </div>

    <div class="test-section">
        <h2>معلومات إضافية</h2>
        <p>للتأكد من عمل الموقع بشكل صحيح، يرجى التأكد من:</p>
        <ul>
            <li>إنشاء قاعدة بيانات باسم "coffee_shop"</li>
            <li>استيراد ملفات SQL لإنشاء الجداول وإدخال البيانات</li>
            <li>التأكد من صحة بيانات الاتصال في ملف db_functions.php</li>
        </ul>
        <p>إذا واجهت أي مشاكل، يرجى مراجعة ملف database_setup_instructions.md للحصول على تعليمات مفصلة.</p>
    </div>

    <footer>
        <div class="copyright">
            <p>© 2023 قهوتنا. جميع الحقوق محفوظة.</p>
        </div>
    </footer>
</body>
</html>
