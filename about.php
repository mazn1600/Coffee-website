<?php
// Set page title
$page_title = 'من نحن';

// Include header
require_once 'header.php';

// Include database functions
require_once 'db_functions.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
?>

<section class="hero">
    <div class="hero-content">
        <h1>من نحن</h1>
        <p>تعرف على قصة قهوتنا وفريقنا المتميز.</p>
    </div>
</section>

<section class="about-content">
    <div class="about-section">
        <h2>قصتنا</h2>
        <p>بدأت قهوتنا كمشروع صغير في عام 2010 بهدف تقديم أجود أنواع القهوة العربية للمستهلكين. نحن نؤمن بأن القهوة ليست مجرد مشروب، بل هي جزء من ثقافتنا وتراثنا.</p>
        <p>نحرص على اختيار أفضل حبوب البن من مصادر موثوقة ونقوم بتحميصها بعناية فائقة للحفاظ على نكهتها الأصلية وجودتها العالية.</p>
    </div>

    <div class="about-section">
        <h2>رؤيتنا</h2>
        <p>نسعى لأن نكون الوجهة الأولى لعشاق القهوة في المملكة العربية السعودية، من خلال تقديم منتجات عالية الجودة وخدمة متميزة للعملاء.</p>
    </div>

    <div class="about-section">
        <h2>قيمنا</h2>
        <ul>
            <li>الجودة: نلتزم بتقديم أفضل المنتجات دون تنازل.</li>
            <li>الأصالة: نحافظ على الطرق التقليدية في تحضير القهوة العربية.</li>
            <li>الابتكار: نسعى دائماً لتطوير منتجاتنا وخدماتنا.</li>
            <li>خدمة العملاء: رضا العملاء هو أولويتنا الأولى.</li>
        </ul>
    </div>
</section>

<?php
// Include footer
require_once 'footer.php';
?>
