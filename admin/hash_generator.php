<?php
// Access this file via browser once to generate the password hash
// URL: http://localhost/Oceanit/admin/hash_generator.php
// Then copy the hash and paste it into login.php

$password = 'oceanit2024';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h2>Password Hash Generator</h2>";
echo "<p><strong>Password:</strong> oceanit2024</p>";
echo "<p><strong>Generated Hash:</strong></p>";
echo "<textarea style='width: 100%; height: 100px; font-family: monospace;'>" . $hash . "</textarea>";
echo "<p>Copy this hash and replace the \$admin_hash value in login.php</p>";

// Test verification
if (password_verify($password, $hash)) {
    echo "<p style='color: green;'><strong>✓ Hash verification successful!</strong></p>";
} else {
    echo "<p style='color: red;'><strong>✗ Hash verification failed!</strong></p>";
}
?>

