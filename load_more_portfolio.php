<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

try {
    // Get total count
    $countStmt = $pdo->query("SELECT COUNT(*) FROM portfolio");
    $totalItems = $countStmt->fetchColumn();
    $totalPages = ceil($totalItems / $perPage);
    
    // Get portfolio items for current page
    $stmt = $pdo->prepare("SELECT * FROM portfolio ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $portfolios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $html = '';
    foreach ($portfolios as $portfolio) {
        $linkAttr = !empty($portfolio['link']) ? 'onclick="window.open(\'' . htmlspecialchars($portfolio['link'], ENT_QUOTES) . '\', \'_blank\')" style="cursor: pointer;"' : '';
        $html .= '<div class="portfolio-item" ' . $linkAttr . '>';
        $html .= '<div class="portfolio-image">';
        if (!empty($portfolio['image_path'])) {
            $html .= '<img src="' . htmlspecialchars($portfolio['image_path']) . '" alt="' . htmlspecialchars($portfolio['project_name']) . '" loading="lazy">';
        } else {
            $html .= '<img src="assets/images/logo.png" alt="' . htmlspecialchars($portfolio['project_name']) . '" loading="lazy">';
        }
        $html .= '<div class="portfolio-overlay">';
        $html .= '<h4>' . htmlspecialchars($portfolio['project_name']) . '</h4>';
        $html .= '<p>' . htmlspecialchars(substr($portfolio['description'], 0, 50)) . '...</p>';
        if (!empty($portfolio['link'])) {
            $html .= '<a href="' . htmlspecialchars($portfolio['link']) . '" target="_blank" class="portfolio-link" style="margin-top: 1rem; color: white; text-decoration: underline;">View Project</a>';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'hasMore' => $page < $totalPages,
        'currentPage' => $page,
        'totalPages' => $totalPages
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

