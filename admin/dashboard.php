<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once '../db_connect.php';

/**
 * Compress and resize image to approximately 600KB
 * @param string $source_path Path to source image
 * @param string $target_path Path to save compressed image
 * @param int $max_size_kb Maximum file size in KB (default: 600)
 * @return bool True on success, False on failure
 */
function compressImage($source_path, $target_path, $max_size_kb = 600) {
    // Check if GD library is available
    if (!function_exists('imagecreatefromjpeg')) {
        return false;
    }
    
    // Get image info
    $image_info = getimagesize($source_path);
    if ($image_info === false) {
        return false;
    }
    
    $mime_type = $image_info['mime'];
    $original_width = $image_info[0];
    $original_height = $image_info[1];
    
    // Maximum width for web (1920px is good for most displays)
    $max_width = 1920;
    $max_height = 1920;
    
    // Calculate new dimensions if image is too large
    $ratio = min($max_width / $original_width, $max_height / $original_height, 1);
    $new_width = (int)($original_width * $ratio);
    $new_height = (int)($original_height * $ratio);
    
    // Create image resource based on type
    switch ($mime_type) {
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($source_path);
            break;
        case 'image/png':
            $source_image = imagecreatefrompng($source_path);
            break;
        case 'image/gif':
            $source_image = imagecreatefromgif($source_path);
            break;
        case 'image/webp':
            if (function_exists('imagecreatefromwebp')) {
                $source_image = imagecreatefromwebp($source_path);
            } else {
                return false;
            }
            break;
        default:
            return false;
    }
    
    if ($source_image === false) {
        return false;
    }
    
    // Create new image with calculated dimensions
    $new_image = imagecreatetruecolor($new_width, $new_height);
    
    // Preserve transparency for PNG and GIF
    if ($mime_type == 'image/png' || $mime_type == 'image/gif') {
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);
        $transparent = imagecolorallocatealpha($new_image, 0, 0, 0, 127);
        imagefill($new_image, 0, 0, $transparent);
    }
    
    // Resize image
    imagecopyresampled($new_image, $source_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
    
    // Binary search for quality to achieve target file size
    $target_size = $max_size_kb * 1024; // Convert to bytes
    $min_quality = 40;
    $max_quality = 100;
    $best_quality = 85;
    $best_path = '';
    
    // Try to find optimal quality
    for ($quality = $max_quality; $quality >= $min_quality; $quality -= 5) {
        $temp_path = $target_path . '.tmp';
        
        // Save as JPEG (best compression)
        imagejpeg($new_image, $temp_path, $quality);
        
        $file_size = filesize($temp_path);
        
        if ($file_size <= $target_size) {
            $best_quality = $quality;
            $best_path = $temp_path;
            break;
        }
        
        // If we're getting close, use this quality
        if ($file_size <= $target_size * 1.2) {
            $best_quality = $quality;
            $best_path = $temp_path;
            break;
        }
        
        // Clean up temp file if not suitable
        if (file_exists($temp_path)) {
            @unlink($temp_path);
        }
    }
    
    // If we found a suitable quality, move temp file to target
    if ($best_path && file_exists($best_path)) {
        if (file_exists($target_path)) {
            @unlink($target_path);
        }
        rename($best_path, $target_path);
    } else {
        // Fallback: save with default quality
        imagejpeg($new_image, $target_path, 85);
    }
    
    // Clean up
    imagedestroy($source_image);
    imagedestroy($new_image);
    
    return true;
}

$message = '';
$message_type = '';
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'portfolio';

// Handle Portfolio Operations
if (isset($_POST['add_portfolio'])) {
    $project_name = trim($_POST['project_name']);
    $description = trim($_POST['description']);
    $link = trim($_POST['link']);
    
    // Handle file upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        
        // Ensure upload directory exists and is writable
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            // Always save as JPEG for better compression
            $file_name = uniqid() . '_' . time() . '.jpg';
            $temp_path = $upload_dir . 'temp_' . $file_name;
            $target_path = $upload_dir . $file_name;
            
            // Move uploaded file to temp location first
            if (move_uploaded_file($_FILES['image']['tmp_name'], $temp_path)) {
                // Compress and resize image to ~600KB
                if (compressImage($temp_path, $target_path, 600)) {
                    // Delete temp file
                    @unlink($temp_path);
                    $image_path = 'uploads/' . $file_name;
                } else {
                    // If compression fails, try to use original (fallback)
                    if (file_exists($temp_path)) {
                        rename($temp_path, $target_path);
                        $image_path = 'uploads/' . $file_name;
                    } else {
                        $message = 'Error compressing image file. Please try again.';
                        $message_type = 'error';
                    }
                }
            } else {
                $message = 'Error uploading image file. Please try again.';
                $message_type = 'error';
            }
        } else {
            $message = 'Invalid file type. Allowed types: JPG, JPEG, PNG, GIF, WEBP';
            $message_type = 'error';
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO portfolio (project_name, description, link, image_path) VALUES (?, ?, ?, ?)");
        $stmt->execute([$project_name, $description, $link, $image_path]);
        $message = 'Portfolio item added successfully!';
        $message_type = 'success';
        $active_tab = 'portfolio';
    } catch (PDOException $e) {
        $message = 'Error adding portfolio item: ' . $e->getMessage();
        $message_type = 'error';
    }
}

if (isset($_POST['edit_portfolio'])) {
    $id = $_POST['portfolio_id'];
    $project_name = trim($_POST['project_name']);
    $description = trim($_POST['description']);
    $link = trim($_POST['link']);
    
    // Get old image path
    $stmt = $pdo->prepare("SELECT image_path FROM portfolio WHERE id = ?");
    $stmt->execute([$id]);
    $old_image = $stmt->fetchColumn();
    
    $image_path = $old_image; // Keep old image by default
    
    // Handle new file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        
        // Ensure upload directory exists and is writable
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            // Always save as JPEG for better compression
            $file_name = uniqid() . '_' . time() . '.jpg';
            $temp_path = $upload_dir . 'temp_' . $file_name;
            $target_path = $upload_dir . $file_name;
            
            // Move uploaded file to temp location first
            if (move_uploaded_file($_FILES['image']['tmp_name'], $temp_path)) {
                // Compress and resize image to ~600KB
                if (compressImage($temp_path, $target_path, 600)) {
                    // Delete temp file
                    @unlink($temp_path);
                    // Delete old image if exists
                    if ($old_image && !empty($old_image) && file_exists('../' . $old_image)) {
                        @unlink('../' . $old_image);
                    }
                    $image_path = 'uploads/' . $file_name;
                } else {
                    // If compression fails, try to use original (fallback)
                    if (file_exists($temp_path)) {
                        rename($temp_path, $target_path);
                        // Delete old image if exists
                        if ($old_image && !empty($old_image) && file_exists('../' . $old_image)) {
                            @unlink('../' . $old_image);
                        }
                        $image_path = 'uploads/' . $file_name;
                    } else {
                        $message = 'Error compressing image file. Please try again.';
                        $message_type = 'error';
                    }
                }
            } else {
                $message = 'Error uploading image file. Please try again.';
                $message_type = 'error';
            }
        } else {
            $message = 'Invalid file type. Allowed types: JPG, JPEG, PNG, GIF, WEBP';
            $message_type = 'error';
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Handle upload errors
        $upload_errors = array(
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
        );
        $error_code = $_FILES['image']['error'];
        $message = 'Upload error: ' . (isset($upload_errors[$error_code]) ? $upload_errors[$error_code] : 'Unknown error');
        $message_type = 'error';
    }
    
    // Only update if no error occurred
    if ($message_type !== 'error') {
        try {
            $stmt = $pdo->prepare("UPDATE portfolio SET project_name = ?, description = ?, link = ?, image_path = ? WHERE id = ?");
            $stmt->execute([$project_name, $description, $link, $image_path, $id]);
            if (empty($message)) {
                $message = 'Portfolio item updated successfully!';
            }
            $message_type = 'success';
            $active_tab = 'portfolio';
        } catch (PDOException $e) {
            $message = 'Error updating portfolio item: ' . $e->getMessage();
            $message_type = 'error';
        }
    } else {
        $active_tab = 'portfolio';
    }
}

if (isset($_GET['delete_portfolio'])) {
    $id = $_GET['delete_portfolio'];
    
    try {
        // Get image path before deleting
        $stmt = $pdo->prepare("SELECT image_path FROM portfolio WHERE id = ?");
        $stmt->execute([$id]);
        $image_path = $stmt->fetchColumn();
        
        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM portfolio WHERE id = ?");
        $stmt->execute([$id]);
        
        // Delete image file from server
        if ($image_path && file_exists('../' . $image_path)) {
            unlink('../' . $image_path);
        }
        
        $message = 'Portfolio item deleted successfully!';
        $message_type = 'success';
        $active_tab = 'portfolio';
    } catch (PDOException $e) {
        $message = 'Error deleting portfolio item: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Handle Reviews Operations
if (isset($_POST['add_review'])) {
    $client_name = trim($_POST['client_name']);
    $business_name = trim($_POST['business_name']);
    $comment = trim($_POST['comment']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO reviews (client_name, business_name, comment) VALUES (?, ?, ?)");
        $stmt->execute([$client_name, $business_name, $comment]);
        $message = 'Review added successfully!';
        $message_type = 'success';
        $active_tab = 'reviews';
    } catch (PDOException $e) {
        $message = 'Error adding review: ' . $e->getMessage();
        $message_type = 'error';
    }
}

if (isset($_POST['edit_review'])) {
    $id = $_POST['review_id'];
    $client_name = trim($_POST['client_name']);
    $business_name = trim($_POST['business_name']);
    $comment = trim($_POST['comment']);
    
    try {
        $stmt = $pdo->prepare("UPDATE reviews SET client_name = ?, business_name = ?, comment = ? WHERE id = ?");
        $stmt->execute([$client_name, $business_name, $comment, $id]);
        $message = 'Review updated successfully!';
        $message_type = 'success';
        $active_tab = 'reviews';
    } catch (PDOException $e) {
        $message = 'Error updating review: ' . $e->getMessage();
        $message_type = 'error';
    }
}

if (isset($_GET['delete_review'])) {
    $id = $_GET['delete_review'];
    try {
        $stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Review deleted successfully!';
        $message_type = 'success';
        $active_tab = 'reviews';
    } catch (PDOException $e) {
        $message = 'Error deleting review: ' . $e->getMessage();
        $message_type = 'error';
    }
}

// Get edit data
$edit_portfolio = null;
if (isset($_GET['edit_portfolio'])) {
    $stmt = $pdo->prepare("SELECT * FROM portfolio WHERE id = ?");
    $stmt->execute([$_GET['edit_portfolio']]);
    $edit_portfolio = $stmt->fetch();
    $active_tab = 'portfolio';
}

$edit_review = null;
if (isset($_GET['edit_review'])) {
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ?");
    $stmt->execute([$_GET['edit_review']]);
    $edit_review = $stmt->fetch();
    $active_tab = 'reviews';
}

// Fetch all data
$portfolios = $pdo->query("SELECT * FROM portfolio ORDER BY id DESC")->fetchAll();
$reviews = $pdo->query("SELECT * FROM reviews ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oceanit - Admin Dashboard</title>
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
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            min-height: 100vh;
            padding: 2rem;
            color: var(--text-primary);
        }

        .dashboard-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .dashboard-header {
            background: rgba(17, 34, 64, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid rgba(100, 255, 218, 0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-container img {
            height: 50px;
            width: auto;
        }

        .logo-text {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: 1px;
        }

        .logout-btn {
            padding: 0.8rem 1.5rem;
            background-color: transparent;
            color: var(--secondary-color);
            border: 2px solid var(--secondary-color);
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .logout-btn:hover {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }

        .message {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            text-align: center;
        }

        .message.success {
            background-color: rgba(16, 185, 129, 0.2);
            border: 1px solid rgba(16, 185, 129, 0.5);
            color: #10b981;
        }

        .message.error {
            background-color: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #ef4444;
        }

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            background: rgba(17, 34, 64, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 1rem;
            border: 1px solid rgba(100, 255, 218, 0.1);
        }

        .tab-btn {
            flex: 1;
            padding: 1rem 2rem;
            background: transparent;
            border: 2px solid transparent;
            color: var(--text-secondary);
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
        }

        .tab-btn.active {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            border-color: var(--secondary-color);
        }

        .tab-btn:hover:not(.active) {
            border-color: var(--secondary-color);
            color: var(--secondary-color);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .section {
            background: rgba(17, 34, 64, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            border: 1px solid rgba(100, 255, 218, 0.1);
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--secondary-color);
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

        .form-group input,
        .form-group textarea,
        .form-group select {
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

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(100, 255, 218, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .form-group input[type="file"] {
            padding: 0.5rem;
        }

        .current-image {
            margin-top: 0.5rem;
            padding: 1rem;
            background: var(--primary-color);
            border-radius: 8px;
            border: 1px solid rgba(100, 255, 218, 0.2);
        }

        .current-image img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 8px;
            margin-top: 0.5rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .btn {
            padding: 0.9rem 2rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(100, 255, 218, 0.3);
        }

        .btn-danger {
            background-color: #ef4444;
            color: var(--white);
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-edit {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            margin-right: 0.5rem;
        }

        .btn-edit:hover {
            background-color: var(--white);
        }

        .table-container {
            overflow-x: auto;
            margin-top: 2rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(100, 255, 218, 0.1);
        }

        th {
            background-color: var(--primary-color);
            color: var(--secondary-color);
            font-weight: 600;
        }

        tr:hover {
            background-color: rgba(100, 255, 218, 0.05);
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .table-image {
            max-width: 80px;
            max-height: 80px;
            border-radius: 8px;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
            }

            .table-container {
                overflow-x: scroll;
            }

            .tabs {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <div class="logo-container">
                <img src="../assets/images/logo.png" alt="Oceanit Logo">
                <span class="logo-text">Oceanit</span>
            </div>
            <a href="login.php?logout=1" class="logout-btn">Logout</a>
        </div>

        <?php if ($message): ?>
            <div class="message <?php echo $message_type; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn <?php echo $active_tab === 'portfolio' ? 'active' : ''; ?>" onclick="switchTab('portfolio')">
                Portfolio
            </button>
            <button class="tab-btn <?php echo $active_tab === 'reviews' ? 'active' : ''; ?>" onclick="switchTab('reviews')">
                Reviews
            </button>
        </div>

        <!-- Portfolio Tab -->
        <div id="portfolio-tab" class="tab-content <?php echo $active_tab === 'portfolio' ? 'active' : ''; ?>">
            <div class="section">
                <h2 class="section-title">Portfolio Management</h2>
                
                <form method="POST" action="" enctype="multipart/form-data">
                    <?php if ($edit_portfolio): ?>
                        <input type="hidden" name="portfolio_id" value="<?php echo $edit_portfolio['id']; ?>">
                        <input type="hidden" name="edit_portfolio" value="1">
                    <?php else: ?>
                        <input type="hidden" name="add_portfolio" value="1">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="project_name">Project Name</label>
                        <input type="text" id="project_name" name="project_name" 
                               value="<?php echo $edit_portfolio ? htmlspecialchars($edit_portfolio['project_name']) : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="image">Image <?php echo $edit_portfolio ? '(Leave empty to keep current image)' : ''; ?></label>
                        <input type="file" id="image" name="image" accept="image/*" <?php echo $edit_portfolio ? '' : 'required'; ?>>
                        <?php if ($edit_portfolio && $edit_portfolio['image_path']): ?>
                            <div class="current-image">
                                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">Current Image:</p>
                                <img src="../<?php echo htmlspecialchars($edit_portfolio['image_path']); ?>" alt="Current image">
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" required><?php echo $edit_portfolio ? htmlspecialchars($edit_portfolio['description']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="link">Link</label>
                        <input type="url" id="link" name="link" 
                               value="<?php echo $edit_portfolio ? htmlspecialchars($edit_portfolio['link']) : ''; ?>" 
                               placeholder="https://example.com" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_portfolio ? 'Update Portfolio Item' : 'Add Portfolio Item'; ?>
                    </button>
                    <?php if ($edit_portfolio): ?>
                        <a href="?tab=portfolio" class="btn" style="background: var(--text-secondary); color: var(--white); margin-left: 1rem;">Cancel</a>
                    <?php endif; ?>
                </form>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Project Name</th>
                                <th>Description</th>
                                <th>Link</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($portfolios as $portfolio): ?>
                            <tr>
                                <td><?php echo $portfolio['id']; ?></td>
                                <td>
                                    <?php if ($portfolio['image_path']): ?>
                                        <img src="../<?php echo htmlspecialchars($portfolio['image_path']); ?>" 
                                             alt="<?php echo htmlspecialchars($portfolio['project_name']); ?>" 
                                             class="table-image">
                                    <?php else: ?>
                                        No image
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($portfolio['project_name']); ?></td>
                                <td><?php echo htmlspecialchars(substr($portfolio['description'], 0, 50)) . '...'; ?></td>
                                <td><a href="<?php echo htmlspecialchars($portfolio['link']); ?>" target="_blank" style="color: var(--secondary-color);">View Link</a></td>
                                <td class="actions">
                                    <a href="?edit_portfolio=<?php echo $portfolio['id']; ?>&tab=portfolio" class="btn btn-edit">Edit</a>
                                    <a href="?delete_portfolio=<?php echo $portfolio['id']; ?>&tab=portfolio" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($portfolios)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center; color: var(--text-secondary);">No portfolio items yet.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reviews Tab -->
        <div id="reviews-tab" class="tab-content <?php echo $active_tab === 'reviews' ? 'active' : ''; ?>">
            <div class="section">
                <h2 class="section-title">Reviews Management</h2>
                
                <form method="POST" action="">
                    <?php if ($edit_review): ?>
                        <input type="hidden" name="review_id" value="<?php echo $edit_review['id']; ?>">
                        <input type="hidden" name="edit_review" value="1">
                    <?php else: ?>
                        <input type="hidden" name="add_review" value="1">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="client_name">Client Name</label>
                            <input type="text" id="client_name" name="client_name" 
                                   value="<?php echo $edit_review ? htmlspecialchars($edit_review['client_name']) : ''; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="business_name">Business Name</label>
                            <input type="text" id="business_name" name="business_name" 
                                   value="<?php echo $edit_review ? htmlspecialchars($edit_review['business_name']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="comment">Comment</label>
                        <textarea id="comment" name="comment" required><?php echo $edit_review ? htmlspecialchars($edit_review['comment']) : ''; ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_review ? 'Update Review' : 'Add Review'; ?>
                    </button>
                    <?php if ($edit_review): ?>
                        <a href="?tab=reviews" class="btn" style="background: var(--text-secondary); color: var(--white); margin-left: 1rem;">Cancel</a>
                    <?php endif; ?>
                </form>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client Name</th>
                                <th>Business Name</th>
                                <th>Comment</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reviews as $review): ?>
                            <tr>
                                <td><?php echo $review['id']; ?></td>
                                <td><?php echo htmlspecialchars($review['client_name']); ?></td>
                                <td><?php echo htmlspecialchars($review['business_name']); ?></td>
                                <td><?php echo htmlspecialchars(substr($review['comment'], 0, 100)) . '...'; ?></td>
                                <td class="actions">
                                    <a href="?edit_review=<?php echo $review['id']; ?>&tab=reviews" class="btn btn-edit">Edit</a>
                                    <a href="?delete_review=<?php echo $review['id']; ?>&tab=reviews" 
                                       class="btn btn-danger" 
                                       onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (empty($reviews)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-secondary);">No reviews yet.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tab + '-tab').classList.add('active');
            
            // Add active class to clicked button
            event.target.classList.add('active');
            
            // Update URL without page reload
            window.history.pushState({}, '', '?tab=' + tab);
        }
    </script>
</body>
</html>
