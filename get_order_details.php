<?php
session_start();
require_once 'db_functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required']);
    exit();
}

$order_id = intval($_GET['order_id']);

// Get order details
$order = getOrderById($order_id);
if (!$order) {
    http_response_code(404);
    echo json_encode(['error' => 'Order not found']);
    exit();
}

// Get ordered items
$items = getAdminOrderDetails($order_id);

// Make sure we have a consistent field name for the order ID
if (!isset($order['order_id']) && isset($order['id'])) {
    $order['order_id'] = $order['id'];
} elseif (!isset($order['order_id']) && !isset($order['id'])) {
    $order['order_id'] = $order_id;
}

echo json_encode([
    'order' => $order,
    'items' => $items
]); 