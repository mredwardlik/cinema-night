<?php
/**
 * Turns PHP errors and exceptions into a readable page (debug mode)
 * or a short message + a line in storage/logs/error.log (production).
 */
namespace lib;

class ErrorHandle
{
    // Third-party code bundled with the project: its deprecation notices are not our errors
    private static $vendorPaths = ['/web/rb/', '/template/lib/'];

    static public function getErrorName($error)
    {
        $errors = [
            E_ERROR             => 'ERROR',
            E_WARNING           => 'WARNING',
            E_PARSE             => 'PARSE',
            E_NOTICE            => 'NOTICE',
            E_CORE_ERROR        => 'CORE_ERROR',
            E_CORE_WARNING      => 'CORE_WARNING',
            E_COMPILE_ERROR     => 'COMPILE_ERROR',
            E_COMPILE_WARNING   => 'COMPILE_WARNING',
            E_USER_ERROR        => 'USER_ERROR',
            E_USER_WARNING      => 'USER_WARNING',
            E_USER_NOTICE       => 'USER_NOTICE',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED        => 'DEPRECATED',
            E_USER_DEPRECATED   => 'USER_DEPRECATED',
        ];

        if (array_key_exists($error, $errors))
        {
            return $errors[$error] . " [$error]";
        }

        return $error;
    }

    public function register()
    {
        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
        register_shutdown_function([$this, 'fatalErrorHandler']);
    }

    public function errorHandler($errno, $errstr, $file, $line)
    {
        if (!(error_reporting() & $errno))
        {
            return true;
        }

        if (in_array($errno, [E_DEPRECATED, E_USER_DEPRECATED], true) && $this->isVendor($file))
        {
            return true;
        }

        $debug = class_exists('Config', false) ? \Config::getapp('debug') : true;
        $isWarning = in_array($errno, [E_WARNING, E_NOTICE, E_USER_WARNING, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED], true);

        // In production minor problems are only logged, so a page still opens
        if (!$debug && $isWarning)
        {
            $this->log(self::getErrorName($errno) . ": $errstr in $file:$line");
            return true;
        }

        throw new \ErrorException($errstr, 0, $errno, $file, $line);
    }

    public function exceptionHandler($e)
    {
        $type = $e instanceof \ErrorException ? self::getErrorName($e->getSeverity()) : get_class($e);
        $this->showError($type, $e->getMessage(), $e->getFile(), $e->getLine(), 500);
    }

    public function fatalErrorHandler()
    {
        $error = error_get_last();

        if ($error && ($error['type'] & (E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR)))
        {
            $this->showError(self::getErrorName($error['type']), $error['message'], $error['file'], $error['line'], 500);
        }
    }

    public function showError($type, $message, $file, $line, $status = 500)
    {
        $this->log("$type: $message in $file:$line");

        if (!headers_sent())
        {
            http_response_code($status);
        }

        $debug = class_exists('Config', false) ? \Config::getapp('debug') : true;
        $isAjax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

        if ($isAjax)
        {
            if (!headers_sent())
            {
                header('Content-Type: application/json; charset=UTF-8');
            }
            echo json_encode(['error' => $debug ? "$message ($file:$line)" : 'Ошибка сервера. Попробуйте позже.'], JSON_UNESCAPED_UNICODE);
            return;
        }

        if ($debug)
        {
            echo '<pre style="margin:20px;padding:16px;border-left:4px solid #c0392b;background:#fdf2f2;font:14px/1.5 monospace;white-space:pre-wrap">'
                . '<b>' . htmlspecialchars($type) . '</b>' . "\n"
                . htmlspecialchars($message) . "\n\n"
                . htmlspecialchars($file) . ':' . (int) $line
                . '</pre>';
        }
        else
        {
            echo '<h1 style="font-family:sans-serif;text-align:center;margin-top:20vh">Что-то пошло не так. Попробуйте позже.</h1>';
        }
    }

    private function isVendor($file)
    {
        $file = str_replace('\\', '/', $file);

        foreach (self::$vendorPaths as $path)
        {
            if (strpos($file, $path) !== false)
            {
                return true;
            }
        }

        return false;
    }

    private function log($line)
    {
        if (!defined('ROOT'))
        {
            return;
        }

        $directory = ROOT . '/storage/logs';

        if (!is_dir($directory))
        {
            @mkdir($directory, 0775, true);
        }

        @file_put_contents($directory . '/error.log', date('[Y-m-d H:i:s] ') . $line . PHP_EOL, FILE_APPEND);
    }

}
