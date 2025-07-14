<?php
session_start();
$page_title = 'سلة التسوق';
require_once 'header.php';

// Initialize total
$total = 0;
?>

<section class="hero">
    <div class="hero-content">
        <h1>عربة التسوق</h1>
        <p>راجع منتجاتك قبل إتمام الشراء</p>
    </div>
</section>

<div class="cart-container">
    <h1>سلة التسوق</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])): ?>
        <div class="empty-cart">
            <h2>السلة فارغة</h2>
            <p>لم تقم بإضافة أي منتجات إلى السلة بعد.</p>
            <a href="products.php" class="btn-primary">تصفح المنتجات</a>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
            <div class="cart-item">
                <div class="item-image">
                    <?php if (!empty($item['image'])): ?>
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <?php else: ?>
                        <img src="images/default-product.jpg" alt="Default product image">
                    <?php endif; ?>
                </div>
                <div class="item-details">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p class="item-price"><?php echo number_format($item['price'], 2); ?> ريال</p>
                </div>
                <div class="quantity-controls">
                    <form action="update_cart.php" method="POST" class="quantity-form">
                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                        <label for="quantity-<?php echo $product_id; ?>">الكمية:</label>
                        <input type="number" 
                               id="quantity-<?php echo $product_id; ?>"
                               name="quantity" 
                               value="<?php echo $item['quantity']; ?>"
                               min="1"
                               onchange="this.form.submit()">
                    </form>
                </div>
                <div class="item-total">
                    <?php 
                    $itemTotal = $item['price'] * $item['quantity'];
                    $total += $itemTotal;
                    echo number_format($itemTotal, 2); 
                    ?> ريال
                </div>
                <form action="remove_from_cart.php" method="POST" class="remove-form">
                    <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                    <button type="submit" class="remove-item" title="إزالة من السلة">إزالة</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <div class="summary-row">
                <span>المجموع الفرعي:</span>
                <span><?php echo number_format($total, 2); ?> ريال</span>
            </div>
            <div class="summary-row">
                <span>الضريبة (15%):</span>
                <span><?php echo number_format($total * 0.15, 2); ?> ريال</span>
            </div>
            <div class="summary-row total">
                <span>المجموع الكلي:</span>
                <span><?php echo number_format($total * 1.15, 2); ?> ريال</span>
            </div>
            <a href="checkout.php" class="btn-primary checkout-btn">إتمام الشراء</a>
            <a href="products.php" class="btn-primary">تسوق الآن</a>
            <a href="products.php" class="btn-secondary continue-shopping">متابعة التسوق</a>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
