<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    exit;
}

function clean($value) {
    return trim(filter_var($value, FILTER_SANITIZE_STRING));
}

$name               = clean($_POST['name'] ?? '');
$email              = trim($_POST['email'] ?? '');
$pickup             = clean($_POST['pickup'] ?? '');
$delivery           = clean($_POST['delivery'] ?? '');
$phone              = clean($_POST['phone'] ?? '');
$freight_type       = clean($_POST['freight_type'] ?? '');
$weight_dimensions  = clean($_POST['weight_dimensions'] ?? '');
$special_instr      = clean($_POST['special_instructions'] ?? '');

$errors = [];

if ($name === '') $errors[] = 'Name is required.';
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
if ($pickup === '') $errors[] = 'Pickup location is required.';
if ($delivery === '') $errors[] = 'Delivery location is required.';
if ($phone === '') $errors[] = 'Phone number is required.';
if ($freight_type === '') $freight_type = 'Not specified';
if ($weight_dimensions === '') $weight_dimensions = 'Not specified';
if ($special_instr === '') $special_instr = 'None';

if (!empty($errors)) {
    echo json_encode(['status' => 'error', 'message' => implode('<br>', $errors)]);
    exit;
}

$to = 'Dispatch@dslgoc.com';
$subject = 'New Freight Quote Request from Website';

$body  = "New freight quote request from Diamond Star Logistics website:\n\n";
$body .= "Name: {$name}\n";
$body .= "Email: {$email}\n";
$body .= "Phone: {$phone}\n\n";
$body .= "Pickup Location: {$pickup}\n";
$body .= "Delivery Location: {$delivery}\n";
$body .= "Freight Type: {$freight_type}\n";
$body .= "Weight & Dimensions: {$weight_dimensions}\n\n";
$body .= "Special Instructions:\n{$special_instr}\n\n";
$body .= "Submitted on: " . date('Y-m-d H:i:s') . "\n";

$headers  = "From: noreply@dslgoc.com\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

if (mail($to, $subject, $body, $headers)) {
    echo json_encode(['status' => 'success', 'message' => 'Quote request sent successfully.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Please try again later.']);
}
