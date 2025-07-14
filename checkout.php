<?php
session_start();
$page_title = 'إتمام الطلب';

// Include db_functions but not header yet (to allow redirects)
require_once 'db_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'يرجى تسجيل الدخول لإتمام عملية الشراء';
    header('Location: login.php');
    exit();
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    $_SESSION['error'] = 'سلة التسوق فارغة';
    header('Location: cart.php');
    exit();
}

// Calculate totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.15;
$total = $subtotal + $tax;

// Process order submission
if (isset($_POST['place_order'])) {
    $payment_method = $_POST['payment_method'];
    $address = $_POST['address'] ?? '';
    
    // Prepare products array for the order
    $products = [];
    foreach ($_SESSION['cart'] as $index => $item) {
        // Make sure we have a valid product_id
        $product_id = is_numeric($index) ? $index : (isset($item['product_id']) ? $item['product_id'] : null);
        
        if (!$product_id) {
            continue; // Skip invalid entries
        }
        
        $products[] = [
            'product_id' => $product_id,
            'quantity' => $item['quantity']
        ];
    }
    
    // Only proceed if we have valid products
    if (count($products) > 0) {
        // Create the order
        $order_id = createOrder($_SESSION['user_id'], $products, $address, $payment_method);
        
        if ($order_id) {
            // Clear the cart
            $_SESSION['cart'] = [];
            $_SESSION['success'] = 'تم إنشاء طلبك بنجاح';
            
            // Redirect to my_orders page
            header('Location: my_orders.php');
            exit();
        } else {
            $_SESSION['error'] = 'حدث خطأ أثناء إنشاء الطلب. يرجى المحاولة مرة أخرى.';
        }
    } else {
        $_SESSION['error'] = 'لا توجد منتجات صالحة في السلة';
        header('Location: cart.php');
        exit();
    }
}

// Now it's safe to include the header as we're past all redirects
require_once 'header.php';
?>

<div class="checkout-container">
    <h1>إتمام الطلب</h1>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="error-message">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="checkout-grid">
        <div class="order-summary">
            <h2>ملخص الطلب</h2>
            <div class="cart-items-summary">
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <div class="summary-item">
                        <div class="item-info">
                            <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                            <span class="item-quantity">× <?php echo $item['quantity']; ?></span>
                        </div>
                        <span class="item-price"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> ريال</span>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="order-totals">
                <div class="total-row">
                    <span>المجموع الفرعي:</span>
                    <span><?php echo number_format($subtotal, 2); ?> ريال</span>
                </div>
                <div class="total-row">
                    <span>الضريبة (15%):</span>
                    <span><?php echo number_format($tax, 2); ?> ريال</span>
                </div>
                <div class="total-row final-total">
                    <span>المجموع الكلي:</span>
                    <span><?php echo number_format($total, 2); ?> ريال</span>
                </div>
            </div>
        </div>
        
        <div class="checkout-form">
            <h2>معلومات الدفع</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="address">عنوان التوصيل (اختياري):</label>
                    <textarea id="address" name="address" rows="3" placeholder="أدخل عنوان التوصيل إذا كنت ترغب في التوصيل"></textarea>
                </div>
                
                <div class="form-group">
                    <label>طريقة الدفع:</label>
                    <div class="payment-options">
                        <div class="payment-option">
                            <input type="radio" id="cash" name="payment_method" value="cash" checked>
                            <label for="cash">الدفع نقداً</label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="card" name="payment_method" value="card">
                            <label for="card">بطاقة ائتمان</label>
                        </div>
                    </div>
                </div>

                <div id="card-details" class="form-group" style="display: none;">
                    <div class="card-inputs">
                        <div class="form-group">
                            <label for="card-number">رقم البطاقة:</label>
                            <input type="text" id="card-number" placeholder="0000 0000 0000 0000" maxlength="19">
                        </div>
                        <div class="card-row">
                            <div class="form-group">
                                <label for="expiry">تاريخ الانتهاء:</label>
                                <input type="text" id="expiry" placeholder="MM/YY" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV:</label>
                                <input type="text" id="cvv" placeholder="123" maxlength="3">
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" name="place_order" class="place-order-btn">إتمام الطلب</button>
                <a href="cart.php" class="back-to-cart">العودة للسلة</a>
            </form>
        </div>
    </div>
</div>

<style>
    .checkout-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem;
    }
    
    .checkout-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        margin-top: 2rem;
    }
    
    @media (max-width: 768px) {
        .checkout-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .order-summary, .checkout-form {
        background-color: #fff;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .cart-items-summary {
        margin: 1.5rem 0;
    }
    
    .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #eee;
    }
    
    .item-info {
        display: flex;
        flex-direction: column;
    }
    
    .item-quantity {
        color: #666;
        font-size: 0.9rem;
    }
    
    .order-totals {
        margin-top: 1.5rem;
    }
    
    .total-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }
    
    .final-total {
        font-weight: bold;
        font-size: 1.2rem;
        border-top: 2px solid #eee;
        margin-top: 0.5rem;
        padding-top: 1rem;
    }
    
    .checkout-form h2 {
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: bold;
    }
    
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1rem;
    }
    
    .payment-options {
        display: flex;
        gap: 1.5rem;
        margin-top: 0.5rem;
    }
    
    .payment-option {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .card-row {
        display: flex;
        gap: 1rem;
    }
    
    .place-order-btn, .back-to-cart {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        text-align: center;
        text-decoration: none;
        font-size: 1rem;
        margin-right: 1rem;
    }
    
    .place-order-btn {
        background-color: #5C3D2E;
        color: white;
        border: none;
        cursor: pointer;
    }
    
    .back-to-cart {
        background-color: #f5f5f5;
        color: #333;
        border: 1px solid #ddd;
    }
    
    .place-order-btn:hover {
        background-color: #4a3124;
    }
    
    .back-to-cart:hover {
        background-color: #e5e5e5;
    }
    
    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1.5rem;
    }
</style>

<script>
    // Toggle card payment details
    document.addEventListener('DOMContentLoaded', function() {
        const cardRadio = document.getElementById('card');
        const cashRadio = document.getElementById('cash');
        const cardDetails = document.getElementById('card-details');
        
        cardRadio.addEventListener('change', function() {
            if (this.checked) {
                cardDetails.style.display = 'block';
            }
        });
        
        cashRadio.addEventListener('change', function() {
            if (this.checked) {
                cardDetails.style.display = 'none';
            }
        });
    });
</script>

<?php require_once 'footer.php'; ?> 