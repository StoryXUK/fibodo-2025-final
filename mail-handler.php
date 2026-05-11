<?php
/**
 * Shared GALAXY demo enquiry mailer.
 * Called by mail-hero.php and mail-cta.php with a $source label.
 *
 * Expects POST fields: name, business_name, email, phone, business_type, message
 * Returns JSON: { "success": true|false, "message": "..." }
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Config ────────────────────────────────────────────────────────────────────
const RECIPIENT  = 'james@story-x.co.uk';
const FROM_NAME  = 'fibodo Website';
const FROM_EMAIL = 'noreply@fibodo.com';
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Sanitise a plain-text field: strip tags, trim, limit length.
 */
function clean(string $value, int $maxLen = 255): string
{
    return mb_substr(trim(strip_tags($value)), 0, $maxLen);
}

// ── Collect & validate ────────────────────────────────────────────────────────
$name          = clean($_POST['name']          ?? '');
$business_name = clean($_POST['business_name'] ?? '');
$email         = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$phone         = clean($_POST['phone']         ?? '', 30);
$business_type = clean($_POST['business_type'] ?? '');
$message       = clean($_POST['message']       ?? '', 2000);
$source        = clean($source                 ?? 'Unknown form', 60); // set by caller

$errors = [];
if ($name          === '') $errors[] = 'Name is required.';
if ($business_name === '') $errors[] = 'Business name is required.';
if (!$email)               $errors[] = 'A valid email address is required.';
if ($phone         === '') $errors[] = 'Phone number is required.';
if ($business_type === '') $errors[] = 'Business type is required.';

if ($errors) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ── Build email ───────────────────────────────────────────────────────────────
$subject = "GALAXY demo enquiry — {$business_name}";

$body  = "New GALAXY demo enquiry from fibodo.com\n";
$body .= "Source: {$source}\n";
$body .= str_repeat('-', 48) . "\n\n";
$body .= "Name:          {$name}\n";
$body .= "Business name: {$business_name}\n";
$body .= "Business type: {$business_type}\n";
$body .= "Email:         {$email}\n";
$body .= "Phone:         {$phone}\n";

if ($message !== '') {
    $body .= "\nMessage:\n{$message}\n";
}

$headers  = "From: " . FROM_NAME . " <" . FROM_EMAIL . ">\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";
$headers .= "MIME-Version: 1.0\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

// ── Send ──────────────────────────────────────────────────────────────────────
$sent = mail(RECIPIENT, $subject, $body, $headers);

if ($sent) {
    echo json_encode([
        'success' => true,
        'message' => "Thanks {$name}, we'll be in touch shortly to arrange your GALAXY demo.",
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Sorry, we could not send your enquiry right now. Please email us at contact@fibodo.com.',
    ]);
}
