<?php
require_once 'db_functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Validate input
$required_fields = ['name', 'email', 'subject', 'message'];
$data = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing required field: $field"]);
        exit;
    }
    $data[$field] = trim($_POST[$field]);
}

// Validate email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format']);
    exit;
}

// Add optional phone number if provided
if (!empty($_POST['phone'])) {
    $phone = trim($_POST['phone']);
    if (preg_match('/^[0-9]{10}$/', $phone)) {
        $data['phone'] = $phone;
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid phone number format']);
        exit;
    }
}

try {
    $pdo = get_db_connection();
    
    $sql = "INSERT INTO contact_messages (name, email, phone, subject, message, created_at) 
            VALUES (:name, :email, :phone, :subject, :message, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':name' => $data['name'],
        ':email' => $data['email'],
        ':phone' => $data['phone'] ?? null,
        ':subject' => $data['subject'],
        ':message' => $data['message']
    ]);
    
    // Send email notification (you'll need to implement this)
    // send_notification_email($data);
    
    echo json_encode([
        'success' => true,
        'message' => 'تم استلام رسالتك بنجاح. سنقوم بالرد عليك في أقرب وقت ممكن.'
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'حدث خطأ أثناء معالجة طلبك. يرجى المحاولة مرة أخرى لاحقاً.'
    ]);
    
    // Log the error (implement proper error logging)
    error_log("Contact form error: " . $e->getMessage());
} 