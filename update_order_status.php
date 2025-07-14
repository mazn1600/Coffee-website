<?php
session_start();
require_once 'db_functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

// Check if required parameters are provided
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Order ID and status are required']);
    exit();
}

$order_id = $_POST['order_id'];
$status = $_POST['status'];

// Validate status
$valid_statuses = ['pending', 'paid', 'shipped', 'delivered', 'cancelled'];
if (!in_array($status, $valid_statuses)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid status']);
    exit();
}

// Update order status
$result = updateOrderStatus($order_id, $status);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to update order status']);
}
?> 