<?php
// Security: Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Method not allowed']));
}

// Security: Set content type to JSON
header('Content-Type: application/json');

// Security: Validate and sanitize input
$name = isset($_POST["name"]) ? strip_tags(trim($_POST["name"])) : '';
$email = isset($_POST["email"]) ? filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL) : '';
$message = isset($_POST["message"]) ? trim($_POST["message"]) : '';

// Validation
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Name must be at least 2 characters long.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please provide a valid email address.';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Message must be at least 10 characters long.';
}

// Security: Prevent spam - basic length check
if (strlen($message) > 5000) {
    $errors[] = 'Message is too long. Please keep it under 5000 characters.';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// Email settings
$recipient = "admin@oceanit.uk";
$subject = "New Contact Form Message from " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

// Build email content - Security: Escape all output
$email_content = "New contact form submission from Oceanit website\n\n";
$email_content .= "Name: " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "\n";
$email_content .= "Email: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "\n\n";
$email_content .= "Message:\n" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "\n";
$email_content .= "\n---\n";
$email_content .= "Sent from: " . $_SERVER['HTTP_REFERER'] ?? 'Oceanit Website' . "\n";
$email_content .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
$email_content .= "Date: " . date('Y-m-d H:i:s') . "\n";

// Build email headers - Security: Prevent header injection
$headers = "From: admin@oceanit.uk\r\n";
$headers .= "Reply-To: " . filter_var($email, FILTER_SANITIZE_EMAIL) . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Send the email
if (mail($recipient, $subject, $email_content, $headers)) {
    echo json_encode(['success' => true, 'message' => 'Thank you! Your message has been sent successfully.']);
} else {
    error_log("Failed to send email from contact form. Name: $name, Email: $email");
    echo json_encode(['success' => false, 'message' => 'Sorry, there was an error sending your message. Please try again later.']);
}
?>

