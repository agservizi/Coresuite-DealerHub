<?php
// helpers/rate_limit.php - naive rate limiting

declare(strict_types=1);

class RateLimit
{
    public static function check(string $key, int $maxRequests, int $windowSeconds): void
    {
        $bucketKey = 'rate_' . md5($key);
        $now = time();
        $_SESSION[$bucketKey] = $_SESSION[$bucketKey] ?? ['count' => 0, 'start' => $now];
        $bucket = &$_SESSION[$bucketKey];

        if (($now - $bucket['start']) > $windowSeconds) {
            $bucket = ['count' => 0, 'start' => $now];
        }

        if ($bucket['count'] >= $maxRequests) {
            http_response_code(429);
            exit('Troppe richieste. Riprova più tardi.');
        }

        $bucket['count']++;
    }
}

?>
