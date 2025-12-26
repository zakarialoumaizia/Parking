<?php
class MailService {
    private $host = 'smtp.gmail.com';
    private $port = 587;
    private $username = 'laozakaria@gmail.com';
    private $password = 'uell rfcn ccsb uwrt';

    public function sendEmail($to, $subject, $body) {
        $socket = fsockopen($this->host, $this->port, $errno, $errstr, 30);
        if (!$socket) {
            return false;
        }

        $this->serverCmd($socket, "EHLO " . $this->host);
        $this->serverCmd($socket, "STARTTLS");
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        $this->serverCmd($socket, "EHLO " . $this->host);
        $this->serverCmd($socket, "AUTH LOGIN");
        $this->serverCmd($socket, base64_encode($this->username));
        $this->serverCmd($socket, base64_encode($this->password));
        $this->serverCmd($socket, "MAIL FROM: <" . $this->username . ">");
        $this->serverCmd($socket, "RCPT TO: <$to>");
        $this->serverCmd($socket, "DATA");

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: ParkingSmart <" . $this->username . ">\r\n";
        $headers .= "To: $to\r\n";
        $headers .= "Subject: $subject\r\n";

        fwrite($socket, "$headers\r\n$body\r\n.\r\n");
        $response = fgets($socket, 512);

        $this->serverCmd($socket, "QUIT");
        fclose($socket);

        return strpos($response, '250') !== false;
    }

    private function serverCmd($socket, $cmd) {
        fwrite($socket, $cmd . "\r\n");
        return fgets($socket, 512);
    }
}
