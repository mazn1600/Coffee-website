<?php
session_start();
require_once 'db_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'يرجى تسجيل الدخول أولاً';
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if we have a product_id
    if (isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

        // Validate inputs
        if ($product_id > 0 && $quantity > 0) {
            // Check if product exists in cart
            if (isset($_SESSION['cart'][$product_id])) {
                // Update quantity
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                $_SESSION['success'] = 'تم تحديث الكمية بنجاح';
            } else {
                $_SESSION['error'] = 'المنتج غير موجود في السلة';
            }
        } else {
            $_SESSION['error'] = 'بيانات غير صالحة';
        }
    } 
    // For backwards compatibility with older code using index
    else if (isset($_POST['index'])) {
        $index = $_POST['index'];
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;
        
        // Validate inputs
        if (isset($_SESSION['cart'][$index]) && $quantity > 0) {
            // Update quantity
            $_SESSION['cart'][$index]['quantity'] = $quantity;
            $_SESSION['success'] = 'تم تحديث الكمية بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء تحديث الكمية';
        }
    } else {
        $_SESSION['error'] = 'بيانات غير صالحة';
    }
}

header('Location: cart.php');
exit();
?> 