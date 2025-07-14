<?php
session_start();
$page_title = 'تفاصيل الطلب';
require_once 'header.php';
require_once 'db_functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'يرجى تسجيل الدخول لعرض تفاصيل الطلب';
    header('Location: login.php');
    exit();
}

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    $_SESSION['error'] = 'لم يتم تحديد رقم الطلب';
    header('Location: my_orders.php');
    exit();
}

$order_id = $_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Get order details
$orderDetails = getOrderDetails($order_id, $user_id);

// If order doesn't exist or doesn't belong to the user
if (!$orderDetails) {
    $_SESSION['error'] = 'لم يتم العثور على الطلب';
    header('Location: my_orders.php');
    exit();
}

$order = $orderDetails['order'];
$items = $orderDetails['items'];

// Status labels in Arabic
$statusLabels = [
    'pending' => 'قيد الانتظار',
    'paid' => 'تم الدفع',
    'shipped' => 'تم الشحن',
    'delivered' => 'تم التوصيل',
    'cancelled' => 'ملغي'
];

// Status timeline steps
$statusTimeline = [
    'pending' => 1,
    'paid' => 2,
    'shipped' => 3,
    'delivered' => 4
];

// Current order status in timeline (if cancelled, set to 0)
$currentStatusStep = isset($statusTimeline[$order['status']]) ? $statusTimeline[$order['status']] : 0;
?>

<div class="order-details-container">
    <div class="page-header">
        <a href="my_orders.php" class="back-link">رجوع</a>
        <h1 class="page-title">تفاصيل الطلب #<?php echo sprintf('%06d', $order['order_id']); ?></h1>
        <div class="header-actions">
            <a href="print_bill.php?order_id=<?php echo $order['order_id']; ?>&auto_print=1" class="btn-print">طباعة</a>
        </div>
    </div>
    
    <?php if ($order['status'] !== 'cancelled'): ?>
    <div class="order-timeline">
        <div class="timeline-track">
            <div class="progress-bar" style="width: <?php echo min(100, ($currentStatusStep - 1) * 33.33); ?>%"></div>
        </div>
        <div class="timeline-steps">
            <div class="timeline-step <?php echo $currentStatusStep >= 1 ? 'active' : ''; ?>">
                <div class="step-icon"><i class="fas fa-clipboard-check"></i></div>
                <div class="step-label">تم الطلب</div>
            </div>
            <div class="timeline-step <?php echo $currentStatusStep >= 2 ? 'active' : ''; ?>">
                <div class="step-icon"><i class="fas fa-credit-card"></i></div>
                <div class="step-label">تم الدفع</div>
            </div>
            <div class="timeline-step <?php echo $currentStatusStep >= 3 ? 'active' : ''; ?>">
                <div class="step-icon"><i class="fas fa-truck"></i></div>
                <div class="step-label">قيد الشحن</div>
            </div>
            <div class="timeline-step <?php echo $currentStatusStep >= 4 ? 'active' : ''; ?>">
                <div class="step-icon"><i class="fas fa-home"></i></div>
                <div class="step-label">تم التوصيل</div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="order-cancelled-banner">
        <i class="fas fa-exclamation-circle"></i>
        <span>تم إلغاء هذا الطلب</span>
    </div>
    <?php endif; ?>
    
    <div class="order-details-grid">
        <div class="order-info-card">
            <div class="card-header">
                <h2><i class="fas fa-info-circle"></i> معلومات الطلب</h2>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-label">رقم الطلب:</div>
                    <div class="info-value">#<?php echo sprintf('%06d', $order['order_id']); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">تاريخ الطلب:</div>
                    <div class="info-value"><?php echo date('Y-m-d', strtotime($order['order_date'])); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">وقت الطلب:</div>
                    <div class="info-value"><?php echo date('H:i', strtotime($order['order_date'])); ?></div>
                </div>
                <div class="info-row">
                    <div class="info-label">حالة الطلب:</div>
                    <div class="info-value">
                        <span class="status-badge <?php echo $order['status']; ?>">
                            <?php echo $statusLabels[$order['status']] ?? $order['status']; ?>
                        </span>
                    </div>
                </div>
                <div class="info-row">
                    <div class="info-label">طريقة الدفع:</div>
                    <div class="info-value">
                        <?php if ($order['payment_method'] === 'cash'): ?>
                            <i class="fas fa-money-bill-wave"></i> الدفع نقداً
                        <?php else: ?>
                            <i class="fas fa-credit-card"></i> بطاقة ائتمان
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (!empty($order['shipping_address'])): ?>
        <div class="shipping-info-card">
            <div class="card-header">
                <h2><i class="fas fa-shipping-fast"></i> معلومات التوصيل</h2>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-label">العنوان:</div>
                    <div class="info-value"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="order-items-card">
            <div class="card-header">
                <h2><i class="fas fa-shopping-bag"></i> المنتجات المطلوبة</h2>
            </div>
            <div class="card-body">
                <div class="items-table-container">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th class="item-image-col"></th>
                                <th class="item-name-col">المنتج</th>
                                <th class="item-price-col">السعر</th>
                                <th class="item-quantity-col">الكمية</th>
                                <th class="item-total-col">المجموع</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="item-image-col">
                                        <div class="item-image-placeholder"></div>
                                    </td>
                                    <td class="item-name-col"><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td class="item-price-col"><?php echo number_format($item['unit_price'], 2); ?> ريال</td>
                                    <td class="item-quantity-col"><?php echo $item['quantity']; ?></td>
                                    <td class="item-total-col"><?php echo number_format($item['subtotal'], 2); ?> ريال</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="order-summary-card">
            <div class="card-header">
                <h2><i class="fas fa-calculator"></i> ملخص الطلب</h2>
            </div>
            <div class="card-body">
                <div class="summary-row">
                    <span class="summary-label">المجموع الفرعي:</span>
                    <span class="summary-value"><?php echo number_format($order['total_price'] / 1.15, 2); ?> ريال</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">الضريبة (15%):</span>
                    <span class="summary-value"><?php echo number_format($order['total_price'] - ($order['total_price'] / 1.15), 2); ?> ريال</span>
                </div>
                <?php if (!empty($order['shipping_address'])): ?>
                <div class="summary-row">
                    <span class="summary-label">رسوم التوصيل:</span>
                    <span class="summary-value">0.00 ريال</span>
                </div>
                <?php endif; ?>
                <div class="summary-divider"></div>
                <div class="summary-row total">
                    <span class="summary-label">المجموع الكلي:</span>
                    <span class="summary-value"><?php echo number_format($order['total_price'], 2); ?> ريال</span>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($order['status'] === 'pending'): ?>
    <div class="order-actions">
        <button type="button" class="btn-cancel" id="cancelOrderBtn" data-order-id="<?php echo $order['order_id']; ?>">إلغاء</button>
    </div>
    <?php endif; ?>
</div>

<style>
    .order-details-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1rem;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }
    
    .back-link {
        color: #5C3D2E;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: bold;
        transition: color 0.3s ease;
    }
    
    .back-link:hover {
        color: #4a3122;
    }
    
    .page-title {
        color: #5C3D2E;
        margin: 0;
        font-size: 1.5rem;
        text-align: center;
        flex-grow: 1;
    }
    
    .header-actions {
        display: flex;
        gap: 1rem;
    }
    
    .btn-print {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background-color: #5C3D2E;
        color: white;
        border-radius: 4px;
        text-decoration: none;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }
    
    .btn-print:hover {
        background-color: #4a3122;
    }
    
    .order-timeline {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        padding: 2rem 1.5rem;
        margin-bottom: 2rem;
        position: relative;
    }
    
    .timeline-track {
        position: absolute;
        top: 50%;
        left: 10%;
        width: 80%;
        height: 4px;
        background-color: #eee;
        transform: translateY(-50%);
    }
    
    .progress-bar {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background-color: #5C3D2E;
        transition: width 0.5s ease;
    }
    
    .timeline-steps {
        display: flex;
        justify-content: space-between;
        position: relative;
        z-index: 1;
    }
    
    .timeline-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        max-width: 100px;
    }
    
    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #f9f9f9;
        border: 2px solid #eee;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .step-label {
        text-align: center;
        color: #777;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }
    
    .timeline-step.active .step-icon {
        background-color: #5C3D2E;
        border-color: #5C3D2E;
        color: white;
    }
    
    .timeline-step.active .step-label {
        color: #5C3D2E;
        font-weight: bold;
    }
    
    .order-cancelled-banner {
        background-color: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-weight: bold;
        font-size: 1.1rem;
        margin-bottom: 2rem;
    }
    
    .order-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .order-info-card, .shipping-info-card, .order-items-card, .order-summary-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .order-items-card, .order-summary-card {
        grid-column: 1 / -1;
    }
    
    .card-header {
        background-color: #f9f9f9;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .card-header h2 {
        color: #5C3D2E;
        margin: 0;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .info-row {
        display: flex;
        margin-bottom: 1rem;
    }
    
    .info-row:last-child {
        margin-bottom: 0;
    }
    
    .info-label {
        flex: 0 0 40%;
        font-weight: bold;
        color: #666;
    }
    
    .info-value {
        flex: 0 0 60%;
        color: #333;
    }
    
    .status-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        font-weight: bold;
        font-size: 0.9rem;
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
    
    .items-table-container {
        overflow-x: auto;
    }
    
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .items-table th, .items-table td {
        padding: 1rem;
        text-align: right;
        border-bottom: 1px solid #eee;
    }
    
    .items-table th {
        background-color: #f9f9f9;
        font-weight: bold;
        color: #5C3D2E;
    }
    
    .items-table tr:last-child td {
        border-bottom: none;
    }
    
    .item-image-col {
        width: 60px;
    }
    
    .item-image-placeholder {
        width: 50px;
        height: 50px;
        background-color: #f5f5f5;
        border-radius: 4px;
    }
    
    .item-name-col {
        width: 40%;
    }
    
    .item-price-col, .item-quantity-col, .item-total-col {
        width: 20%;
    }
    
    .item-total-col {
        font-weight: bold;
        color: #5C3D2E;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }
    
    .summary-divider {
        height: 1px;
        background-color: #eee;
        margin: 1rem 0;
    }
    
    .summary-row.total {
        font-weight: bold;
        font-size: 1.2rem;
        color: #5C3D2E;
    }
    
    .order-actions {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }
    
    .btn-cancel {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background-color 0.3s ease;
    }
    
    .btn-cancel:hover {
        background-color: #f5c6cb;
    }
    
    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
        
        .page-title {
            text-align: right;
        }
        
        .header-actions {
            align-self: flex-end;
        }
        
        .order-timeline {
            padding: 1.5rem 1rem 2rem;
        }
        
        .timeline-track {
            width: 90%;
            left: 5%;
        }
        
        .step-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .step-label {
            font-size: 0.8rem;
        }
        
        .info-row {
            flex-direction: column;
            margin-bottom: 1.5rem;
        }
        
        .info-label {
            margin-bottom: 0.25rem;
        }
        
        .items-table th, .items-table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.9rem;
        }
    }
    
    @media (max-width: 480px) {
        .page-title {
            font-size: 1.3rem;
        }
        
        .step-icon {
            width: 36px;
            height: 36px;
            font-size: 0.9rem;
        }
        
        .step-label {
            font-size: 0.7rem;
        }
        
        .card-header h2 {
            font-size: 1.1rem;
        }
        
        .items-table th, .items-table td {
            padding: 0.5rem 0.3rem;
            font-size: 0.8rem;
        }
        
        .item-image-placeholder {
            width: 40px;
            height: 40px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cancel order functionality (would need backend implementation)
        const cancelBtn = document.getElementById('cancelOrderBtn');
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', function() {
                const orderId = this.getAttribute('data-order-id');
                
                if (confirm('هل أنت متأكد من رغبتك في إلغاء هذا الطلب؟')) {
                    // Here you would implement the actual cancellation
                    alert('تم إرسال طلب الإلغاء');
                }
            });
        }
    });
</script>

<?php require_once 'footer.php'; ?> 