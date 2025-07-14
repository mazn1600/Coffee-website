<?php
// Include database functions
require_once 'db_functions.php';

// Start session
session_start();

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect to home page
    header('Location: index.php');
    exit;
}

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $is_admin = isset($_POST['is_admin']) && $_POST['is_admin'] === 'yes';
    $secret_code = $_POST['secret_code'] ?? '';
    
    // Validate password match
    if ($password !== $confirm_password) {
        $error_message = 'كلمات المرور غير متطابقة';
    }
    // Validate password length
    else if (strlen($password) < 8) {
        $error_message = 'يجب أن تكون كلمة المرور 8 أحرف على الأقل';
    }
    // Validate admin registration
    else if ($is_admin && $secret_code !== '1212') {
        $error_message = 'رمز التحقق غير صحيح';
    }
    else {
        // Register user
        $role = $is_admin ? 'admin' : 'customer';
        $result = registerUser($name, $email, $password, $role);
        
        if ($result) {
            // Set success message
            $_SESSION['register_success'] = true;
            // Redirect to login page
            header('Location: login.php');
            exit;
        } else {
            $error_message = 'البريد الإلكتروني مستخدم بالفعل';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>تسجيل حساب جديد | قهوتنا</title>
  <link rel="stylesheet" href="styles.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
    .admin-options {
      margin: 15px 0;
      padding: 15px;
      border: 1px solid #d5c1a2;
      border-radius: 4px;
      background: #f9f5f0;
      text-align: right;
    }
    
    .admin-options label {
      display: flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      color: #6f4e37;
      font-size: 1.1em;
    }
    
    .admin-options input[type="checkbox"] {
      width: 18px;
      height: 18px;
      cursor: pointer;
      accent-color: #6f4e37;
    }
    
    .secret-code-field {
      display: none;
      margin-top: 10px;
    }
    
    .secret-code-field.show {
      display: block;
    }

    .secret-code-field input {
      width: 100%;
      padding: 10px;
      border: 1px solid #d5c1a2;
      border-radius: 4px;
      font-size: 1em;
      color: #6f4e37;
      background: #fff;
    }

    .secret-code-field input:focus {
      outline: none;
      border-color: #6f4e37;
    }
  </style>
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
            <h1>تسجيل حساب جديد</h1>
            <p>أنشئ حسابك للبدء في التسوق معنا</p>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <form class="auth-form" method="post" action="register.php">
                <input type="text" name="name" placeholder="الاسم الكامل" required />
                <input type="email" name="email" placeholder="البريد الإلكتروني" required />
                <div class="form-group">
                    <div class="password-container" style="position: relative;">
                        <input type="password" id="password" name="password" placeholder="كلمة المرور" required style="padding-left: 40px;">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6f4e37; display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; transition: color 0.3s ease;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <div class="password-container" style="position: relative;">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="تأكيد كلمة المرور" required style="padding-left: 40px;">
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6f4e37; display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; transition: color 0.3s ease;">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="admin-options">
                    <label>
                        <input type="checkbox" name="is_admin" value="yes" onchange="toggleSecretCode(this)">
                        تسجيل حساب اداري
                    </label>
                    <div class="secret-code-field" id="secretCodeField">
                        <input type="password" name="secret_code" placeholder="ادخل المفتاح" />
                    </div>
                </div>
                
                <button type="submit">تسجيل</button>
                <div class="form-links">
                    <a href="login.php">لديك حساب بالفعل؟ سجل دخولك</a>
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

    function toggleSecretCode(checkbox) {
        const secretCodeField = document.getElementById('secretCodeField');
        if (checkbox.checked) {
            secretCodeField.classList.add('show');
            secretCodeField.querySelector('input').required = true;
        } else {
            secretCodeField.classList.remove('show');
            secretCodeField.querySelector('input').required = false;
        }
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
