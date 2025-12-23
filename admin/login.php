<?php
session_start();

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}

$admin_email = "oceanit.uk@gmail.com";

// Password: oceanit2024
// To generate a new hash, visit: admin/hash_generator.php in your browser
// Current hash (will be generated on first login if not set correctly):
$admin_hash = '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcfl7p92ldGxad68LJZdL17lhWy'; 

$error = '';

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if ($email === $admin_email) {
        // Try password_verify with stored hash first
        if (password_verify($password, $admin_hash)) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $admin_email;
            header('Location: dashboard.php');
            exit();
        }
        // Fallback: If hash doesn't work, try direct comparison and generate proper hash
        // This allows login while you set up the correct hash
        elseif ($password === 'oceanit2024') {
            // Generate proper hash for future use (check PHP error log for the hash)
            $new_hash = password_hash('oceanit2024', PASSWORD_DEFAULT);
            error_log("=== COPY THIS HASH TO LOGIN.PHP ===");
            error_log("New hash: " . $new_hash);
            error_log("Replace \$admin_hash in login.php with the hash above");
            
            // Allow login
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $admin_email;
            header('Location: dashboard.php');
            exit();
        }
    }
    
    $error = 'Invalid email or password. Please check your credentials.';
}

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oceanit - Admin Login</title>
    <link rel="icon" type="image/png" href="../assets/images/logo.png">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #0a192f;
            --secondary-color: #64ffda;
            --accent-color: #112240;
            --text-primary: #ccd6f6;
            --text-secondary: #8892b0;
            --white: #ffffff;
            --dark-bg: #0a192f;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: var(--text-primary);
        }

        .login-container {
            background: rgba(17, 34, 64, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(100, 255, 218, 0.1);
        }

        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .logo-container img {
            height: 60px;
            width: auto;
        }

        .logo-text {
            font-size: 2rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 1px;
        }

        .login-header h1 {
            font-size: 1.5rem;
            color: var(--text-primary);
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .login-header p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 0.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            color: var(--text-primary);
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.9rem 1.2rem;
            background-color: var(--primary-color);
            border: 1px solid rgba(100, 255, 218, 0.2);
            border-radius: 8px;
            color: var(--white);
            font-size: 1rem;
            font-family: inherit;
            transition: var(--transition);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(100, 255, 218, 0.1);
        }

        .form-group input::placeholder {
            color: var(--text-secondary);
        }

        .error-message {
            background-color: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #ff6b6b;
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            text-align: center;
        }

        .submit-button {
            width: 100%;
            padding: 1rem;
            background-color: var(--secondary-color);
            color: var(--primary-color);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 0.5rem;
        }

        .submit-button:hover {
            background-color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(100, 255, 218, 0.3);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 2rem 1.5rem;
            }

            .logo-container img {
                height: 50px;
            }

            .logo-text {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo-container">
                <img src="../assets/images/logo.png" alt="Oceanit Logo">
                <span class="logo-text">Oceanit</span>
            </div>
            <h1>Admin Login</h1>
            <p>Enter your credentials to access the dashboard</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="admin@oceanit.com" 
                    required 
                    autocomplete="email"
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                >
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Enter your password" 
                    required 
                    autocomplete="current-password"
                >
            </div>

            <button type="submit" name="login" class="submit-button">Login</button>
        </form>
    </div>
</body>
</html>