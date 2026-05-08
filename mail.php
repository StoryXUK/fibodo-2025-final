<?php
/**
 * mail.php — GALAXY demo enquiry handler
 * Accepts POST from goodsystems.html, sends notification to jake.shand@fibodo.com
 */

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Helpers ───────────────────────────────────────────────────────────────────

function clean(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
}

function is_valid_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// ── Collect and sanitise ──────────────────────────────────────────────────────

$name          = clean($_POST['name']          ?? '');
$business_name = clean($_POST['business_name'] ?? '');
$email         = clean($_POST['email']         ?? '');
$phone         = clean($_POST['phone']         ?? '');
$business_type = clean($_POST['business_type'] ?? '');
$message       = clean($_POST['message']       ?? '');

// ── Validate required fields ──────────────────────────────────────────────────

$errors = [];

if ($name === '') {
    $errors[] = 'Name is required.';
}
if ($business_name === '') {
    $errors[] = 'Business name is required.';
}
if ($email === '' || !is_valid_email($email)) {
    $errors[] = 'A valid email address is required.';
}
if ($phone === '') {
    $errors[] = 'Phone number is required.';
}
if ($business_type === '') {
    $errors[] = 'Business type is required.';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ── Build email ───────────────────────────────────────────────────────────────

$to      = 'jake.shand@fibodo.com, james.murphy@fibodo.com';
$subject = 'GALAXY demo enquiry — ' . $business_name;

$body  = "A new GALAXY demo enquiry has been submitted via goodsystems.html.\n\n";
$body .= "Name:          {$name}\n";
$body .= "Business name: {$business_name}\n";
$body .= "Email:         {$email}\n";
$body .= "Phone:         {$phone}\n";
$body .= "Business type: {$business_type}\n";

if ($message !== '') {
    $body .= "\nMessage:\n{$message}\n";
}

$body .= "\n--\nSent from fibodo.com/goodsystems";

// Reply-To so Jake can respond directly to the enquirer
$headers  = "From: GALAXY Enquiries <noreply@fibodo.com>\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// ── Send ──────────────────────────────────────────────────────────────────────

$sent = mail($to, $subject, $body, $headers);

if ($sent) {
    echo json_encode(['success' => true, 'message' => "Thank you \u2014 we'll be in touch shortly."]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Sorry, the message could not be sent. Please email us directly at contact@fibodo.com.']);
}
