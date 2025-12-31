<?php
/**
 * Online Resume System - Admin Login Page
 * Cobalt Blue + White theme
 *
 * ULTRATHINK #255 - New Year's Eve Build
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

// Redirect if already logged in
redirectIfAuthenticated();

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid request. Please try again.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // Validate inputs
        if (empty($email) || empty($password)) {
            $error = 'Please enter both email and password.';
        } else {
            // Get user by email
            $user = getUserByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Login successful
                loginUser($user['id'], $user['email']);

                // Set success flash message
                setFlash('success', 'Welcome back! You are now logged in.');

                // Redirect to dashboard or original requested page
                $redirectUrl = $_SESSION['redirect_url'] ?? 'index.php';
                unset($_SESSION['redirect_url']);
                redirect($redirectUrl);
            } else {
                $error = 'Invalid email or password.';
            }
        }
    }
}

// Get flash message if any
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?= e(APP_NAME) ?></title>
    <link rel="stylesheet" href="<?= CSS_URL ?>/base.css">
    <style>
        /* Login Page Specific Styles */
        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--space-4);
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: var(--white);
            border-radius: var(--rounded-xl);
            box-shadow: var(--shadow-xl);
            padding: var(--space-8);
        }

        .login-header {
            text-align: center;
            margin-bottom: var(--space-8);
        }

        .login-logo {
            width: 64px;
            height: 64px;
            background: var(--primary);
            border-radius: var(--rounded-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto var(--space-4);
            color: var(--white);
            font-size: var(--text-2xl);
            font-weight: var(--font-bold);
        }

        .login-title {
            font-size: var(--text-2xl);
            color: var(--gray-900);
            margin-bottom: var(--space-2);
        }

        .login-subtitle {
            color: var(--gray-500);
            font-size: var(--text-sm);
        }

        .login-form .form-group {
            margin-bottom: var(--space-5);
        }

        .login-form .form-label {
            font-weight: var(--font-semibold);
            color: var(--gray-700);
        }

        .login-form .form-input {
            padding: var(--space-4);
            font-size: var(--text-base);
        }

        .login-form .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 71, 171, 0.1);
        }

        .password-wrapper {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: var(--space-3);
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-500);
            cursor: pointer;
            padding: var(--space-2);
        }

        .password-toggle:hover {
            color: var(--gray-700);
        }

        .login-btn {
            width: 100%;
            padding: var(--space-4);
            font-size: var(--text-base);
            font-weight: var(--font-semibold);
            margin-top: var(--space-2);
        }

        .login-footer {
            text-align: center;
            margin-top: var(--space-6);
            color: var(--gray-500);
            font-size: var(--text-sm);
        }

        .login-footer a {
            color: var(--primary);
            font-weight: var(--font-medium);
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        /* Error state */
        .form-input.error {
            border-color: var(--danger);
        }

        .form-input.error:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: var(--space-6);
            }

            .login-logo {
                width: 56px;
                height: 56px;
                font-size: var(--text-xl);
            }

            .login-title {
                font-size: var(--text-xl);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">R</div>
                <h1 class="login-title">Welcome Back</h1>
                <p class="login-subtitle">Sign in to manage your resume</p>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?>">
                    <?= e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="login-form" autocomplete="off">
                <?= csrfField() ?>

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-input <?= $error ? 'error' : '' ?>"
                        placeholder="admin@example.com"
                        value="<?= e($_POST['email'] ?? '') ?>"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input <?= $error ? 'error' : '' ?>"
                            placeholder="Enter your password"
                            required
                        >
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <svg id="eye-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary login-btn">
                    Sign In
                </button>
            </form>

            <div class="login-footer">
                <a href="../index.php">Back to Portfolio</a>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        }

        // Easter egg in console
        console.log('%c Powered by Kiyo Software TechLab', 'color: #0047AB; font-size: 14px; font-weight: bold;');
    </script>
</body>
</html>
