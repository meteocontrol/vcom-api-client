<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;

class System extends SubEndpoint {

    /**
     * @param EndpointInterface $parent
     */
    public function __construct(EndpointInterface $parent) {
        $this->uri = '';
        $this->api = $parent->getApiClient();
        $this->parent = $parent;
    }

    /**
     * @return \meteocontrol\client\vcomapi\model\SystemDetail
     */
    public function get() {
        $systemJson = $this->api->run(
            $this->getUri()
        );
        $decodedData = json_decode($systemJson, true);
        return \meteocontrol\client\vcomapi\model\SystemDetail::deserialize($decodedData['data']);
    }

    /**
     * @return Basics
     */
    public function basics() {
        return new Basics($this);
    }

    /**
     * @return Calculations
     */
    public function calculations() {
        return new Calculations($this);
    }

    /**
     * @return Inverters
     */
    public function inverters() {
        return new Inverters($this);
    }

    /**
     * @param string|array $deviceId
     * @return Inverter
     */
    public function inverter($deviceId) {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $inverters = new Inverters($this);
        $inverterIdEndpoint = new DeviceId($inverters, $deviceId);
        $inverterEndpoint = new \meteocontrol\client\vcomapi\endpoints\sub\systems\Inverter($inverterIdEndpoint);
        return $inverterEndpoint;
    }

    /**
     * @return Meters
     */
    public function meters() {
        return new Meters($this);
    }

    /**
     * @param string|array $deviceId
     * @return Meter
     */
    public function meter($deviceId) {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $meters = new Meters($this);
        $meterIdEndpoint = new DeviceId($meters, $deviceId);
        $meterEndpoint = new \meteocontrol\client\vcomapi\endpoints\sub\systems\Meter($meterIdEndpoint);
        return $meterEndpoint;
    }

    /**
     * @return Sensors
     */
    public function sensors() {
        return new Sensors($this);
    }

    /**
     * @param string|array $deviceId
     * @return Sensor
     */
    public function sensor($deviceId) {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $sensors = new Sensors($this);
        $sensorIdEndpoint = new DeviceId($sensors, $deviceId);
        $sensorEndpoint = new \meteocontrol\client\vcomapi\endpoints\sub\systems\Sensor($sensorIdEndpoint);
        return $sensorEndpoint;
    }

    /**
     * @return Batteries
     */
    public function batteries() {
        return new Batteries($this);
    }

    /**
     * @param string|array $deviceId
     * @return Battery
     */
    public function battery($deviceId) {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $batteries = new Batteries($this);
        $batteryIdEndpoint = new DeviceId($batteries, $deviceId);
        return new \meteocontrol\client\vcomapi\endpoints\sub\systems\Battery($batteryIdEndpoint);
    }

    /**
     * @return StringBoxes
     */
    public function stringboxes() {
        return new StringBoxes($this);
    }

    /**
     * @return Stringbox
     */
    public function stringbox() {
        return new StringBox($this);
    }

    /**
     * @return TechnicalData
     */
    public function technicalData() {
        return new TechnicalData($this);
    }

    /**
     * @return Bulk
     */
    public function bulk() {
        return new Bulk($this);
    }

    /**
     * @return Users
     */
    public function users() {
        return new Users($this);
    }

    /**
     * @return Responsibilities
     */
    public function responsibilities() {
        return new Responsibilities($this);
    }

    /**
     * @param string $userId
     * @return User
     */
    public function user($userId) {
        $users = new Users($this);
        $userIdEndpoint = new UserId($users, $userId);
        $userEndpoint = new User($userIdEndpoint);
        return $userEndpoint;
    }
}
