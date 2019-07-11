<?php

namespace meteocontrol\client\vcomapi;

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
    public function getApiUrl() {
        return $this->config['API_URL'];
    }

    /**
     * @param string $url
     */
    public function setApiUrl(string $url) {
        $this->config['API_URL'] = $url;
    }

    /**
     * @return string
     */
    public function getApiKey() {
        return $this->config['API_KEY'];
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey) {
        $this->config['API_KEY'] = $apiKey;
    }

    /**
     * @return string
     */
    public function getApiUsername() {
        return $this->config['API_USERNAME'];
    }

    /**
     * @param string $username
     */
    public function setApiUsername(string $username) {
        $this->config['API_USERNAME'] = $username;
    }

    /**
     * @return string
     */
    public function getApiPassword() {
        return $this->config['API_PASSWORD'];
    }

    /**
     * @param string $password
     */
    public function setApiPassword(string $password) {
        $this->config['API_PASSWORD'] = $password;
    }

    /**
     * @return string
     */
    public function getApiAuthorizationMode() {
        return $this->config['API_AUTH_MODE'] ?? self::DEFAULT_AUTH_MODE;
    }

    /**
     * @param string $authorizationMode
     */
    public function setApiAuthorizationMode(string $authorizationMode) {
        $this->config['API_AUTH_MODE'] = $authorizationMode;
    }

    /**
     * @return callable|null
     */
    public function getTokenRefreshCallable() {
        return $this->tokenRefreshCallable;
    }

    /**
     * @param callable $tokenRefreshCallable
     */
    public function setTokenRefreshCallable(callable $tokenRefreshCallable) {
        $this->tokenRefreshCallable = $tokenRefreshCallable;
    }

    /**
     * @return callable|null
     */
    public function getTokenAccessCallable() {
        return $this->tokenAccessCallable;
    }

    /**
     * @param callable $tokenAccessCallable
     */
    public function setTokenAccessCallable(callable $tokenAccessCallable) {
        $this->tokenAccessCallable = $tokenAccessCallable;
    }

    public function deleteTokenAccessFile() {
        $filename = md5($this->getApiUsername());
        if (file_exists(self::TOKEN_ACCESS_DIR . $filename)) {
            unlink(self::TOKEN_ACCESS_DIR . $filename);
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function validate() {
        foreach ($this->config as $key => $value) {
            $this->checkForUnexpectedKeys($key);
        }
        $this->checkForMissingKeys($this->config);
    }

    /**
     * @param string $path
     */
    private function readConfigurationFile(string $path) {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException("config file '$path' not found");
        }
        $this->config = parse_ini_file($path);

        $username = &$this->config['API_USERNAME'];
        $this->setTokenRefreshCallable(static function ($accessToken, $refreshToken) use (&$username) {
            self::createTokenDir();
            $credentials = [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
            ];
            file_put_contents(
                self::TOKEN_ACCESS_DIR . md5($username),
                base64_encode(json_encode($credentials))
            );
        });

        $this->setTokenAccessCallable(static function () use (&$username) {
            if (!file_exists(self::TOKEN_ACCESS_DIR . md5($username))) {
                return false;
            }
            return json_decode(base64_decode(
                file_get_contents(self::TOKEN_ACCESS_DIR . md5($username))
            ), true);
        });

        $this->validate();
    }

    /**
     * @param string $key
     */
    private function checkForUnexpectedKeys(string $key) {
        if (!in_array($key, $this->acceptableKeys, true)) {
            throw new \InvalidArgumentException(
                "wrong config file provided - unexpected key '$key' found"
            );
        }
    }

    /**
     * @param array $config
     */
    private function checkForMissingKeys(array $config) {
        foreach ($this->expectedKeys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new \InvalidArgumentException(
                    "wrong config file provided - expected key '$key' not found"
                );
            }
        }
    }

    private static function createTokenDir() {
        !is_dir(self::TOKEN_ACCESS_DIR) &&
        !mkdir(self::TOKEN_ACCESS_DIR) &&
        !is_dir(self::TOKEN_ACCESS_DIR);
    }
}
