<?php

declare(strict_types=1);

require __DIR__ . '/../bootstrap.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$input = json_decode((string) file_get_contents('php://input'), true) ?: [];

$firstName = trim((string) ($input['firstName'] ?? ''));
$lastName = trim((string) ($input['lastName'] ?? ''));
$email = strtolower(trim((string) ($input['email'] ?? '')));
$phone = trim((string) ($input['phone'] ?? ''));
$clinic = trim((string) ($input['clinic'] ?? ''));
$service = trim((string) ($input['service'] ?? ''));
$message = trim((string) ($input['message'] ?? ''));
$website = trim((string) ($input['website'] ?? ''));

if ($website !== '') {
    echo json_encode(['success' => true, 'message' => 'Thank you for your inquiry. We will contact you soon.']);
    exit;
}

if ($firstName === '' || $lastName === '' || $email === '' || $message === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid email format.']);
    exit;
}

$fullName = trim($firstName . ' ' . $lastName);

$stmt = cms_db()->prepare(
    'INSERT INTO inquiries (full_name, first_name, last_name, email, phone, clinic, service, message, status)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
);
$stmt->execute([
    mb_substr($fullName, 0, 120),
    mb_substr($firstName, 0, 80),
    mb_substr($lastName, 0, 80),
    mb_substr($email, 0, 160),
    $phone !== '' ? mb_substr($phone, 0, 40) : null,
    $clinic !== '' ? mb_substr($clinic, 0, 120) : null,
    $service !== '' ? mb_substr($service, 0, 80) : null,
    mb_substr($message, 0, 5000),
    'new',
]);

echo json_encode([
    'success' => true,
    'message' => 'Thank you for your inquiry. We will contact you soon.',
], JSON_UNESCAPED_SLASHES);
