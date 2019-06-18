<?php

namespace meteocontrol\client\vcomapi;

class Config {

    private const DEFAULT_AUTH_MODE = 'oauth';

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

    /**
     * @param string $path
     */
    public function __construct($path = '') {
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
        return $this->config['API_AUTH_MODE'] ?? self::DEFAULT_AUTH_MODE;
    }

    /**
     * @param string $authorizationMode
     */
    public function setApiAuthorizationMode($authorizationMode) {
        $this->config['API_AUTH_MODE'] = $authorizationMode;
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
        $this->validate();
    }

    /**
     * @param string $key
     */
    private function checkForUnexpectedKeys($key) {
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
}
