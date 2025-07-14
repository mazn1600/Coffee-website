<?php
// Set page title
$page_title = 'الأسئلة الشائعة';

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
        <h1>الأسئلة الشائعة</h1>
        <p>أجوبة على أكثر الأسئلة شيوعاً حول خدماتنا</p>
    </div>
</section>

<div class="faq-container">
    <div class="faq-overlay"></div>
    <div class="faq-content">
        
        <div class="faq-section">
            <div class="faq-item">
                <div class="faq-question">
                    <h3>كيف يمكنني طلب منتجاتكم؟</h3>
                    <span class="faq-icon">+</span>
    </div>
                <div class="faq-answer">
        <p>يمكنك طلب منتجاتنا من خلال موقعنا الإلكتروني أو زيارة أحد فروعنا.</p>
                </div>
    </div>

    <div class="faq-item">
                <div class="faq-question">
                    <h3>ما هي طرق الدفع المتاحة؟</h3>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
        <p>نقبل الدفع عن طريق البطاقات البنكية، المحافظ الإلكترونية، والدفع عند الاستلام.</p>
                </div>
    </div>

    <div class="faq-item">
                <div class="faq-question">
                    <h3>كم تستغرق مدة التوصيل؟</h3>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
        <p>تتراوح مدة التوصيل بين 1-3 أيام عمل حسب موقعك.</p>
                </div>
    </div>

    <div class="faq-item">
                <div class="faq-question">
                    <h3>هل يمكنني إرجاع المنتج؟</h3>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
        <p>نعم، يمكنك إرجاع المنتج خلال 14 يوماً من تاريخ الشراء بشرط أن يكون بحالة جيدة.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <h3>هل تقدمون خدمة التوصيل لجميع المناطق؟</h3>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>نعم، نقدم خدمة التوصيل لجميع مناطق المملكة العربية السعودية.</p>
                </div>
            </div>

            <div class="faq-item">
                <div class="faq-question">
                    <h3>كيف يمكنني تتبع طلبي؟</h3>
                    <span class="faq-icon">+</span>
                </div>
                <div class="faq-answer">
                    <p>يمكنك تتبع طلبك من خلال رقم التتبع الذي سيصلك عبر البريد الإلكتروني أو من خلال حسابك على موقعنا.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        const icon = item.querySelector('.faq-icon');
        
        question.addEventListener('click', () => {
            const isOpen = answer.classList.contains('active');
            
            // Close all other answers
            document.querySelectorAll('.faq-answer').forEach(ans => {
                ans.style.maxHeight = null;
                ans.classList.remove('active');
            });
            document.querySelectorAll('.faq-icon').forEach(icn => {
                icn.textContent = '+';
                icn.style.transform = 'rotate(0deg)';
            });
            
            // Toggle current answer
            if (!isOpen) {
                answer.style.maxHeight = answer.scrollHeight + 'px';
                answer.classList.add('active');
                icon.textContent = '−';
                icon.style.transform = 'rotate(180deg)';
                
                // Add hover effect to the open item
                item.style.transform = 'translateX(-10px)';
            } else {
                answer.style.maxHeight = null;
                answer.classList.remove('active');
                icon.textContent = '+';
                icon.style.transform = 'rotate(0deg)';
                
                // Remove hover effect
                item.style.transform = 'translateX(0)';
            }
        });
    });
});
</script>

<?php
// Include footer
require_once 'footer.php';
?>
