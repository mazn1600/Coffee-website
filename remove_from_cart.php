<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['cart'])) {
    // Check if we have a product_id
    if (isset($_POST['product_id'])) {
        $product_id = (int)$_POST['product_id'];
        
        // Validate product_id
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['success'] = 'تم إزالة المنتج من السلة بنجاح';
        } else {
            $_SESSION['error'] = 'المنتج غير موجود في السلة';
        }
    }
    // For backwards compatibility with older code using index
    else if (isset($_POST['index'])) {
        $index = $_POST['index'];
        
        // Validate index
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['success'] = 'تم إزالة المنتج من السلة بنجاح';
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء إزالة المنتج';
        }
    } else {
        $_SESSION['error'] = 'بيانات غير صالحة';
    }
}

header('Location: cart.php');
exit();
?> 