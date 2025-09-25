<?php
namespace App\Core;

class Mailer {
    public static function sendHtml(string $to, string $subject, string $html): bool {
        $config = require __DIR__ . '/../../config.php';
        $from = $config['app']['from_email'];
        $fromName = $config['app']['from_name'];
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=UTF-8";
        $headers[] = "From: {$fromName} <{$from}>";
        $headers[] = "Reply-To: {$from}";
        $headers[] = "X-Mailer: PHP/" . phpversion();
        $ok = mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $html, implode("\r\n", $headers));
        Database::call('sp_log_email', [$to, $subject, $html]);
        return $ok;
    }
}
