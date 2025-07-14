<?php
session_start();
$page_title = 'طباعة الفاتورة';
require_once 'header.php';
require_once 'db_functions.php';

// Check if order ID is provided
if (!isset($_GET['order_id'])) {
    $_SESSION['error'] = 'لم يتم تحديد رقم الطلب';
    header('Location: cart.php');
    exit();
}

$order_id = $_GET['order_id'];
$order_details = getOrderDetails($order_id, isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null);

// If order doesn't exist or doesn't belong to the user
if (!$order_details) {
    $_SESSION['error'] = 'لم يتم العثور على الطلب';
    header('Location: cart.php');
    exit();
}

$order = $order_details['order'];
$items = $order_details['items'];

// Check if auto_print is set (will be used to trigger automatic printing)
$auto_print = isset($_GET['auto_print']) && $_GET['auto_print'] == 1;
?>

<div class="bill-container">
    <div class="bill-actions">
        <button onclick="window.print()" class="print-button">طباعة الفاتورة</button>
        <a href="products.php" class="continue-button">متابعة التسوق</a>
    </div>

    <div class="bill" id="printable-bill">
        <div class="bill-header">
            <h1>قهوتنا</h1>
            <p>فاتورة ضريبية مبسطة</p>
            <div class="bill-info">
                <div class="info-row">
                    <span>رقم الطلب:</span>
                    <span>#<?php echo sprintf('%06d', $order['order_id']); ?></span>
                </div>
                <div class="info-row">
                    <span>تاريخ الطلب:</span>
                    <span><?php echo date('Y-m-d H:i', strtotime($order['order_date'])); ?></span>
                </div>
                <div class="info-row">
                    <span>طريقة الدفع:</span>
                    <span><?php echo $order['payment_method'] === 'cash' ? 'الدفع نقداً' : 'بطاقة ائتمان'; ?></span>
                </div>
            </div>
        </div>

        <div class="bill-items">
            <table>
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th>السعر</th>
                        <th>الكمية</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><?php echo number_format($item['unit_price'], 2); ?> ريال</td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo number_format($item['subtotal'], 2); ?> ريال</td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="bill-summary">
            <div class="summary-row">
                <span>المجموع الفرعي:</span>
                <span><?php echo number_format($order['total_price'] / 1.15, 2); ?> ريال</span>
            </div>
            <div class="summary-row">
                <span>الضريبة (15%):</span>
                <span><?php echo number_format($order['total_price'] - ($order['total_price'] / 1.15), 2); ?> ريال</span>
            </div>
            <?php if (!empty($order['shipping_address'])): ?>
                <div class="summary-row">
                    <span>عنوان التوصيل:</span>
                    <span><?php echo htmlspecialchars($order['shipping_address']); ?></span>
                </div>
            <?php endif; ?>
            <div class="summary-row total">
                <span>المجموع الكلي:</span>
                <span><?php echo number_format($order['total_price'], 2); ?> ريال</span>
            </div>
        </div>

        <div class="bill-footer">
            <p>شكراً لتسوقكم من قهوتنا</p>
            <p>نتمنى لكم يوماً سعيداً</p>
        </div>
    </div>
</div>

<style>
    .bill-container {
        max-width: 800px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .bill-actions {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .print-button, .continue-button {
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        font-size: 1rem;
        cursor: pointer;
        text-decoration: none;
        text-align: center;
    }

    .print-button {
        background-color: #5C3D2E;
        color: white;
        border: none;
    }

    .continue-button {
        background-color: #f5f5f5;
        color: #333;
        border: 1px solid #ddd;
    }

    .bill {
        background-color: white;
        border-radius: 8px;
        padding: 2rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .bill-header {
        text-align: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #eee;
    }

    .bill-header h1 {
        color: #5C3D2E;
        margin-bottom: 0.5rem;
    }

    .bill-info {
        margin-top: 1.5rem;
        text-align: right;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .bill-items table {
        width: 100%;
        border-collapse: collapse;
        margin: 1.5rem 0;
    }

    .bill-items th, .bill-items td {
        padding: 0.75rem;
        text-align: right;
        border-bottom: 1px solid #eee;
    }

    .bill-items th {
        background-color: #f9f9f9;
        font-weight: bold;
    }

    .bill-summary {
        margin: 1.5rem 0;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
    }

    .total {
        font-weight: bold;
        font-size: 1.2rem;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #eee;
    }

    .bill-footer {
        margin-top: 2rem;
        padding-top: 1rem;
        border-top: 1px solid #eee;
        text-align: center;
    }

    @media print {
        .bill-actions, header, footer, nav {
            display: none !important;
        }

        .bill {
            box-shadow: none;
            padding: 0;
        }

        .bill-container {
            margin: 0;
            padding: 0;
        }

        @page {
            margin: 0.5cm;
        }

        body {
            font-size: 12pt;
        }
    }
</style>

<script>
    // Auto-print when page loads if auto_print parameter is set
    <?php if ($auto_print): ?>
    window.addEventListener('load', function() {
        // Short delay to ensure page is fully loaded
        setTimeout(function() {
            window.print();
        }, 1000);
    });
    <?php endif; ?>
</script>

<?php require_once 'footer.php'; ?> 