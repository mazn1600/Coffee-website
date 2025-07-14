<?php
session_start();
require_once 'db_functions.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle product deletion
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    deleteProduct($product_id);
}

// Handle product addition
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $type = $_POST['type']; // Changed from category to type
    
    // Create uploads directory if it doesn't exist
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    // Handle image upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $target_file = $target_dir . time() . '_' . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $target_file;
        }
    }
    
    // Add product with proper type (beans or machine)
    $result = addProduct($name, $description, $price, $stock, $image, $type);
    if ($result) {
        header('Location: admin_panel.php?success=1');
        exit();
    }
}

// Get all products
$products = getAllProducts();
// Get all orders
$orders = getAllOrders();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Coffee Shop</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .admin-section {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .admin-section h2 {
            color: #6f4e37;
            margin-bottom: 20px;
            border-bottom: 2px solid #e2c275;
            padding-bottom: 10px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
        }

        .product-info {
            margin-top: 10px;
        }

        .product-info h3 {
            color: #3a2c1a;
            margin-bottom: 5px;
        }

        .product-info p {
            color: #6e5c45;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .product-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-edit, .btn-delete {
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-edit {
            background: #e2c275;
            color: #3a2c1a;
        }

        .btn-delete {
            background: #ff6b6b;
            color: white;
        }

        .add-product-form {
            max-width: 500px;
            margin: 0 auto;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #3a2c1a;
        }

        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #d5c1a2;
            border-radius: 4px;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .orders-table th, .orders-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #d5c1a2;
        }

        .orders-table th {
            background: #f7f7f7;
            color: #3a2c1a;
        }

        .status-pending {
            color: #f39c12;
        }

        .status-completed {
            color: #27ae60;
        }

        .status-cancelled {
            color: #e74c3c;
        }

        .tab-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .tab-button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            background: #f7f7f7;
            color: #3a2c1a;
            cursor: pointer;
        }

        .tab-button.active {
            background: #6f4e37;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 class="logo">Coffee Shop Admin</h1>
            <nav>
                <ul>
                    <li><a href="index.php">Back to Shop</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="admin-container">
        <div class="tab-buttons">
            <button class="tab-button active" onclick="showTab('products')">Products</button>
            <button class="tab-button" onclick="showTab('orders')">Orders</button>
            <button class="tab-button" onclick="showTab('add-product')">Add Product</button>
        </div>

        <div id="products" class="tab-content active">
            <div class="admin-section">
                <h2>Manage Products</h2>
                <div class="product-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <div class="admin-desc-scroll">
                                <?php echo htmlspecialchars($product['description']); ?>
                            </div>
                            <p>Price: <?php echo number_format($product['price'], 2); ?> SAR</p>
                            <p>Stock: <?php echo $product['stock']; ?></p>
                            <div class="product-actions">
                                <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn-edit">Edit</a>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                                    <button type="submit" name="delete_product" class="btn-delete" onclick="return confirm('Are you sure you want to delete this product?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div id="orders" class="tab-content">
            <div class="admin-section">
                <h2>Order Management</h2>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo isset($order['order_id']) ? $order['order_id'] : $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td><?php echo number_format(isset($order['total_price']) ? $order['total_price'] : $order['total_amount'], 2); ?> SAR</td>
                                <td class="status-<?php echo strtolower($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </td>
                                <td>
                                    <button class="btn-edit" onclick="viewOrder(<?php echo isset($order['order_id']) ? $order['order_id'] : $order['id']; ?>)">View Details</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No orders found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="add-product" class="tab-content">
            <div class="admin-section" id="addProduct">
                <h2>Add New Product </h2>
                <form class="add-product-form" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Product Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="price">Price (SAR)</label>
                        <input type="number" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="number" id="stock" name="stock" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select id="type" name="type" required>
                            <option value="beans">Coffee Beans</option>
                            <option value="machine">Coffee Machine</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <input type="file" id="image" name="image" accept="image/*">
                    </div>
                    <button type="submit" name="add_product" class="btn-primary">Add Product</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
        <div class="modal-content" style="background: white; margin: 10% auto; padding: 20px; width: 80%; max-width: 800px; border-radius: 8px; position: relative;">
            <span class="close" onclick="closeModal()" style="position: absolute; right: 20px; top: 10px; font-size: 24px; cursor: pointer;">&times;</span>
            <h2>Order Details</h2>
            <div id="orderDetails"></div>
        </div>
    </div>

    <script>
        function showTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Show selected tab and activate button
            document.getElementById(tabId).classList.add('active');
            event.target.classList.add('active');
        }

        function editProduct(productId) {
            // Fetch product details
            fetch('get_product_details.php?product_id=' + productId)
                .then(response => response.json())
                .then(data => {
                    // Implement edit product functionality
                    alert('Edit product ' + productId);
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading product details');
                });
        }

        function viewOrder(orderId) {
            // Fetch order details
            fetch('get_order_details.php?order_id=' + orderId)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    const modal = document.getElementById('orderModal');
                    const detailsDiv = document.getElementById('orderDetails');
                    
                    // Check if data contains required fields
                    if (!data.order || !data.items) {
                        detailsDiv.innerHTML = '<p>Error: Unable to load order details</p>';
                        modal.style.display = 'block';
                        return;
                    }
                    
                    // Get order ID safely
                    const orderId = data.order.order_id || data.order.id || 'Unknown';
                    const customerName = data.order.customer_name || 'Unknown Customer';
                    const orderDate = data.order.order_date ? new Date(data.order.order_date).toLocaleDateString() : 'Unknown Date';
                    const orderStatus = data.order.status || 'pending';
                    const totalAmount = data.order.total_price || data.order.total_amount || 0;
                    
                    // Create order details HTML
                    let html = `
                        <div class="order-info">
                            <p><strong>Order ID:</strong> #${orderId}</p>
                            <p><strong>Customer:</strong> ${customerName}</p>
                            <p><strong>Date:</strong> ${orderDate}</p>
                            <p><strong>Status:</strong> 
                                <select onchange="updateOrderStatus(${orderId}, this.value)">
                                    <option value="pending" ${orderStatus === 'pending' ? 'selected' : ''}>Pending</option>
                                    <option value="paid" ${orderStatus === 'paid' ? 'selected' : ''}>Paid</option>
                                    <option value="shipped" ${orderStatus === 'shipped' ? 'selected' : ''}>Shipped</option>
                                    <option value="delivered" ${orderStatus === 'delivered' ? 'selected' : ''}>Delivered</option>
                                    <option value="cancelled" ${orderStatus === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                                </select>
                            </p>
                        </div>
                        <table class="orders-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    if (data.items.length > 0) {
                        data.items.forEach(item => {
                            const productName = item.product_name || 'Unknown Product';
                            const quantity = item.quantity || 0;
                            const price = parseFloat(item.unit_price || item.price || 0).toFixed(2);
                            const subtotal = (quantity * parseFloat(price)).toFixed(2);
                            
                            html += `
                                <tr>
                                    <td>${productName}</td>
                                    <td>${quantity}</td>
                                    <td>${price} SAR</td>
                                    <td>${subtotal} SAR</td>
                                </tr>
                            `;
                        });
                    } else {
                        html += `<tr><td colspan="4" style="text-align: center;">No items found</td></tr>`;
                    }
                    
                    html += `
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align: right;"><strong>Total:</strong></td>
                                    <td>${parseFloat(totalAmount).toFixed(2)} SAR</td>
                                </tr>
                            </tfoot>
                        </table>
                    `;
                    
                    detailsDiv.innerHTML = html;
                    modal.style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading order details: ' + error.message);
                });
        }

        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
        }

        function updateOrderStatus(orderId, status) {
            fetch('update_order_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&status=${status}`
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    alert('Order status updated successfully');
                    // Refresh the orders table
                    location.reload();
                } else {
                    alert('Error updating order status: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating order status: ' + error.message);
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html> 