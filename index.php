<?php
// Start session
session_start();

// Include database functions
require_once 'db_functions.php';

// Debug session
error_log('Current session in index.php: ' . print_r($_SESSION, true));

// Set page title
$page_title = 'الرئيسية';

// Include header
require_once 'header.php';
?>

<section class="hero">
    <div class="hero-content">
        <h1>أهلاً بكم في قهوتنا</h1>
        <p>استمتعوا بأجود أنواع القهوة العربية وخلطاتنا الخاصة.</p>
    </div>
</section>

<section class="features">
    <div class="feature-card">
        <h3>جودة عالية</h3>
        <p>نستخدم أفضل حبوب البن المحمصة بعناية.</p>
    </div>
    <div class="feature-card">
        <h3>توصيل سريع</h3>
        <p>نوصل طلباتكم إلى باب المنزل في وقت قياسي.</p>
    </div>
    <div class="feature-card">
        <h3>دعم عملاء</h3>
        <p>خدمتكم شرف لنا، تواصلوا معنا في أي وقت.</p>
    </div>
</section>

<?php
// Include footer
require_once 'footer.php';
?>
