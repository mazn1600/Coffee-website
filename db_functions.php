<?php
// Database connection configuration
$db_host = 'localhost:8889';  
$db_user = 'root';
$db_password = 'root';
$db_name = 'coffee_shop';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8mb4");

// Function to get all products
function getAllProducts() {
    global $conn;
    $sql = "SELECT * FROM products ORDER BY product_id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Function to get product by ID
function getProductById($id) {
    global $conn;
    $id = $conn->real_escape_string($id);
    
    $sql = "SELECT * FROM products WHERE product_id = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

// Function to authenticate user
function authenticateUser($email, $password) {
    global $conn;
    $email = $conn->real_escape_string($email);
    
    // First check if user exists with this email
    $sql = "SELECT * FROM users WHERE email = '$email' AND role = 'customer'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verify the password matches for this specific email
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }
    
    return false;
}

// Function to register new user
function registerUser($name, $email, $password, $role = 'customer') {
    global $conn;
    
    // Escape input
    $name = $conn->real_escape_string($name);
    $email = $conn->real_escape_string($email);
    $role = $conn->real_escape_string($role);
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if email already exists
    $check_sql = "SELECT * FROM users WHERE email = '$email'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        return false; // User already exists
    }
    
    // Insert new user
    $sql = "INSERT INTO users (name, email, password, role) 
            VALUES ('$name', '$email', '$hashed_password', '$role')";
    
    if ($conn->query($sql) === TRUE) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

// Function to create a new order
function createOrder($user_id, $products, $shipping_address, $payment_method) {
    global $conn;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Calculate total price
        $total_price = 0;
        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $quantity = $product['quantity'];
            
            // Check if product_id is valid (it should be an integer)
            if (!is_numeric($product_id)) {
                throw new Exception("Invalid product ID: $product_id");
            }
            
            // Get product price
            $price_sql = "SELECT price, stock FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($price_sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $product_data = $result->fetch_assoc();
                $price = $product_data['price'];
                $stock = $product_data['stock'];
                
                // Check if enough stock
                if ($stock < $quantity) {
                    throw new Exception("Not enough stock for product ID: $product_id");
                }
                
                $total_price += ($price * $quantity);
                
                // Update stock
                $update_stock = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
                $stmt = $conn->prepare($update_stock);
                $stmt->bind_param("ii", $quantity, $product_id);
                $stmt->execute();
            } else {
                throw new Exception("Product not found: $product_id");
            }
        }
        
        // Create order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, shipping_address, payment_method, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->bind_param("idss", $user_id, $total_price, $shipping_address, $payment_method);
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Add ordered items
        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $quantity = $product['quantity'];
            
            // Get product price
            $price_sql = "SELECT price FROM products WHERE product_id = ?";
            $stmt = $conn->prepare($price_sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $price_data = $result->fetch_assoc();
            $unit_price = $price_data['price'];
            
            $item_sql = "INSERT INTO ordered_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($item_sql);
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $unit_price);
            $stmt->execute();
        }
        
        // Commit transaction
        $conn->commit();
        return $order_id;
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        // For debugging:
        error_log("Order creation failed: " . $e->getMessage());
        return false;
    }
}

// Function to get user orders
function getUserOrders($user_id) {
    global $conn;
    $user_id = $conn->real_escape_string($user_id);
    
    $sql = "SELECT o.*, 
            (SELECT COUNT(*) FROM ordered_items WHERE order_id = o.order_id) as item_count 
            FROM orders o 
            WHERE o.user_id = '$user_id' 
            ORDER BY o.order_date DESC";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result->fetch_all(MYSQLI_ASSOC);
    } else {
        return [];
    }
}

// Function to get order details
function getOrderDetails($order_id, $user_id = null) {
    global $conn;
    $order_id = $conn->real_escape_string($order_id);
    
    $sql = "SELECT o.*, oi.product_id, oi.quantity, oi.unit_price, p.name 
            FROM orders o 
            JOIN ordered_items oi ON o.order_id = oi.order_id 
            JOIN products p ON oi.product_id = p.product_id 
            WHERE o.order_id = '$order_id'";
    
    // If user_id is provided, ensure order belongs to user
    if ($user_id) {
        $user_id = $conn->real_escape_string($user_id);
        $sql .= " AND o.user_id = '$user_id'";
    }
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $order = null;
        $items = [];
        
        while ($row = $result->fetch_assoc()) {
            if (!$order) {
                $order = [
                    'order_id' => $row['order_id'],
                    'user_id' => $row['user_id'],
                    'total_price' => $row['total_price'],
                    'shipping_address' => $row['shipping_address'],
                    'payment_method' => $row['payment_method'],
                    'status' => $row['status'],
                    'order_date' => $row['order_date']
                ];
            }
            
            $items[] = [
                'product_id' => $row['product_id'],
                'name' => $row['name'],
                'unit_price' => $row['unit_price'],
                'quantity' => $row['quantity'],
                'subtotal' => $row['unit_price'] * $row['quantity']
            ];
        }
        
        return [
            'order' => $order,
            'items' => $items
        ];
    } else {
        return null;
    }
}

// Function to authenticate admin
function authenticateAdmin($email, $password) {
    global $conn;
    $email = $conn->real_escape_string($email);
    
    // First check if user exists with this email and is an admin
    $sql = "SELECT * FROM users WHERE email = '$email' AND role = 'admin'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        // Verify the password matches for this specific email
        if (password_verify($password, $admin['password'])) {
            return $admin;
        }
    }
    
    return false;
}

// Function to add a new product
function addProduct($name, $description, $price, $stock, $image, $type, $extra_info = null) {
    global $conn;
    
    // Validate type
    if (!in_array($type, ['beans', 'machine'])) {
        return false;
    }
    
    $stmt = $conn->prepare("INSERT INTO products (name, description, price, stock, image, type, extra_info) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdisss", $name, $description, $price, $stock, $image, $type, $extra_info);
    
    if ($stmt->execute()) {
        return $conn->insert_id;
    } else {
        return false;
    }
}

// Function to update a product
function updateProduct($product_id, $name, $description, $price, $stock, $image = null, $type = null, $extra_info = null) {
    global $conn;
    
    $product_id = $conn->real_escape_string($product_id);
    $name = $conn->real_escape_string($name);
    $description = $conn->real_escape_string($description);
    $image = $image ? $conn->real_escape_string($image) : null;
    $type = $type ? $conn->real_escape_string($type) : null;
    $extra_info = $extra_info ? $conn->real_escape_string($extra_info) : null;
    
    $updates = [];
    $updates[] = "name = '$name'";
    $updates[] = "description = '$description'";
    $updates[] = "price = '$price'";
    $updates[] = "stock = '$stock'";
    
    if ($image) {
        $updates[] = "image = '$image'";
    }
    if ($type) {
        $updates[] = "type = '$type'";
    }
    if ($extra_info) {
        $updates[] = "extra_info = '$extra_info'";
    }
    
    $sql = "UPDATE products SET " . implode(", ", $updates) . " WHERE product_id = '$product_id'";
    
    return $conn->query($sql) === TRUE;
}

// Function to delete a product
function deleteProduct($product_id) {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    return $stmt->execute();
}

// Admin Functions
function getAllOrders() {
    global $conn;
    try {
        $query = "SELECT o.*, u.name as customer_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                ORDER BY o.order_date DESC";
        $result = $conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    } catch (Exception $e) {
        error_log("Error fetching orders: " . $e->getMessage());
        return [];
    }
}

// Function to get an order by ID for admin
function getOrderById($order_id) {
    global $conn;
    try {
        $query = "SELECT o.*, u.name as customer_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                WHERE o.order_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    } catch (Exception $e) {
        error_log("Error fetching order by ID: " . $e->getMessage());
        return null;
    }
}

function getAdminOrderDetails($order_id) {
    global $conn;
    try {
        $query = "SELECT oi.*, p.name as product_name, p.price 
                FROM ordered_items oi 
                JOIN products p ON oi.product_id = p.product_id 
                WHERE oi.order_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_all(MYSQLI_ASSOC);
        } else {
            return [];
        }
    } catch (Exception $e) {
        error_log("Error fetching order details: " . $e->getMessage());
        return [];
    }
}

function updateOrderStatus($order_id, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $order_id);
    return $stmt->execute();
}

function getAdminProductById($product_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to save cart items for a user
function saveCartItems($user_id, $cart_items) {
    global $conn;
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // First, clear existing cart items for this user
        $clear_sql = "DELETE FROM cart_items WHERE user_id = ?";
        $stmt = $conn->prepare($clear_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Then insert new cart items
        $insert_sql = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        
        foreach ($cart_items as $item) {
            $stmt->bind_param("iii", $user_id, $item['product_id'], $item['quantity']);
            $stmt->execute();
        }
        
        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        return false;
    }
}

// Function to get cart items for a user
function getCartItems($user_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT ci.*, p.name, p.price, p.image, p.description, p.stock 
        FROM cart_items ci 
        JOIN products p ON ci.product_id = p.product_id 
        WHERE ci.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    return $items;
}

function getCartItem($user_id, $product_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT * FROM cart_items 
        WHERE user_id = ? AND product_id = ?
    ");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function updateCartItem($user_id, $product_id, $quantity) {
    global $conn;
    $stmt = $conn->prepare("
        UPDATE cart_items 
        SET quantity = ? 
        WHERE user_id = ? AND product_id = ?
    ");
    $stmt->bind_param("iii", $quantity, $user_id, $product_id);
    return $stmt->execute();
}

function removeFromCart($user_id, $product_id) {
    global $conn;
    $stmt = $conn->prepare("
        DELETE FROM cart_items 
        WHERE user_id = ? AND product_id = ?
    ");
    $stmt->bind_param("ii", $user_id, $product_id);
    return $stmt->execute();
}

function addToCart($user_id, $product_id, $quantity = 1) {
    global $conn;
    
    // Check if item already exists in cart
    $existing_item = getCartItem($user_id, $product_id);
    
    if ($existing_item) {
        // Update quantity if item exists
        return updateCartItem($user_id, $product_id, $existing_item['quantity'] + $quantity);
    } else {
        // Add new item to cart
        $stmt = $conn->prepare("
            INSERT INTO cart_items (user_id, product_id, quantity) 
            VALUES (?, ?, ?)
        ");
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        return $stmt->execute();
    }
}

// Close connection when script ends
// $conn->close();
?>
