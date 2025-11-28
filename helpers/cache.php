<?php
// helpers/cache.php - simple file-based cache for lightweight data

declare(strict_types=1);

class Cache
{
    private static string $cacheDir = __DIR__ . '/../storage/cache';

    public static function remember(string $key, int $ttl, callable $callback)
    {
        $path = self::pathFor($key);
        if (file_exists($path) && (time() - filemtime($path)) < $ttl) {
            return unserialize((string) file_get_contents($path));
        }
        $value = $callback();
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }
        file_put_contents($path, serialize($value));
        return $value;
    }

    public static function forget(string $key): void
    {
        $path = self::pathFor($key);
        if (file_exists($path)) {
            unlink($path);
        }
    }

    private static function pathFor(string $key): string
    {
        return self::$cacheDir . '/' . md5($key) . '.cache';
    }
}

?>
