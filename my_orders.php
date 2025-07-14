<?php
session_start();
$page_title = 'طلباتي';
require_once 'header.php';
require_once 'db_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'يرجى تسجيل الدخول لعرض طلباتك';
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all orders for this user
$orders = getUserOrders($user_id);

// Status labels in Arabic
$statusLabels = [
    'pending' => 'قيد الانتظار',
    'paid' => 'تم الدفع',
    'shipped' => 'تم الشحن',
    'delivered' => 'تم التوصيل',
    'cancelled' => 'ملغي'
];
?>

<div class="my-orders-container">
    <div class="page-header">
        <h1 class="page-title">طلباتي</h1>
    </div>
    
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

    <?php if (empty($orders)): ?>
        <div class="no-orders">
            <div class="empty-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h2>لا توجد طلبات سابقة</h2>
            <p>تصفح منتجاتنا المميزة وابدأ التسوق الآن!</p>
            <a href="products.php" class="shop-now-btn">تسوق الآن</a>
        </div>
    <?php else: ?>
        <div class="orders-filter">
            <div class="filter-title">تصفية حسب حالة الطلب:</div>
            <div class="filter-buttons">
                <button class="filter-btn active" data-status="all">الكل</button>
                <button class="filter-btn" data-status="pending">قيد الانتظار</button>
                <button class="filter-btn" data-status="paid">تم الدفع</button>
                <button class="filter-btn" data-status="shipped">تم الشحن</button>
                <button class="filter-btn" data-status="delivered">تم التوصيل</button>
                <button class="filter-btn" data-status="cancelled">ملغي</button>
            </div>
        </div>
        
        <div class="orders-list">
            <?php foreach ($orders as $order): ?>
                <div class="order-card" data-status="<?php echo $order['status']; ?>">
                    <div class="order-header">
                        <div class="order-info">
                            <div class="order-number">
                                رقم الطلب: <span>#<?php echo sprintf('%06d', $order['order_id']); ?></span>
                            </div>
                            <div class="order-date">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo date('Y-m-d', strtotime($order['order_date'])); ?>
                                <span class="time">
                                    <i class="far fa-clock"></i>
                                    <?php echo date('H:i', strtotime($order['order_date'])); ?>
                                </span>
                            </div>
                        </div>
                        <div class="order-status <?php echo $order['status']; ?>">
                            <?php echo $statusLabels[$order['status']] ?? $order['status']; ?>
                        </div>
                    </div>
                    
                    <?php 
                    // Get order details including items
                    $orderDetails = getOrderDetails($order['order_id'], $user_id);
                    $items = isset($orderDetails['items']) && is_array($orderDetails['items']) ? $orderDetails['items'] : [];
                    ?>
                    
                    <div class="order-content">
                        <div class="order-items-preview">
                            <?php 
                            $itemsToShow = array_slice($items, 0, 2); // Show first 2 items
                            $remainingCount = count($items) - count($itemsToShow);
                            ?>
                            
                            <?php foreach ($itemsToShow as $item): ?>
                                <div class="preview-item">
                                    <span class="item-name"><?php echo htmlspecialchars($item['name']); ?></span>
                                    <span class="item-quantity">× <?php echo $item['quantity']; ?></span>
                                </div>
                            <?php endforeach; ?>
                            
                            <?php if ($remainingCount > 0): ?>
                                <div class="more-items">
                                    +<?php echo $remainingCount; ?> منتج آخر
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="order-summary">
                            <div class="summary-item">
                                <span class="label">عدد المنتجات:</span>
                                <span class="value"><?php echo count($items); ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="label">طريقة الدفع:</span>
                                <span class="value"><?php echo $order['payment_method'] === 'cash' ? 'الدفع نقداً' : 'بطاقة ائتمان'; ?></span>
                            </div>
                            <div class="summary-item">
                                <span class="label">المجموع:</span>
                                <span class="value price"><?php echo number_format($order['total_price'], 2); ?> ريال</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="order-actions">
                        <a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" class="btn-details">
                            <i class="fas fa-info-circle"></i>
                            عرض التفاصيل
                        </a>
                        <a href="print_bill.php?order_id=<?php echo $order['order_id']; ?>&auto_print=1" class="btn-print">
                            <i class="fas fa-print"></i>
                            طباعة الفاتورة
                        </a>
                        <?php if ($order['status'] === 'pending'): ?>
                            <a href="#" class="btn-cancel" data-order-id="<?php echo $order['order_id']; ?>">
                                <i class="fas fa-times-circle"></i>
                                إلغاء الطلب
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    .my-orders-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .page-header {
        margin-bottom: 2rem;
        border-bottom: 2px solid #E3D2B9;
        padding-bottom: 1rem;
    }
    
    .page-title {
        color: #5C3D2E;
        margin: 0;
        font-size: 1.8rem;
        text-align: center;
    }
    
    .success-message {
        background-color: #d4edda;
        color: #155724;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    
    .error-message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    
    .no-orders {
        text-align: center;
        padding: 3rem 1rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .empty-icon {
        font-size: 4rem;
        color: #E3D2B9;
        margin-bottom: 1rem;
    }
    
    .no-orders h2 {
        color: #5C3D2E;
        margin-bottom: 1rem;
    }
    
    .no-orders p {
        color: #666;
        margin-bottom: 2rem;
    }
    
    .shop-now-btn {
        display: inline-block;
        background-color: #5C3D2E;
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }
    
    .shop-now-btn:hover {
        background-color: #4a3122;
    }
    
    .orders-filter {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
    }
    
    .filter-title {
        font-weight: bold;
        color: #5C3D2E;
    }
    
    .filter-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .filter-btn {
        background-color: #f5f5f5;
        border: 1px solid #ddd;
        color: #333;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .filter-btn:hover {
        background-color: #e9e9e9;
    }
    
    .filter-btn.active {
        background-color: #5C3D2E;
        color: white;
        border-color: #5C3D2E;
    }
    
    .orders-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .order-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f9f9f9;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .order-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .order-number span {
        font-weight: bold;
        color: #5C3D2E;
    }
    
    .order-date {
        color: #666;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .time {
        margin-right: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .order-status {
        font-weight: bold;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        text-align: center;
        min-width: 100px;
    }
    
    .order-status.pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .order-status.paid {
        background-color: #d4edda;
        color: #155724;
    }
    
    .order-status.shipped {
        background-color: #cce5ff;
        color: #004085;
    }
    
    .order-status.delivered {
        background-color: #d1e7dd;
        color: #0c5460;
    }
    
    .order-status.cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .order-content {
        display: flex;
        padding: 1.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .order-items-preview {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding-left: 1.5rem;
        border-left: 1px solid #eee;
    }
    
    .order-summary {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        padding-right: 1.5rem;
    }
    
    .preview-item {
        display: flex;
        justify-content: space-between;
    }
    
    .item-name {
        font-weight: bold;
    }
    
    .item-quantity {
        color: #666;
    }
    
    .more-items {
        color: #5C3D2E;
        font-style: italic;
        margin-top: 0.5rem;
    }
    
    .summary-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .summary-item .label {
        color: #666;
    }
    
    .summary-item .value {
        font-weight: bold;
    }
    
    .summary-item .price {
        color: #5C3D2E;
    }
    
    .order-actions {
        display: flex;
        padding: 1rem 1.5rem;
        gap: 0.75rem;
        justify-content: flex-end;
    }
    
    .btn-details, .btn-print, .btn-cancel {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        text-decoration: none;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .btn-details {
        background-color: #f5f5f5;
        color: #333;
        border: 1px solid #ddd;
    }
    
    .btn-details:hover {
        background-color: #e9e9e9;
    }
    
    .btn-print {
        background-color: #5C3D2E;
        color: white;
        border: none;
    }
    
    .btn-print:hover {
        background-color: #4a3122;
    }
    
    .btn-cancel {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .btn-cancel:hover {
        background-color: #f5c6cb;
    }
    
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.5rem;
        }
        
        .orders-filter {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .filter-buttons {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            justify-content: flex-start;
        }
        
        .filter-btn {
            white-space: nowrap;
        }
        
        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .order-status {
            align-self: flex-start;
        }
        
        .order-content {
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .order-items-preview {
            border-left: none;
            border-bottom: 1px solid #eee;
            padding-left: 0;
            padding-bottom: 1rem;
        }
        
        .order-summary {
            padding-right: 0;
            padding-top: 1rem;
        }
        
        .order-actions {
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn-details, .btn-print, .btn-cancel {
            flex: 1 1 auto;
            justify-content: center;
        }
    }
    
    @media (max-width: 480px) {
        .page-title {
            font-size: 1.3rem;
        }
        
        .order-header, .order-content, .order-actions {
            padding: 1rem;
        }
        
        .btn-details, .btn-print, .btn-cancel {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Filter buttons functionality
        const filterButtons = document.querySelectorAll('.filter-btn');
        const orderCards = document.querySelectorAll('.order-card');
        
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                const status = this.getAttribute('data-status');
                
                // Filter order cards
                orderCards.forEach(card => {
                    if (status === 'all' || card.getAttribute('data-status') === status) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
        
        // Cancel order functionality (would need backend implementation)
        const cancelButtons = document.querySelectorAll('.btn-cancel');
        
        cancelButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const orderId = this.getAttribute('data-order-id');
                
                if (confirm('هل أنت متأكد من رغبتك في إلغاء هذا الطلب؟')) {
                    // Here you would implement the actual cancellation
                    alert('تم إرسال طلب الإلغاء');
                }
            });
        });
    });
</script>

<?php require_once 'footer.php'; ?> 