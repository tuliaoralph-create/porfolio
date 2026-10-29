<?php
/**
 * Contact Form API Handler
 */
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$subject = $_POST['subject'] ?? 'General Inquiry';
$message = $_POST['message'] ?? '';

$result = saveContactInquiry($name, $email, $subject, $message);
echo json_encode($result);
exit;
