<?php
// Start session at the very beginning
session_start();

// Include database functions
require_once 'db_functions.php';

// Debug session before login
error_log('Session before login attempt: ' . print_r($_SESSION, true));

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to home page
    header('Location: index.php');
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Try admin authentication first
    $admin = authenticateAdmin($email, $password);
    if ($admin) {
        // Clear any existing session data
        session_unset();
        session_destroy();
        session_start();
        
        // Set session variables for admin
        $_SESSION['user_id'] = $admin['user_id'];
        $_SESSION['name'] = $admin['name'];
        $_SESSION['role'] = 'admin';
        
        // Debug session after admin login
        error_log('Session after admin login: ' . print_r($_SESSION, true));
        
        // Redirect to home page
        header('Location: index.php');
        exit;
    }
    
    // If not admin, try regular user authentication
    $user = authenticateUser($email, $password);
    if ($user) {
        // Clear any existing session data
        session_unset();
        session_destroy();
        session_start();
        
        // Set session variables for user
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = 'customer';
        
        // Debug session after user login
        error_log('Session after user login: ' . print_r($_SESSION, true));
        
        // Redirect to home page
        header('Location: index.php');
        exit;
    } else {
        // Authentication failed
        $error_message = 'البريد الإلكتروني أو كلمة المرور غير صحيحة';
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>تسجيل الدخول | قهوتنا</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">قهوتنا</div>
        <nav>
            <ul>
                <li><a href="index.php">الرئيسية</a></li>
                <li><a href="about.php">من نحن</a></li>
                <li><a href="products.php">منتجاتنا</a></li>
                <li><a href="locations.php">مواقعنا</a></li>
                <li><a href="contacts.php">تواصل معنا</a></li>
                <li><a href="faq.php">الأسئلة الشائعة</a></li>
                <li><a href="cart.php">عربة التسوق</a></li>
            </ul>
        </nav>
        <div class="auth-buttons">
            <a href="register.php" class="signup">حساب جديد</a>
            <a href="login.php" class="login">تسجيل دخول</a>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>تسجيل الدخول</h1>
            <p>سجّل دخولك للوصول إلى حسابك</p>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form class="auth-form" method="post" action="login.php">
                <input type="email" name="email" placeholder="البريد الإلكتروني" required />
                <div class="form-group">
                    <div class="password-container" style="position: relative;">
                        <input type="password" id="password" name="password" placeholder="كلمة المرور" required style="padding-left: 40px;">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6f4e37; display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; transition: color 0.3s ease;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit">تسجيل الدخول</button>
                <div class="form-links">
                    <a href="register.php">ليس لديك حساب؟ سجل الآن</a>
                </div>
            </form>
        </div>
    </section>

    <footer>
        <div class="copyright">
            <p>© 2023 قهوتنا. جميع الحقوق محفوظة.</p>
        </div>
    </footer>

    <script>
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const toggleButton = passwordInput.nextElementSibling;
        const icon = toggleButton.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
            toggleButton.style.color = '#3a2c1a';
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
            toggleButton.style.color = '#6f4e37';
        }
        
        passwordInput.focus();
    }

    // Add hover effect
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.toggle-password');
        toggleButtons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.color = '#3a2c1a';
            });
            button.addEventListener('mouseleave', function() {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    this.style.color = '#6f4e37';
                }
            });
        });
    });
    </script>
</body>
</html>
