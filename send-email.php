<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Validation
$errors = [];
if (empty($name) || strlen($name) < 2) $errors[] = 'Name is required (2+ chars)';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required';
if (empty($subject)) $errors[] = 'Subject required';
if (empty($message) || strlen($message) < 10) $errors[] = 'Message required (10+ chars)';

// Basic spam check
if (preg_match('/http|www|script|javascript|viagra|casino/i', $message)) {
    $errors[] = 'Spam detected';
}

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode('<br>', $errors)]);
    exit;
}

// Email configuration
$to = 'Dispatch@dslgoc.com';
$email_subject = 'New Contact Form: ' . $subject;
$email_body = "New message from Diamond Star Logistics website:\n\n";
$email_body .= "Name: $name\n";
$email_body .= "Email: $email\n";
$email_body .= "Subject: $subject\n";
$email_body .= "Message:\n$message\n\n";
$email_body .= "Sent: " . date('Y-m-d H:i:s');

$headers = "From: noreply@dslgoc.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail($to, $email_subject, $email_body, $headers)) {
    echo json_encode(['status' => 'success', 'message' => 'Email sent successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email']);
}
?>
