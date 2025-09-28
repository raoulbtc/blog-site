<?php
require_once 'includes/functions.php';
session_start();

// Handle AJAX requests for captcha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'generate_captcha') {
        // Generate new 3-character captcha
        $captcha = generateSimpleCaptcha();
        $_SESSION['captcha_code'] = $captcha;
        echo json_encode(['success' => true, 'captcha' => $captcha]);
        exit;
    }
    
    if ($_POST['action'] === 'verify_subscription') {
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $captchaResponse = strtoupper(trim($_POST['captcha_response']));
        $captchaCode = $_SESSION['captcha_code'] ?? '';
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Invalid email format detected by security systems.']);
        } elseif ($captchaResponse !== $captchaCode) {
            echo json_encode(['success' => false, 'message' => 'Security verification failed. Please try again.']);
        } else {
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '';
            $result = saveSubscription($email, $ipAddress);
            echo json_encode($result);
            
            // Clear captcha after use
            unset($_SESSION['captcha_code']);
        }
        exit;
    }
}

// Handle admin login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'admin_login') {
    $password = $_POST['admin_password'];
    if ($password === 'classified2025') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: index.php?page=admin");
        exit;
    } else {
        header("Location: index.php?page=admin&login_error=1");
        exit;
    }
}

// Get the current page
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$period = isset($_GET['period']) ? $_GET['period'] : '';

// Load blog posts data
$posts = getAllPosts();

// Handle different pages
switch ($page) {
    case 'post':
        $currentPost = getPostById($posts, $postId);
        if (!$currentPost) {
            $page = 'home';
        }
        $pageTitle = $currentPost ? $currentPost['title'] . ' - Intelligence Network' : 'Classified Intelligence Network';
        break;
        
    case 'archive':
        $archivePosts = getPostsByPeriod($posts, $period);
        $periodName = getPeriodName($period);
        if (empty($archivePosts)) {
            $page = 'home';
        }
        $pageTitle = $periodName ? 'Archives: ' . $periodName . ' - Intelligence Network' : 'Classified Intelligence Network';
        break;
        
    case 'about':
        $pageTitle = 'Classified Information - Intelligence Network';
        break;
        
    case 'contact':
        $pageTitle = 'Secure Contact - Intelligence Network';
        break;
        
    case 'admin':
        $pageTitle = 'Admin Panel - Intelligence Network';
        if (!isset($_SESSION['admin_logged_in'])) {
            $showAdminLogin = true;
        } else {
            $subscribers = getAllSubscriptions();
        }
        break;
        
    case 'logout':
        session_destroy();
        header("Location: index.php");
        exit;
        
    default:
        $page = 'home';
        $pageTitle = 'Classified Intelligence Network';
        $featuredPosts = getFeaturedPosts($posts);
        $recentPosts = getRecentPosts($posts, 12);
        $archiveData = getArchiveData($posts);
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <?php include 'includes/header.php'; ?>

        <main class="content">
            <?php
            switch ($page) {
                case 'post':
                    include 'includes/pages/post.php';
                    break;
                    
                case 'archive':
                    include 'includes/pages/archive.php';
                    break;
                    
                case 'about':
                    include 'includes/pages/about.php';
                    break;
                    
                case 'contact':
                    include 'includes/pages/contact.php';
                    break;
                    
                case 'admin':
                    include 'includes/pages/admin.php';
                    break;
                    
                default:
                    include 'includes/pages/home.php';
                    break;
            }
            ?>
        </main>
    </div>

    <!-- Captcha Modal -->
    <div id="captcha-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>🔐 Security Verification</h3>
                <span class="modal-close" onclick="closeCaptchaModal()">&times;</span>
            </div>
            <div class="modal-body">
                <p class="captcha-instruction">Enter the 3 characters shown below to complete your registration:</p>
                
                <div class="captcha-display">
                    <span id="captcha-characters">ABC</span>
                    <button type="button" class="captcha-refresh" onclick="refreshCaptcha()" title="Generate new code">🔄</button>
                </div>
                
                <input type="text" id="captcha-input" placeholder="Enter 3 characters..." maxlength="3" class="captcha-verification-input">
                
                <div class="modal-actions">
                    <button onclick="verifyCaptcha()" class="btn-verify">Verify & Join Network</button>
                    <button onclick="closeCaptchaModal()" class="btn-cancel">Cancel</button>
                </div>
                
                <div id="captcha-message" class="captcha-message"></div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="success-modal" class="modal">
        <div class="modal-content modal-success">
            <div class="modal-header">
                <h3>✅ Mission Accomplished</h3>
                <span class="modal-close" onclick="closeSuccessModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div class="success-icon">🕵️</div>
                <p id="success-message">Welcome to the network, operative. Your secure briefings will begin shortly.</p>
                <button onclick="closeSuccessModal()" class="btn-success">Continue</button>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
    <script>
        let currentEmail = '';

        // Handle newsletter form submission
        function handleNewsletterSubmit(event) {
            event.preventDefault();
            const emailInput = document.querySelector('.newsletter-input');
            const email = emailInput.value.trim();
            
            if (!email) {
                showCaptchaMessage('Please enter your email address.', 'error');
                return;
            }
            
            if (!isValidEmail(email)) {
                showCaptchaMessage('Invalid email format detected by security systems.', 'error');
                return;
            }
            
            currentEmail = email;
            showCaptchaModal();
            refreshCaptcha();
        }

        function isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function showCaptchaModal() {
            document.getElementById('captcha-modal').style.display = 'flex';
            document.getElementById('captcha-input').value = '';
            document.getElementById('captcha-input').focus();
            clearCaptchaMessage();
        }

        function closeCaptchaModal() {
            document.getElementById('captcha-modal').style.display = 'none';
            currentEmail = '';
        }

        function showSuccessModal(message) {
            document.getElementById('success-message').textContent = message;
            document.getElementById('success-modal').style.display = 'flex';
        }

        function closeSuccessModal() {
            document.getElementById('success-modal').style.display = 'none';
            // Clear the email input
            document.querySelector('.newsletter-input').value = '';
        }

        function refreshCaptcha() {
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'ajax=1&action=generate_captcha'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('captcha-characters').textContent = data.captcha;
                    document.getElementById('captcha-input').value = '';
                    document.getElementById('captcha-input').focus();
                    clearCaptchaMessage();
                }
            })
            .catch(error => {
                console.error('Error generating captcha:', error);
            });
        }

        function verifyCaptcha() {
            const captchaResponse = document.getElementById('captcha-input').value.trim();
            
            if (!captchaResponse) {
                showCaptchaMessage('Please enter the security code.', 'error');
                return;
            }
            
            if (captchaResponse.length !== 3) {
                showCaptchaMessage('Please enter exactly 3 characters.', 'error');
                return;
            }
            
            // Show loading state
            showCaptchaMessage('Verifying security credentials...', 'info');
            
            fetch('index.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `ajax=1&action=verify_subscription&email=${encodeURIComponent(currentEmail)}&captcha_response=${encodeURIComponent(captchaResponse)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeCaptchaModal();
                    showSuccessModal(data.message);
                } else {
                    showCaptchaMessage(data.message, 'error');
                    refreshCaptcha(); // Generate new captcha on failure
                }
            })
            .catch(error => {
                console.error('Error verifying captcha:', error);
                showCaptchaMessage('Network error. Please try again.', 'error');
            });
        }

        function showCaptchaMessage(message, type) {
            const messageDiv = document.getElementById('captcha-message');
            messageDiv.textContent = message;
            messageDiv.className = `captcha-message ${type}`;
        }

        function clearCaptchaMessage() {
            document.getElementById('captcha-message').textContent = '';
            document.getElementById('captcha-message').className = 'captcha-message';
        }

        // Handle Enter key in captcha input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('captcha-input').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    verifyCaptcha();
                }
            });
            
            // Close modals when clicking outside
            window.onclick = function(event) {
                const captchaModal = document.getElementById('captcha-modal');
                const successModal = document.getElementById('success-modal');
                
                if (event.target === captchaModal) {
                    closeCaptchaModal();
                }
                if (event.target === successModal) {
                    closeSuccessModal();
                }
            }
        });
    </script>
</body>
</html>