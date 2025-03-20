<?php
/**
 * 無料体験レッスン申込フォーム処理
 * Nexusソフトテニススクール
 */

// セッション開始
session_start();

// エラーメッセージ用の配列
$errors = [];

// フォームが送信された場合
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 入力データを取得して検証
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $class = filter_input(INPUT_POST, 'class', FILTER_SANITIZE_STRING);
    $experience = filter_input(INPUT_POST, 'experience', FILTER_SANITIZE_STRING);
    $preferred_date = filter_input(INPUT_POST, 'preferred_date', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);
    $privacy = isset($_POST['privacy']) ? true : false;
    
    // バリデーション
    if (empty($name)) {
        $errors[] = 'お名前を入力してください。';
    }
    
    if (empty($age) || $age < 6) {
        $errors[] = '有効な年齢を入力してください。';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = '有効なメールアドレスを入力してください。';
    }
    
    if (empty($phone)) {
        $errors[] = '電話番号を入力してください。';
    }
    
    if (empty($class)) {
        $errors[] = '希望クラスを選択してください。';
    }
    
    if (empty($preferred_date)) {
        $errors[] = '希望日を選択してください。';
    }
    
    if (!$privacy) {
        $errors[] = 'プライバシーポリシーに同意していただく必要があります。';
    }
    
    // クラスを日本語に変換
    $class_display = '';
    switch ($class) {
        case 'elementary':
            $class_display = '小学生クラス';
            break;
        case 'junior-high':
            $class_display = '中高生クラス';
            break;
        case 'adult':
            $class_display = '社会人クラス';
            break;
        default:
            $class_display = $class;
    }
    
    // 経験を日本語に変換
    $experience_display = '';
    switch ($experience) {
        case 'beginner':
            $experience_display = '初心者（未経験）';
            break;
        case 'intermediate':
            $experience_display = '1～3年';
            break;
        case 'advanced':
            $experience_display = '3年以上';
            break;
        default:
            $experience_display = $experience;
    }
    
    // エラーがない場合、メール送信
    if (empty($errors)) {
        // 管理者宛てのメール設定
        $to = "wassoi1357@gmail.com"; // 管理者のメールアドレス
        $admin_subject = "【無料体験申込】" . $name . " 様";
        
        // メールヘッダー
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . $name . " <" . $email . ">" . "\r\n";
        
        // 管理者宛メール本文
        $admin_message = "
        <html>
        <head>
            <title>無料体験レッスン申込</title>
        </head>
        <body>
            <h2>無料体験レッスン申込詳細</h2>
            <p><strong>名前:</strong> {$name}</p>
            <p><strong>年齢:</strong> {$age}</p>
            <p><strong>メールアドレス:</strong> {$email}</p>
            <p><strong>電話番号:</strong> {$phone}</p>
            <p><strong>希望クラス:</strong> {$class_display}</p>
            <p><strong>テニス経験:</strong> {$experience_display}</p>
            <p><strong>希望日:</strong> {$preferred_date}</p>
            <p><strong>メッセージ:</strong></p>
            <p>" . nl2br($message) . "</p>
        </body>
        </html>
        ";
        
        // 自動返信用の設定
        $auto_subject = "【Nexusソフトテニススクール】無料体験レッスン申込確認";
        $auto_headers = "MIME-Version: 1.0" . "\r\n";
        $auto_headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $auto_headers .= "From: Nexusソフトテニススクール <wassoi1357@gmail.com>" . "\r\n";
        
        // お客様宛て自動返信メール本文
        $auto_message = "
        <html>
        <head>
            <title>無料体験レッスン申込確認</title>
        </head>
        <body>
            <h2>{$name} 様</h2>
            <p>Nexusソフトテニススクールの無料体験レッスン申込をいただき、ありがとうございます。</p>
            <p>以下の内容で申込を受け付けました。</p>
            <hr>
            <p><strong>希望クラス:</strong> {$class_display}</p>
            <p><strong>テニス経験:</strong> {$experience_display}</p>
            <p><strong>希望日:</strong> {$preferred_date}</p>
            <hr>
            <p>担当者より改めて日程調整のご連絡をさせていただきます。</p>
            <p>なお、2営業日経っても連絡がない場合は、お手数ですが080-5292-1635までご連絡ください。</p>
            <p>どうぞよろしくお願いいたします。</p>
            <p>Nexusソフトテニススクール</p>
            <p>
                〒244-0801<br>
                神奈川県横浜市戸塚区品濃町1588-1<br>
                KPIPARKテニスコート<br>
                TEL: 080-5292-1635<br>
                Email: wassoi1357@gmail.com
            </p>
        </body>
        </html>
        ";
        
        // メール送信
        $admin_mail_sent = mail($to, $admin_subject, $admin_message, $headers);
        $auto_mail_sent = mail($email, $auto_subject, $auto_message, $auto_headers);
        
        if ($admin_mail_sent && $auto_mail_sent) {
            // 送信成功
            $_SESSION['trial_success'] = true;
            header("Location: thank-you.html");
            exit();
        } else {
            // 送信失敗
            $errors[] = 'メール送信に失敗しました。しばらく経ってから再度お試しください。';
        }
    }
    
    // エラーがあれば保存してリダイレクト
    if (!empty($errors)) {
        $_SESSION['trial_errors'] = $errors;
        $_SESSION['trial_form_data'] = [
            'name' => $name,
            'age' => $age,
            'email' => $email,
            'phone' => $phone,
            'class' => $class,
            'experience' => $experience,
            'preferred_date' => $preferred_date,
            'message' => $message
        ];
        header("Location: joining.html#trial");
        exit();
    }
} else {
    // GETリクエストの場合はトップページにリダイレクト
    header("Location: index.html");
    exit();
}
