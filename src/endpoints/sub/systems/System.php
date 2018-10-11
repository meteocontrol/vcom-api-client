<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\vcomapi\model\SystemDetail;

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
     * @return SystemDetail
     */
    public function get() {
        $systemJson = $this->api->run(
            $this->getUri()
        );
        return SystemDetail::deserialize($this->jsonDecode($systemJson, true)['data']);
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
        $inverterEndpoint = new Inverter($inverterIdEndpoint);
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
        $meterEndpoint = new Meter($meterIdEndpoint);
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
        $sensorEndpoint = new Sensor($sensorIdEndpoint);
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
        return new Battery($batteryIdEndpoint);
    }

    /**
     * @return PowerPlantControllers
     */
    public function powerPlantControllers() {
        return new PowerPlantControllers($this);
    }

    /**
     * @param string|array $deviceId
     * @return PowerPlantController
     */
    public function powerPlantController($deviceId) {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $powerPlantControllers = new PowerPlantControllers($this);
        $powerPlantControllerIdEndpoint = new DeviceId($powerPlantControllers, $deviceId);
        return new PowerPlantController($powerPlantControllerIdEndpoint);
    }

    /**
     * @return Stringboxes
     */
    public function stringboxes() {
        return new Stringboxes($this);
    }

    /**
     * @param string|array $deviceId
     * @return StringBox
     */
    public function stringbox($deviceId) {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $stringboxes = new Stringboxes($this);
        $stringboxIdEndpoint = new DeviceId($stringboxes, $deviceId);
        return new Stringbox($stringboxIdEndpoint);
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
    /**
     * @return Picture
     */
    public function picture() {
        return new Picture($this);
    }
}
