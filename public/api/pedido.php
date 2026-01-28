<?php
// Simple endpoint for order requests with basic anti-spam and SMTP sending.

header("X-Content-Type-Options: nosniff");

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$isJson = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

function respond($ok, $message, $status = 200, $isJson = false) {
  http_response_code($status);
  if ($isJson) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(["ok" => $ok, "message" => $message]);
  } else {
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="es"><head><meta charset="utf-8"><title>Pedido</title></head><body>';
    echo '<p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
    echo '</body></html>';
  }
  exit;
}

function sanitize_text($value) {
  $value = trim((string)$value);
  $value = preg_replace('/\s+/', ' ', $value);
  return $value;
}

function contains_url($text) {
  return preg_match('/(https?:\/\/|www\.|\.(com|net|org|info|biz|io|co|es|mx|pe|ar|cl|br|uk|us))/i', $text) === 1;
}

$blockedWords = [
  'viagra', 'cialis', 'casino', 'bet', 'betting', 'loan', 'prestamo', 'préstamo', 'credito', 'crédito',
  'crypto', 'bitcoin', 'forex', 'investment', 'inversion', 'inversión', 'work from home',
  'dinero gratis', 'free money', 'adult', 'xxx', 'seo', 'backlink', 'affiliate', 'afiliado', 'promo code', 'descuento'
];

function contains_blocked_words($text, $blockedWords) {
  $lower = mb_strtolower($text, 'UTF-8');
  foreach ($blockedWords as $word) {
    if (str_contains($lower, $word)) {
      return true;
    }
  }
  return false;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  respond(false, 'Método no permitido.', 405, $isJson || $isAjax);
}

// Honeypot
if (!empty($_POST['website'])) {
  respond(false, 'Solicitud rechazada.', 400, $isJson || $isAjax);
}

// Minimum time check (anti-bot)
$startedAt = (int)($_POST['started_at'] ?? 0);
$elapsed = $startedAt > 0 ? (time() * 1000 - $startedAt) : 0;
if ($startedAt > 0 && $elapsed < 5000) {
  respond(false, 'Envío demasiado rápido. Intenta de nuevo.', 400, $isJson || $isAjax);
}

// Rate limit per IP (3 per 10 minutes)
$limitDir = __DIR__ . '/logs';
if (!is_dir($limitDir)) {
  @mkdir($limitDir, 0755, true);
}
$limitFile = $limitDir . '/rate_' . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $ip) . '.json';
$now = time();
$window = 600;
$maxRequests = 3;
$entries = [];

if (file_exists($limitFile)) {
  $content = @file_get_contents($limitFile);
  $entries = $content ? json_decode($content, true) : [];
}
if (!is_array($entries)) {
  $entries = [];
}
$entries = array_filter($entries, fn($ts) => ($now - (int)$ts) < $window);
if (count($entries) >= $maxRequests) {
  respond(false, 'Demasiadas solicitudes. Intenta más tarde.', 429, $isJson || $isAjax);
}
$entries[] = $now;
@file_put_contents($limitFile, json_encode(array_values($entries)));

$pareja = sanitize_text($_POST['pareja'] ?? '');
$email = sanitize_text($_POST['email'] ?? '');
$telefono = sanitize_text($_POST['telefono'] ?? '');
$fecha = sanitize_text($_POST['fecha_evento'] ?? '');
$mensaje = sanitize_text($_POST['mensaje'] ?? '');
$template = sanitize_text($_POST['template'] ?? '');

if ($pareja === '' || $email === '' || $telefono === '' || $template === '') {
  respond(false, 'Faltan campos obligatorios.', 400, $isJson || $isAjax);
}

if (!preg_match('/^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\'\’.,\-\s]{3,80}$/u', $pareja)) {
  respond(false, 'Nombre inválido.', 400, $isJson || $isAjax);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  respond(false, 'Email inválido.', 400, $isJson || $isAjax);
}

if (!preg_match('/^[0-9]{7,15}$/', $telefono)) {
  respond(false, 'Teléfono inválido.', 400, $isJson || $isAjax);
}

if ($fecha !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
  respond(false, 'Fecha inválida.', 400, $isJson || $isAjax);
}

if ($mensaje !== '') {
  if (contains_url($mensaje) || contains_blocked_words($mensaje, $blockedWords)) {
    respond(false, 'No se permiten enlaces ni contenido promocional.', 400, $isJson || $isAjax);
  }
}

if (contains_url($pareja) || contains_url($email) || contains_url($telefono)) {
  respond(false, 'Datos inválidos.', 400, $isJson || $isAjax);
}

// SMTP settings (fill with your real Hostinger SMTP data)
$smtpHost = 'smtp.hostinger.com';
$smtpPort = 465;
$smtpUser = 'joseantoniobejaranovela@outlook.es';
$smtpPass = 'PON_TU_PASSWORD_AQUI';
$smtpFrom = 'joseantoniobejaranovela@outlook.es';
$smtpTo = 'joseantoniobejaranovela@outlook.es';

$subject = 'Nuevo pedido de invitación';
$body = "Nuevo pedido:\n\n";
$body .= "Plantilla: {$template}\n";
$body .= "Pareja: {$pareja}\n";
$body .= "Email: {$email}\n";
$body .= "Teléfono: {$telefono}\n";
$body .= "Fecha del evento: " . ($fecha !== '' ? $fecha : 'No indicada') . "\n";
$body .= "Mensaje: " . ($mensaje !== '' ? $mensaje : 'Sin mensaje') . "\n";
$body .= "IP: {$ip}\n";

$sent = false;
$sendError = '';

// Use PHPMailer if available
$phpMailerPath = __DIR__ . '/PHPMailer/src/PHPMailer.php';
if (file_exists($phpMailerPath)) {
  require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
  require_once __DIR__ . '/PHPMailer/src/SMTP.php';
  require_once __DIR__ . '/PHPMailer/src/Exception.php';

  $mail = new PHPMailer\PHPMailer\PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = $smtpPort;

    $mail->setFrom($smtpFrom, 'Invitaciones Boda Sevilla');
    $mail->addAddress($smtpTo);
    $mail->addReplyTo($email, $pareja);

    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->send();
    $sent = true;
  } catch (Exception $e) {
    $sendError = $e->getMessage();
  }
} else {
  $sendError = 'PHPMailer no encontrado.';
}

if (!$sent) {
  respond(false, 'No se pudo enviar el correo. ' . $sendError, 500, $isJson || $isAjax);
}

respond(true, 'Solicitud enviada. Gracias, te contactaremos pronto.', 200, $isJson || $isAjax);
