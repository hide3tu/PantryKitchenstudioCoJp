<?php
/**
 * お問い合わせフォーム送信処理
 * reCAPTCHA v3 検証付き
 */

// ============================================
// ★ 設定（ここを変更してください）
// ============================================

// 送信先メールアドレス（TO）
$toEmail = 'ikeda@kitchenstudio.co.jp';

// CC（複数の場合はカンマ区切り）
$ccEmail = 'kadota@kitchenstudio.co.jp, aisoramanta@kitchenstudio.co.jp';

// 送信元メールアドレス（サーバーのドメインに合わせる）
$fromEmail = 'readonly@kitchenstudio.co.jp';

// reCAPTCHA v3 シークレットキー
$recaptchaSecret = '6LdK5LoqAAAAALY99Cn7x0H9ayIw_qitzIo_Ud_5';

// ============================================

// CORS設定（同一オリジンのみ許可）
header('Content-Type: application/json; charset=utf-8');

// POST以外は拒否
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method Not Allowed']);
    exit;
}

// フォームデータ取得
$company = isset($_POST['company']) ? trim($_POST['company']) : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$recaptchaToken = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

// 必須項目チェック
if (empty($name) || empty($email) || empty($type) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => '必須項目を入力してください']);
    exit;
}

// メールアドレス形式チェック
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'メールアドレスの形式が正しくありません']);
    exit;
}

// reCAPTCHA検証
if (empty($recaptchaToken)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'reCAPTCHAを確認してください']);
    exit;
}

$recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
$recaptchaData = [
    'secret' => $recaptchaSecret,
    'response' => $recaptchaToken,
    'remoteip' => $_SERVER['REMOTE_ADDR']
];

$options = [
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/x-www-form-urlencoded',
        'content' => http_build_query($recaptchaData)
    ]
];

$context = stream_context_create($options);
$recaptchaResult = file_get_contents($recaptchaUrl, false, $context);
$recaptchaResponse = json_decode($recaptchaResult, true);

if (!$recaptchaResponse['success']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'reCAPTCHA認証に失敗しました']);
    exit;
}

// v3 スコア検証（0.5以上を許可）
$score = isset($recaptchaResponse['score']) ? $recaptchaResponse['score'] : 0;
if ($score < 0.5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'スパム判定されました。時間をおいて再度お試しください']);
    exit;
}

// お問い合わせ種別の変換
$typeLabels = [
    'scenario' => 'シナリオ制作のご依頼',
    'novel' => 'ノベライズのご依頼',
    'consulting' => '企画・コンサルティング',
    'other' => 'その他のお問い合わせ'
];
$typeLabel = isset($typeLabels[$type]) ? $typeLabels[$type] : $type;

// メール本文作成
$mailBody = <<<EOT
テキストパントリー ウェブサイトよりお問い合わせがありました。

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
■ お問い合わせ内容
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

【会社名・団体名】
{$company}

【お名前】
{$name}

【メールアドレス】
{$email}

【電話番号】
{$phone}

【お問い合わせ種別】
{$typeLabel}

【お問い合わせ内容】
{$message}

━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
送信日時:
EOT;
$mailBody .= date('Y年m月d日 H:i:s');

// 文字エンコーディング設定
mb_language('Japanese');
mb_internal_encoding('UTF-8');

// Message-ID生成（一意のID）
$messageId = sprintf(
    '<%s.%s@%s>',
    date('YmdHis'),
    uniqid(),
    'kitchenstudio.co.jp'
);

// メールヘッダー（Gmail対応）
$headers = [];
$headers[] = 'MIME-Version: 1.0';
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'Content-Transfer-Encoding: base64';
$headers[] = 'From: テキストパントリー <' . $fromEmail . '>';
$headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
$headers[] = 'Return-Path: <' . $fromEmail . '>';
$headers[] = 'Message-ID: ' . $messageId;
$headers[] = 'Date: ' . date('r');
$headers[] = 'X-Mailer: Text-Pantry-Contact-Form';
$headers[] = 'X-Priority: 3';

// CCが設定されている場合は追加
if (!empty($ccEmail)) {
    $headers[] = 'Cc: ' . $ccEmail;
}

$headerString = implode("\r\n", $headers);

// 件名
$subject = '【テキストパントリー】' . $typeLabel;
$subject = mb_encode_mimeheader($subject, 'UTF-8', 'B');

// 本文をBase64エンコード
$mailBodyEncoded = base64_encode($mailBody);

// メール送信（第5引数でEnvelope-Fromを指定）
$mailResult = mail($toEmail, $subject, $mailBodyEncoded, $headerString, '-f ' . $fromEmail);

if ($mailResult) {
    echo json_encode(['success' => true, 'message' => 'お問い合わせを送信しました']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '送信に失敗しました。時間をおいて再度お試しください']);
}
