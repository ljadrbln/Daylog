<?php
namespace Daylog\Infrastructure\Utils;

class DSNParser {
    /**
     * Parse a DSN string like:
     * mysql://user:pass@host:port/dbname
     *
     * @param string $url
     * @return array{0: string, 1: string, 2: string} dsn, user, pass
     */
    public static function parse(string $url): array {
        $parts = parse_url($url);

        if (
            !isset($parts['scheme']) ||
            !isset($parts['host']) ||
            !isset($parts['path']) ||
            !isset($parts['user']) ||
            !isset($parts['pass'])
        ) {
            throw new \InvalidArgumentException('Invalid DSN format');
        }

        $driver = $parts['scheme'];
        $host   = $parts['host'];
        $port   = $parts['port'] ?? 3306;
        $dbname = ltrim($parts['path'], '/');
        $user   = $parts['user'];
        $pass   = $parts['pass'];

        $dsn = sprintf('%s:host=%s;port=%d;dbname=%s', $driver, $host, $port, $dbname);

        return [$dsn, $user, $pass];
    }
}