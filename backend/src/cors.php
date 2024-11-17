<?php

class CorsMiddleware
{
    private array $options;

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

    private function isOriginAllowed(string $origin): bool
    {
        if (in_array('*', $this->options['allowedOrigins'])) {
            return true;
        }

        return in_array($origin, $this->options['allowedOrigins']);
    }

    private function addCorsHeaders(): void
    {
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
        header('Access-Control-Allow-Methods: '.implode(', ', $this->options['allowedMethods']));
        header('Access-Control-Allow-Headers: '.implode(', ', $this->options['allowedHeaders']));
        header('Access-Control-Max-Age: '.$this->options['maxAge']);
    }

    public function handle(): bool
    {
        try {
            // Always add basic CORS headers
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
            ]);

            return false;
        }
    }
}

// Example usage in index.php:
/*$corsOptions = [*/
/*    'allowedOrigins' => [*/
/*        'http://localhost:8080',*/
/*        'http://169.239.251.102:3341',*/
/*        'https://your-production-domain.com',*/
/*    ],*/
/*    'allowedMethods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],*/
/*    'allowedHeaders' => ['Content-Type', 'Authorization', 'X-Requested-With'],*/
/*    'allowCredentials' => true,*/
/*    'maxAge' => 3600,*/
/*];*/
/**/
/*$cors = new CorsMiddleware($corsOptions);*/
/**/
/*// Handle CORS first, before any other logic*/
/*if (! $cors->handle()) {*/
/*    exit();*/
/*}*/
