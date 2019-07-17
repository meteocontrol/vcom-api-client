<?php

namespace meteocontrol\client\vcomapi;

class Config {

    /** @var string */
    private static $tokenAccessDir;

    /** @var array */
    private $config = [];

    /** @var array */
    private $expectedKeys = [
        'API_URL',
        'API_KEY',
        'API_USERNAME',
        'API_PASSWORD',
        'API_AUTH_MODE'
    ];

    /** @var callable */
    private $tokenRefreshCallable;

    /** @var callable */
    private $tokenAccessCallable;

    /**
     * @param string $path
     */
    public function __construct($path = '') {
        self::$tokenAccessDir = __DIR__ . '/../.tokenAccess/';

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
    public function setApiUrl($url) {
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
    public function setApiKey($apiKey) {
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
    public function setApiUsername($username) {
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
    public function setApiPassword($password) {
        $this->config['API_PASSWORD'] = $password;
    }

    /**
     * @return string
     */
    public function getApiAuthorizationMode() {
        return $this->config['API_AUTH_MODE'];
    }

    /**
     * @param string $authorizationMode
     */
    public function setApiAuthorizationMode($authorizationMode) {
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
        $filename = $this->getTokenAccessFilename();
        if (file_exists(self::$tokenAccessDir . $filename)) {
            unlink(self::$tokenAccessDir . $filename);
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
    private function readConfigurationFile($path) {
        if (!file_exists($path)) {
            throw new \InvalidArgumentException("config file '$path' not found");
        }
        $this->config = parse_ini_file($path);

        $this->setTokenRefreshCallable(function ($credentials) {
            self::createTokenDir();
            $credentials = [
                'access_token' => $credentials['access_token'],
                'refresh_token' => $credentials['refresh_token'],
            ];
            file_put_contents(
                self::$tokenAccessDir . $this->getTokenAccessFilename(),
                base64_encode(json_encode($credentials))
            );
        });

        $this->setTokenAccessCallable(function () {
            $filename = $this->getTokenAccessFilename();
            if (!file_exists(self::$tokenAccessDir . $filename)) {
                return false;
            }
            return json_decode(base64_decode(
                file_get_contents(self::$tokenAccessDir . $filename)
            ), true);
        });

        $this->validate();
    }

    /**
     * @param string $key
     */
    private function checkForUnexpectedKeys($key) {
        if (!in_array($key, $this->expectedKeys, true)) {
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

    /**
     * @return string
     */
    private function getTokenAccessFilename() {
        return md5($this->getApiUsername() . $this->getApiPassword());
    }

    private static function createTokenDir() {
        !is_dir(self::$tokenAccessDir) &&
        !mkdir(self::$tokenAccessDir) &&
        !is_dir(self::$tokenAccessDir);
    }
}
