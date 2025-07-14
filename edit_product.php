<?php
require_once 'db_functions.php';

// Get product ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid product ID.');
}
$product_id = intval($_GET['id']);

// Fetch product details
$product = getProductById($product_id);
if (!$product) {
    die('Product not found.');
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $type = $_POST['type'];
    $image = $_POST['image']; // Assuming image is a URL or filename

    $success = updateProduct($product_id, $name, $description, $price, $stock, $image, $type);
    if ($success) {
        $message = 'Product updated successfully!';
        // Refresh product details
        $product = getProductById($product_id);
    } else {
        $message = 'Failed to update product.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background: #f7faf9;
        }
        .edit-container {
            max-width: 600px;
            margin: 40px auto;
        }
        .edit-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            padding: 32px 28px 24px 28px;
        }
        .edit-card h2 {
            color: #6f4e37;
            margin-bottom: 24px;
            border-bottom: 2px solid #e2c275;
            padding-bottom: 10px;
            font-weight: 700;
        }
        .form-label {
            color: #3a2c1a;
            font-weight: 500;
        }
        .btn-primary {
            background: #e2c275;
            color: #3a2c1a;
            border: none;
        }
        .btn-primary:hover {
            background: #d1b06b;
            color: #fff;
        }
        .btn-secondary {
            background: #6f4e37;
            color: #fff;
            border: none;
        }
        .btn-secondary:hover {
            background: #3a2c1a;
        }
        .form-control:focus {
            border-color: #e2c275;
            box-shadow: 0 0 0 0.2rem rgba(226,194,117,.25);
        }
        .alert-info {
            background: #fffbe6;
            color: #6f4e37;
            border-color: #e2c275;
        }
    </style>
</head>
<body>
<div class="edit-container">
    <div class="edit-card">
        <h2>Edit Product</h2>
        <?php if ($message): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Stock</label>
                <input type="number" name="stock" class="form-control" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Image (URL or filename)</label>
                <input type="text" name="image" class="form-control" value="<?php echo htmlspecialchars($product['image']); ?>">
                <?php if (!empty($product['image'])): ?>
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" class="img-fluid mt-2" style="max-height:120px; border-radius:6px;">
                <?php endif; ?>
            </div>
            <div class="mb-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-control" required>
                    <option value="beans" <?php if ($product['type'] == 'beans') echo 'selected'; ?>>Beans</option>
                    <option value="machine" <?php if ($product['type'] == 'machine') echo 'selected'; ?>>Machine</option>
                </select>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="admin_panel.php" class="btn btn-secondary">Back to Admin Panel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html> 