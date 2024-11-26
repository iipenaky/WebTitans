<?php

interface Middleware
{
    /**
     * @return void
     */
    public function handle();
}

class CorsMiddleware implements Middleware
{
    private array $options;

    /**
     * @param  array<int,mixed>  $options
     */
    public function __construct(array $options = [])
    {
        $this->options = array_merge([
            'allowedOrigins' => ['*'],
            'allowedMethods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'allowedHeaders' => ['Content-Type', 'Authorization', 'X-Requested-With'],
            'exposedHeaders' => [],
            'maxAge' => 3600,
            'allowCredentials' => false,
        ], $options);
    }

    private function getOrigin(): ?string
    {
        return $_SERVER['HTTP_ORIGIN'] ?? null;
    }

    private function isSameOrigin(): bool
    {
        $origin = $this->getOrigin();
        if (! $origin) {
            return true; // No origin header typically means same origin
        }

        $currentOrigin = sprintf(
            '%s://%s%s',
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http',
            $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'],
            isset($_SERVER['SERVER_PORT']) && ! in_array($_SERVER['SERVER_PORT'], [80, 443])
                ? ':'.$_SERVER['SERVER_PORT']
                : ''
        );

        return $origin === $currentOrigin;
    }

    private function isOriginAllowed(string $origin): bool
    {
        if ($this->isSameOrigin()) {
            return true;
        }

        if (in_array('*', $this->options['allowedOrigins'])) {
            return true;
        }

        return in_array($origin, $this->options['allowedOrigins']);
    }

    private function addCorsHeaders(): void
    {
        // If it's a same-origin request, we don't need to add CORS headers
        if ($this->isSameOrigin()) {
            return;
        }

        $origin = $this->getOrigin();

        if ($origin && $this->isOriginAllowed($origin)) {
            header("Access-Control-Allow-Origin: $origin");

            if ($this->options['allowCredentials']) {
                header('Access-Control-Allow-Credentials: true');
            }

            if (! empty($this->options['exposedHeaders'])) {
                header('Access-Control-Expose-Headers: '.implode(', ', $this->options['exposedHeaders']));
            }
        }
    }

    private function addPreflightHeaders(): void
    {
        if ($this->isSameOrigin()) {
            return;
        }

        header('Access-Control-Allow-Methods: '.implode(', ', $this->options['allowedMethods']));
        header('Access-Control-Allow-Headers: '.implode(', ', $this->options['allowedHeaders']));
        header('Access-Control-Max-Age: '.$this->options['maxAge']);
    }

    public function handle(): bool
    {
        try {

            // If it's same origin, just continue without CORS headers
            if ($this->isSameOrigin()) {
                return true;
            }

            // Add CORS headers for cross-origin requests
            $this->addCorsHeaders();

            // Handle preflight requests
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                $this->addPreflightHeaders();
                header('HTTP/1.1 204 No Content');
                exit();
            }

            return true;

        } catch (Exception $e) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode([
                'error' => 'CORS Error',
                'message' => $e->getMessage(),
                'isSameOrigin' => $this->isSameOrigin(),
                'origin' => $this->getOrigin(),
            ]);

            return false;
        }
    }
}

class JsonMiddleware implements Middleware
{
    public function handle(): bool
    {
        header('Content-Type: application/json; charset=UTF-8');

        return true;
    }
}

class MiddlewareHandler
{
    private array $middlewares = [];

    public function add(Middleware $middleware): MiddlewareHandler
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    public function handle(): bool
    {
        foreach ($this->middlewares as $middleware) {
            if (! $middleware->handle()) {
                return false;
            }
        }

        return true;
    }
}
