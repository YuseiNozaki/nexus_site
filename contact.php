<?php
/**
 * お問い合わせフォーム処理
 * Nexusソフトテニススクール
 */

// セッション開始
session_start();

// エラーメッセージ用の配列
$errors = [];

// フォームが送信された場合
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 入力データを取得して検証
    $name = filter_input(INPUT_POST, 'contact_name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'contact_email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'contact_phone', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'contact_type', FILTER_SANITIZE_STRING);
    $subject = filter_input(INPUT_POST, 'contact_subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'contact_message', FILTER_SANITIZE_STRING);
    $privacy = isset($_POST['contact_privacy']) ? true : false;
    
    // バリデーション
    if (empty($name)) {
        $errors[] = 'お名前を入力してください。';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '有効なメールアドレスを入力してください。';
    }
    
    if (empty($type)) {
        $errors[] = 'お問い合わせ種別を選択してください。';
    }
    
    if (empty($subject)) {
        $errors[] = '件名を入力してください。';
    }
    
    if (empty($message)) {
        $errors[] = 'お問い合わせ内容を入力してください。';
    }
    
    if (!$privacy) {
        $errors[] = 'プライバシーポリシーに同意していただく必要があります。';
    }
    
    // エラーがない場合、メール送信
    if (empty($errors)) {
        // 管理者宛てのメール設定
        $to = "info@nexus-tennis.com"; // 管理者のメールアドレス
        $admin_subject = "【お問い合わせ】" . $subject;
        
        // メールヘッダー
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . $name . " <" . $email . ">" . "\r\n";
        
        // 管理者宛メール本文
        $admin_message = "
        <html>
        <head>
            <title>Nexusソフトテニススクールへのお問い合わせ</title>
        </head>
        <body>
            <h2>お問い合わせ内容</h2>
            <p><strong>お名前:</strong> {$name}</p>
            <p><strong>メールアドレス:</strong> {$email}</p>
            <p><strong>電話番号:</strong> " . ($phone ? $phone : '記入なし') . "</p>
            <p><strong>お問い合わせ種別:</strong> {$type}</p>
            <p><strong>件名:</strong> {$subject}</p>
            <p><strong>内容:</strong></p>
            <p>" . nl2br($message) . "</p>
        </body>
        </html>
        ";
        
        // 自動返信用の設定
        $auto_subject = "【自動返信】お問い合わせありがとうございます - Nexusソフトテニススクール";
        $auto_headers = "MIME-Version: 1.0" . "\r\n";
        $auto_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $auto_headers .= "From: Nexusソフトテニススクール <info@nexus-tennis.com>" . "\r\n";
        
        // お客様宛て自動返信メール本文
        $auto_message = "
        <html>
        <head>
            <title>お問い合わせありがとうございます</title>
        </head>
        <body>
            <h2>{$name} 様</h2>
            <p>Nexusソフトテニススクールへのお問い合わせありがとうございます。</p>
            <p>以下の内容でお問い合わせを受け付けました。</p>
            <hr>
            <p><strong>お問い合わせ種別:</strong> {$type}</p>
            <p><strong>件名:</strong> {$subject}</p>
            <p><strong>内容:</strong></p>
            <p>" . nl2br($message) . "</p>
            <hr>
            <p>担当者より改めてご連絡いたします。</p>
            <p>なお、2営業日経っても返信がない場合は、お手数ですが045-XXX-XXXXまでご連絡ください。</p>
            <p>どうぞよろしくお願いいたします。</p>
            <p>Nexusソフトテニススクール</p>
            <p>
                〒244-0801<br>
                神奈川県横浜市戸塚区品濃町1588-1<br>
                KPIPARKテニスコート<br>
                TEL: 045-XXX-XXXX<br>
                Email: info@nexus-tennis.com
            </p>
        </body>
        </html>
        ";
        
        // メール送信
        $admin_mail_sent = mail($to, $admin_subject, $admin_message, $headers);
        $auto_mail_sent = mail($email, $auto_subject, $auto_message, $auto_headers);
        
        if ($admin_mail_sent && $auto_mail_sent) {
            // 送信成功
            $_SESSION['contact_success'] = true;
            header("Location: thank-you.html");
            exit();
        } else {
            // 送信失敗
            $errors[] = 'メール送信に失敗しました。しばらく経ってから再度お試しください。';
        }
    }
    
    // エラーがあれば保存してリダイレクト
    if (!empty($errors)) {
        $_SESSION['contact_errors'] = $errors;
        $_SESSION['contact_form_data'] = [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'type' => $type,
            'subject' => $subject,
            'message' => $message
        ];
        header("Location: contact.html");
        exit();
    }
} else {
    // GETリクエストの場合はトップページにリダイレクト
    header("Location: index.html");
    exit();
}
