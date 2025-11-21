<?php
/**
 * Simple .env file loader
 * Alternative to vlucas/phpdotenv for systems without Composer
 */

class EnvLoader {
    private static $loaded = false;
    private static $vars = [];
    
    public static function load($path) {
        if (self::$loaded) {
            return;
        }
        
        $envFile = $path . '/.env';
        
        if (!file_exists($envFile)) {
            throw new Exception(".env file not found at: " . $envFile);
        }
        
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse KEY=VALUE
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                $value = trim($value, '"\'');
                
                // Set in $_ENV and $_SERVER
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
                self::$vars[$key] = $value;
                
                // Also set using putenv for compatibility
                putenv("$key=$value");
            }
        }
        
        self::$loaded = true;
    }
    
    public static function get($key, $default = null) {
        return $_ENV[$key] ?? $default;
    }
}
