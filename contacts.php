<?php
// Set page title
$page_title = 'تواصل معنا';

// Include header
require_once 'header.php';

// Include database functions
require_once 'db_functions.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
?>

<!-- Add contact page specific CSS -->
<link rel="stylesheet" href="css/contact.css">
<!-- Add Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<section class="hero">
    <div class="hero-content">
        <h1>تواصل معنا</h1>
        <p>نحن هنا للإجابة على استفساراتكم وخدمتكم بأفضل شكل ممكن</p>
    </div>
</section>

<section class="contact-content">
    <div class="contact-info">
        <h2>معلومات الاتصال</h2>
        <p><i class="fas fa-envelope"></i> info@qahwatna.com</p>
        <p><i class="fas fa-phone"></i> 920000000</p>
        <p><i class="fas fa-map-marker-alt"></i> الرياض، المملكة العربية السعودية</p>
        
        <div class="social-links">
            <h3>تابعونا على</h3>
            <div class="social-icons">
                <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>
    </div>

    <div class="contact-form">
        <h2>راسلنا</h2>
        <form action="process_contact.php" method="POST" id="contactForm">
            <div class="form-group">
                <label for="name">الاسم</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="phone">رقم الجوال</label>
                <input type="tel" id="phone" name="phone" pattern="[0-9]{10}" placeholder="05xxxxxxxx">
            </div>
            <div class="form-group">
                <label for="subject">الموضوع</label>
                <select id="subject" name="subject" required>
                    <option value="">اختر الموضوع</option>
                    <option value="inquiry">استفسار عام</option>
                    <option value="order">طلب خاص</option>
                    <option value="complaint">شكوى</option>
                    <option value="suggestion">اقتراح</option>
                </select>
            </div>
            <div class="form-group">
                <label for="message">الرسالة</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-paper-plane"></i> إرسال
            </button>
        </form>
    </div>
</section>

<section class="map-section">
    <div class="container">
        <h2>موقعنا</h2>
        <div class="map-container">
            <!-- Replace the iframe src with your actual Google Maps embed code -->
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d463880.6811160197!2d46.5423529!3d24.7135517!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2f03890d489399%3A0xba974d1c98e79fd5!2sRiyadh%20Saudi%20Arabia!5e0!3m2!1sen!2s!4v1620000000000!5m2!1sen!2s" 
                width="100%" 
                height="100%" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>

<script>
document.getElementById('contactForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    
    // Show loading state
    submitButton.classList.add('loading');
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
    
    try {
        const formData = new FormData(form);
        const response = await fetch('process_contact.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (response.ok) {
            // Show success message
            alert(result.message);
            form.reset();
        } else {
            // Show error message
            alert(result.error || 'حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('حدث خطأ أثناء إرسال الرسالة. يرجى المحاولة مرة أخرى.');
    } finally {
        // Reset button state
        submitButton.classList.remove('loading');
        submitButton.innerHTML = originalButtonText;
    }
});
</script>

<?php
// Include footer
require_once 'footer.php';
?>
