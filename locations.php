<?php
// Set page title
$page_title = 'مواقعنا';

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
        <h1>مواقعنا</h1>
        <p>تفضل بزيارتنا في أقرب فرع إليك</p>
    </div>
</section>

<div class="locations-container">
    <div class="location-cards">
        <!-- Riyadh Location -->
    <div class="location-card">
            <div class="location-header">
        <h2>الرياض - حي النخيل</h2>
                <div class="location-status active">مفتوح</div>
            </div>
            <div class="location-details">
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
        <p>شارع الأمير محمد بن عبدالعزيز</p>
                </div>
                <div class="detail-item">
                    <i class="fas fa-phone"></i>
                    <p>011-1234567</p>
                </div>
                <div class="detail-item">
                    <i class="fas fa-clock"></i>
        <p>ساعات العمل: 7:00 صباحاً - 12:00 منتصف الليل</p>
                </div>
                <a href="#" class="location-directions">احصل على الاتجاهات</a>
            </div>
    </div>

        <!-- Jeddah Location -->
    <div class="location-card">
            <div class="location-header">
        <h2>جدة - حي الروضة</h2>
                <div class="location-status active">مفتوح</div>
            </div>
            <div class="location-details">
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
        <p>شارع التحلية</p>
                </div>
                <div class="detail-item">
                    <i class="fas fa-phone"></i>
                    <p>012-7654321</p>
                </div>
                <div class="detail-item">
                    <i class="fas fa-clock"></i>
        <p>ساعات العمل: 7:00 صباحاً - 12:00 منتصف الليل</p>
                </div>
                <a href="#" class="location-directions">احصل على الاتجاهات</a>
            </div>
    </div>

        <!-- Dammam Location -->
    <div class="location-card">
            <div class="location-header">
        <h2>الدمام - حي الشاطئ</h2>
                <div class="location-status active">مفتوح</div>
            </div>
            <div class="location-details">
                <div class="detail-item">
                    <i class="fas fa-map-marker-alt"></i>
        <p>شارع الأمير محمد بن فهد</p>
                </div>
                <div class="detail-item">
                    <i class="fas fa-phone"></i>
                    <p>013-9876543</p>
                </div>
                <div class="detail-item">
                    <i class="fas fa-clock"></i>
        <p>ساعات العمل: 7:00 صباحاً - 12:00 منتصف الليل</p>
                </div>
                <a href="#" class="location-directions">احصل على الاتجاهات</a>
            </div>
        </div>
    </div>

    <div class="map-section">
    <h2>الخريطة التفاعلية</h2>
    <div class="map-container" style="height:400px; border-radius:10px; overflow:hidden;">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3623.447964964839!2d46.67529531500144!3d24.71355168411259!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2f03890d489399%3A0xba974d1c98e79fd5!2sRiyadh%2C%20Saudi%20Arabia!5e0!3m2!1sen!2ssa!4v1680000000000!5m2!1sen!2ssa"
            width="100%"
            height="100%"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>
</div>
</div>

<?php
// Include footer
require_once 'footer.php';
?>
