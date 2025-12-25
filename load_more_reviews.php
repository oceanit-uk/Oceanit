<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 8;
$offset = ($page - 1) * $perPage;

try {
    // Get total count - Security: Using prepared statement
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM reviews");
    $countStmt->execute();
    $totalItems = $countStmt->fetchColumn();
    $totalPages = ceil($totalItems / $perPage);
    
    // Get reviews for current page
    $stmt = $pdo->prepare("SELECT * FROM reviews ORDER BY id DESC LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $html = '';
    foreach ($reviews as $review) {
        $html .= '<div class="review-card">';
        $html .= '<div class="review-rating">★★★★★</div>';
        $html .= '<p class="review-text">"' . htmlspecialchars($review['comment']) . '"</p>';
        $html .= '<div class="review-author">';
        $html .= '<div class="author-name">' . htmlspecialchars($review['client_name']) . '</div>';
        if (!empty($review['business_name'])) {
            $html .= '<div class="author-title">' . htmlspecialchars($review['business_name']) . '</div>';
        }
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

