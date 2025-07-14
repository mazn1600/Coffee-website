<?php
session_start();
require_once 'db_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Validate inputs
    if ($product_id <= 0 || $quantity <= 0) {
        $_SESSION['error'] = 'Invalid product or quantity';
        header('Location: products.php');
        exit();
    }

    // Get product details from database
    $product = getProductById($product_id);
    if (!$product) {
        $_SESSION['error'] = 'Product not found';
        header('Location: products.php');
        exit();
    }

    // Check stock availability
    if ($quantity > $product['stock']) {
        $_SESSION['error'] = 'Not enough stock available';
        header('Location: products.php');
        exit();
    }

    // Initialize cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Add or update cart item
    $cart_item = [
        'product_id' => $product_id,
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => $quantity,
        'image' => $product['image']
    ];

    // Use product_id as the array key
    if (isset($_SESSION['cart'][$product_id])) {
        // If product already exists, update quantity
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        // Add new product to cart
        $_SESSION['cart'][$product_id] = $cart_item;
    }

    $_SESSION['success'] = 'تمت إضافة المنتج إلى السلة بنجاح';
    header('Location: cart.php');
    exit();
} else {
    header('Location: products.php');
    exit();
}
?> 