<?php

namespace meteocontrol\client\vcomapi;

use InvalidArgumentException;

class Config {

    private const DEFAULT_AUTH_MODE = 'oauth';

    private const TOKEN_ACCESS_DIR = __DIR__ . '/../.tokenAccess/';

    /** @var array */
    private $config = [];

    /** @var array */
    private $expectedKeys = [
        'API_URL',
        'API_KEY',
        'API_USERNAME',
        'API_PASSWORD',
    ];

    /** @var array */
    private $acceptableKeys = [
        'API_URL',
        'API_KEY',
        'API_USERNAME',
        'API_PASSWORD',
        'API_AUTH_MODE',
    ];

    /** @var callable */
    private $tokenRefreshCallable;

    /** @var callable */
    private $tokenAccessCallable;

    /**
     * @param string $path
     */
    public function __construct(string $path = '') {
        if (!$path) {
            $path = __DIR__ . '/../config.ini';
        }
        if (is_file($path)) {
            $this->readConfigurationFile($path);
        }
    }

    /**
     * @return string
     */
    public function getApiUrl(): string {
        return $this->config['API_URL'];
    }

    /**
     * @param string $url
     * @return void
     */
    public function setApiUrl(string $url): void {
        $this->config['API_URL'] = $url;
    }

    /**
     * @return string
     */
    public function getApiKey(): string {
        return $this->config['API_KEY'];
    }

    /**
     * @param string $apiKey
     * @return void
     */
    public function setApiKey(string $apiKey): void {
        $this->config['API_KEY'] = $apiKey;
    }

    /**
     * @return string
     */
    public function getApiUsername(): string {
        return $this->config['API_USERNAME'];
    }

    /**
     * @param string $username
     * @return void
     */
    public function setApiUsername(string $username): void {
        $this->config['API_USERNAME'] = $username;
    }

    /**
     * @return string
     */
    public function getApiPassword(): string {
        return $this->config['API_PASSWORD'];
    }

    /**
     * @param string $password
     * @return void
     */
    public function setApiPassword(string $password): void {
        $this->config['API_PASSWORD'] = $password;
    }

    /**
     * @return string
     */
    public function getApiAuthorizationMode(): string {
        return $this->config['API_AUTH_MODE'] ?? self::DEFAULT_AUTH_MODE;
    }

    /**
     * @param string $authorizationMode
     * @return void
     */
    public function setApiAuthorizationMode(string $authorizationMode): void {
        $this->config['API_AUTH_MODE'] = $authorizationMode;
    }

    /**
     * @return callable|null
     */
    public function getTokenRefreshCallable(): ?callable {
        return $this->tokenRefreshCallable;
    }

    /**
     * @param callable $tokenRefreshCallable
     * @return void
     */
    public function setTokenRefreshCallable(callable $tokenRefreshCallable): void {
        $this->tokenRefreshCallable = $tokenRefreshCallable;
    }

    /**
     * @return callable|null
     */
    public function getTokenAccessCallable(): ?callable {
        return $this->tokenAccessCallable;
    }

    /**
     * @param callable $tokenAccessCallable
     * @return void
     */
    public function setTokenAccessCallable(callable $tokenAccessCallable): void {
        $this->tokenAccessCallable = $tokenAccessCallable;
    }

    /**
     * @return void
     */
    public function deleteTokenAccessFile(): void {
        $filename = $this->getTokenAccessFilename();
        if (file_exists(self::TOKEN_ACCESS_DIR . $filename)) {
            unlink(self::TOKEN_ACCESS_DIR . $filename);
        }
    }

    /**
     * @return void
     */
    public function validate(): void {
        foreach ($this->config as $key => $value) {
            $this->checkForUnexpectedKeys($key);
        }
        $this->checkForMissingKeys($this->config);
    }

    /**
     * @param string $path
     * @return void
     * @throws InvalidArgumentException
     */
    private function readConfigurationFile(string $path): void {
        if (!file_exists($path)) {
            throw new InvalidArgumentException("config file '$path' not found");
        }
        $this->config = parse_ini_file($path);

        $this->setTokenRefreshCallable(function ($credentials) {
            self::createTokenDir();
            $credentials = [
                'access_token' => $credentials['access_token'],
                'refresh_token' => $credentials['refresh_token'],
            ];
            file_put_contents(
                self::TOKEN_ACCESS_DIR . $this->getTokenAccessFilename(),
                base64_encode(json_encode($credentials))
            );
        });

        $this->setTokenAccessCallable(function () {
            $filename = $this->getTokenAccessFilename();
            if (!file_exists(self::TOKEN_ACCESS_DIR . $filename)) {
                return false;
            }
            return json_decode(base64_decode(
                file_get_contents(self::TOKEN_ACCESS_DIR . $filename)
            ), true);
        });

        $this->validate();
    }

    /**
     * @param string $key
     * @return void
     * @throws InvalidArgumentException
     */
    private function checkForUnexpectedKeys(string $key): void {
        if (!in_array($key, $this->acceptableKeys, true)) {
            throw new InvalidArgumentException(
                "wrong config file provided - unexpected key '$key' found"
            );
        }
    }

    /**
     * @param array $config
     * @return void
     * @throws InvalidArgumentException
     */
    private function checkForMissingKeys(array $config): void {
        foreach ($this->expectedKeys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new InvalidArgumentException(
                    "wrong config file provided - expected key '$key' not found"
                );
            }
        }
    }

    /**
     * @return string
     */
    private function getTokenAccessFilename(): string {
        return md5($this->getApiUsername() . $this->getApiPassword());
    }

    /**
     * @return void
     */
    private static function createTokenDir(): void {
        !is_dir(self::TOKEN_ACCESS_DIR) &&
        !mkdir(self::TOKEN_ACCESS_DIR) &&
        !is_dir(self::TOKEN_ACCESS_DIR);
    }
}
