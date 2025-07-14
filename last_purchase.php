<?php
session_start();
$page_title = 'آخر عملية شراء';
require_once 'header.php';
require_once 'db_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'يرجى تسجيل الدخول لعرض عمليات الشراء';
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get all orders for this user
$orders = getUserOrders($user_id);

// Check if there are any orders
$hasOrders = !empty($orders);
$latestOrder = $hasOrders ? $orders[0] : null; // Get the first order (most recent)
$orderDetails = $hasOrders ? getOrderDetails($latestOrder['order_id'], $user_id) : null;

// Status labels in Arabic
$statusLabels = [
    'pending' => 'قيد الانتظار',
    'paid' => 'تم الدفع',
    'shipped' => 'تم الشحن',
    'delivered' => 'تم التوصيل',
    'cancelled' => 'ملغي'
];
?>

<div class="last-purchase-container" id="printableArea">
    <div class="page-header">
        <h1 class="page-title">آخر عملية شراء</h1>
        <?php if ($hasOrders): ?>
            <div class="action-buttons">
                <button onclick="printOrder()" class="print-btn">طباعة</button>
                <a href="my_orders.php" class="view-all-btn">عرض الكل</a>
                <a href="products.php" class="shop-now-btn">تسوق الآن</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($hasOrders): ?>
        <div class="purchase-content">
            <div class="purchase-header">
                <div class="receipt-header">
                    <div class="logo-section">
                        <h2>قهوتنا</h2>
                        <p>أفضل مذاق للقهوة العربية</p>
                    </div>
                    <div class="receipt-info">
                        <p>تاريخ: <?php echo date('Y-m-d', strtotime($latestOrder['order_date'])); ?></p>
                        <p>وقت: <?php echo date('H:i', strtotime($latestOrder['order_date'])); ?></p>
                        <p>رقم الفاتورة: #<?php echo sprintf('%06d', $latestOrder['order_id']); ?></p>
                    </div>
                </div>
                
                <div class="customer-info">
                    <h3>معلومات العميل</h3>
                    <p>الاسم: <?php echo htmlspecialchars($_SESSION['name']); ?></p>
                    <?php if (!empty($latestOrder['shipping_address'])): ?>
                        <p>العنوان: <?php echo htmlspecialchars($latestOrder['shipping_address']); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="order-status-section">
                    <h3>حالة الطلب</h3>
                    <div class="status-badge <?php echo $latestOrder['status']; ?>">
                        <?php echo $statusLabels[$latestOrder['status']] ?? $latestOrder['status']; ?>
                    </div>
                </div>
            </div>
            
            <div class="purchase-details">
                <h3>تفاصيل المشتريات</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="item-name">المنتج</th>
                            <th class="item-price">السعر</th>
                            <th class="item-quantity">الكمية</th>
                            <th class="item-total">المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orderDetails['items'] as $item): ?>
                            <tr>
                                <td class="item-name"><?php echo htmlspecialchars($item['name']); ?></td>
                                <td class="item-price"><?php echo number_format($item['unit_price'], 2); ?> ريال</td>
                                <td class="item-quantity"><?php echo $item['quantity']; ?></td>
                                <td class="item-total"><?php echo number_format($item['subtotal'], 2); ?> ريال</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="purchase-summary">
                <div class="summary-row">
                    <span class="summary-label">المجموع الفرعي:</span>
                    <span class="summary-value"><?php echo number_format($orderDetails['order']['total_price'] / 1.15, 2); ?> ريال</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">الضريبة (15%):</span>
                    <span class="summary-value"><?php echo number_format($orderDetails['order']['total_price'] - ($orderDetails['order']['total_price'] / 1.15), 2); ?> ريال</span>
                </div>
                <div class="summary-row total">
                    <span class="summary-label">المجموع الكلي:</span>
                    <span class="summary-value"><?php echo number_format($orderDetails['order']['total_price'], 2); ?> ريال</span>
                </div>
            </div>
            
            <div class="purchase-footer">
                <p>طريقة الدفع: <?php echo $orderDetails['order']['payment_method'] === 'cash' ? 'الدفع نقداً' : 'بطاقة ائتمان'; ?></p>
                <p class="thank-you">شكراً لاختياركم قهوتنا</p>
                <p class="contact-info">للاستفسارات: 055-123-4567 | info@qahwatuna.com</p>
            </div>
        </div>
    <?php else: ?>
        <div class="no-orders">
            <div class="empty-icon">
                <i class="fas fa-shopping-basket"></i>
            </div>
            <h2>لم تقم بأي عملية شراء بعد</h2>
            <p>استكشف منتجاتنا المميزة وابدأ التسوق الآن!</p>
            <a href="products.php" class="shop-now-btn">تصفح المنتجات</a>
        </div>
    <?php endif; ?>
</div>

<style>
    .last-purchase-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        border-bottom: 2px solid #E3D2B9;
        padding-bottom: 1rem;
    }
    
    .page-title {
        color: #5C3D2E;
        margin: 0;
        font-size: 1.8rem;
    }
    
    .action-buttons {
        display: flex;
        gap: 1rem;
    }
    
    .print-btn, .view-all-btn {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-weight: bold;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
    }
    
    .print-btn {
        background-color: #5C3D2E;
        color: white;
        border: none;
    }
    
    .print-btn:hover {
        background-color: #4a3122;
    }
    
    .view-all-btn {
        background-color: #f5f5f5;
        color: #333;
        border: 1px solid #ddd;
    }
    
    .view-all-btn:hover {
        background-color: #e9e9e9;
    }
    
    .print-btn i, .view-all-btn i {
        margin-left: 0.5rem;
    }
    
    .purchase-content {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        padding: 2rem;
    }
    
    .receipt-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }
    
    .logo-section h2 {
        color: #5C3D2E;
        margin: 0;
        font-size: 1.8rem;
    }
    
    .logo-section p {
        color: #888;
        margin: 0.5rem 0 0;
    }
    
    .receipt-info {
        text-align: left;
    }
    
    .receipt-info p {
        margin: 0.25rem 0;
    }
    
    .customer-info, .order-status-section {
        margin-bottom: 1.5rem;
    }
    
    .customer-info h3, .order-status-section h3, .purchase-details h3 {
        color: #5C3D2E;
        margin-bottom: 0.5rem;
        font-size: 1.2rem;
    }
    
    .customer-info p {
        margin: 0.25rem 0;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        font-weight: bold;
    }
    
    .status-badge.pending {
        background-color: #fff3cd;
        color: #856404;
    }
    
    .status-badge.paid {
        background-color: #d4edda;
        color: #155724;
    }
    
    .status-badge.shipped {
        background-color: #cce5ff;
        color: #004085;
    }
    
    .status-badge.delivered {
        background-color: #d1e7dd;
        color: #0c5460;
    }
    
    .status-badge.cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
    }
    
    .items-table th, .items-table td {
        padding: 0.75rem;
        text-align: right;
        border-bottom: 1px solid #eee;
    }
    
    .items-table th {
        background-color: #f9f9f9;
        color: #5C3D2E;
        font-weight: bold;
    }
    
    .items-table tr:last-child td {
        border-bottom: none;
    }
    
    .item-name {
        width: 40%;
    }
    
    .item-price, .item-quantity, .item-total {
        width: 20%;
    }
    
    .item-total {
        font-weight: bold;
        color: #5C3D2E;
    }
    
    .purchase-summary {
        background-color: #f9f9f9;
        padding: 1rem;
        border-radius: 4px;
        margin-bottom: 2rem;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }
    
    .summary-row.total {
        font-weight: bold;
        font-size: 1.1rem;
        border-top: 1px solid #ddd;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        color: #5C3D2E;
    }
    
    .purchase-footer {
        text-align: center;
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
    }
    
    .purchase-footer p {
        margin: 0.5rem 0;
    }
    
    .thank-you {
        font-size: 1.2rem;
        font-weight: bold;
        color: #5C3D2E;
        margin: 1rem 0;
    }
    
    .contact-info {
        color: #888;
        font-size: 0.9rem;
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
    
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .action-buttons {
            width: 100%;
        }
        
        .print-btn, .view-all-btn {
            flex: 1;
            justify-content: center;
        }
        
        .receipt-header {
            flex-direction: column;
            gap: 1rem;
        }
        
        .receipt-info {
            text-align: right;
        }
        
        .items-table th, .items-table td {
            padding: 0.5rem;
            font-size: 0.9rem;
        }
    }
    
    @media print {
        body * {
            visibility: hidden;
        }
        
        .last-purchase-container, .last-purchase-container * {
            visibility: visible;
        }
        
        .last-purchase-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 0;
            margin: 0;
        }
        
        .page-header, .action-buttons {
            display: none;
        }
        
        .purchase-content {
            box-shadow: none;
            padding: 0;
        }
        
        @page {
            size: A4;
            margin: 1cm;
        }
    }
</style>

<script>
    function printOrder() {
        window.print();
    }
</script>

<?php require_once 'footer.php'; ?> 