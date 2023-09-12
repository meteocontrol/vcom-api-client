<?php

namespace meteocontrol\client\vcomapi\endpoints\sub\systems;

use meteocontrol\client\vcomapi\endpoints\EndpointInterface;
use meteocontrol\client\vcomapi\endpoints\sub\SubEndpoint;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\EnvironmentalSavings;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\Forecasts;
use meteocontrol\client\vcomapi\endpoints\sub\systems\system\Satellite;
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
    public function get(): SystemDetail {
        $systemJson = $this->api->run(
            $this->getUri()
        );
        return SystemDetail::deserialize($this->jsonDecode($systemJson, true)['data']);
    }

    /**
     * @return Basics
     */
    public function basics(): Basics {
        return new Basics($this);
    }

    /**
     * @return Calculations
     */
    public function calculations(): Calculations {
        return new Calculations($this);
    }

    public function environmentalSavings(): EnvironmentalSavings {
        return new EnvironmentalSavings($this);
    }

    /**
     * @return Forecasts
     */
    public function forecasts(): Forecasts {
        return new Forecasts($this);
    }

    /**
     * @return Inverters
     */
    public function inverters(): Inverters {
        return new Inverters($this);
    }

    /**
     * @param string|array $deviceId
     * @return Inverter
     */
    public function inverter($deviceId): Inverter {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $inverters = new Inverters($this);
        $inverterIdEndpoint = new DeviceId($inverters, $deviceId);
        return new Inverter($inverterIdEndpoint);
    }

    /**
     * @return Meters
     */
    public function meters(): Meters {
        return new Meters($this);
    }

    /**
     * @param string|array $deviceId
     * @return Meter
     */
    public function meter($deviceId): Meter {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $meters = new Meters($this);
        $meterIdEndpoint = new DeviceId($meters, $deviceId);
        return new Meter($meterIdEndpoint);
    }

    /**
     * @return Sensors
     */
    public function sensors(): Sensors {
        return new Sensors($this);
    }

    /**
     * @param string|array $deviceId
     * @return Sensor
     */
    public function sensor($deviceId): Sensor {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $sensors = new Sensors($this);
        $sensorIdEndpoint = new DeviceId($sensors, $deviceId);
        return new Sensor($sensorIdEndpoint);
    }

    /**
     * @return Batteries
     */
    public function batteries(): Batteries {
        return new Batteries($this);
    }

    /**
     * @param string|array $deviceId
     * @return Battery
     */
    public function battery($deviceId): Battery {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $batteries = new Batteries($this);
        $batteryIdEndpoint = new DeviceId($batteries, $deviceId);
        return new Battery($batteryIdEndpoint);
    }

    /**
     * @return PowerPlantControllers
     */
    public function powerPlantControllers(): PowerPlantControllers {
        return new PowerPlantControllers($this);
    }

    /**
     * @param string|array $deviceId
     * @return PowerPlantController
     */
    public function powerPlantController($deviceId): PowerPlantController {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $powerPlantControllers = new PowerPlantControllers($this);
        $powerPlantControllerIdEndpoint = new DeviceId($powerPlantControllers, $deviceId);
        return new PowerPlantController($powerPlantControllerIdEndpoint);
    }

    /**
     * @return Stringboxes
     */
    public function stringboxes(): Stringboxes {
        return new Stringboxes($this);
    }

    /**
     * @param string|array $deviceId
     * @return StringBox
     */
    public function stringbox($deviceId): StringBox {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $stringboxes = new Stringboxes($this);
        $stringboxIdEndpoint = new DeviceId($stringboxes, $deviceId);
        return new Stringbox($stringboxIdEndpoint);
    }

    /**
     * @return Trackers
     */
    public function trackers(): Trackers {
        return new Trackers($this);
    }

    /**
     * @param string|array $deviceId
     * @return Tracker
     */
    public function tracker($deviceId): Tracker {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $trackers = new Trackers($this);
        $trackerIdEndpoint = new DeviceId($trackers, $deviceId);
        return new Tracker($trackerIdEndpoint);
    }

    /**
     * @return Statuses
     */
    public function statuses(): Statuses {
        return new Statuses($this);
    }

    /**
     * @param string|array $deviceId
     * @return Status
     */
    public function status($deviceId): Status {
        $deviceId = is_array($deviceId) ? implode(',', $deviceId) : $deviceId;
        $statuses = new Statuses($this);
        $statusIdEndpoint = new DeviceId($statuses, $deviceId);
        return new Status($statusIdEndpoint);
    }

    /**
     * @return TechnicalData
     */
    public function technicalData(): TechnicalData {
        return new TechnicalData($this);
    }

    /**
     * @return Bulk
     */
    public function bulk(): Bulk {
        return new Bulk($this);
    }

    /**
     * @return Users
     */
    public function users(): Users {
        return new Users($this);
    }

    /**
     * @return Responsibilities
     */
    public function responsibilities(): Responsibilities {
        return new Responsibilities($this);
    }

    /**
     * @param string $userId
     * @return User
     */
    public function user(string $userId): User {
        $users = new Users($this);
        $userIdEndpoint = new UserId($users, $userId);
        return new User($userIdEndpoint);
    }
    /**
     * @return Picture
     */
    public function picture(): Picture {
        return new Picture($this);
    }

    /**
     * @return VirtualMeters
     */
    public function virtualMeters(): VirtualMeters {
        return new VirtualMeters($this);
    }

    /**
     * @param string $virtualMeterId
     * @return VirtualMeter
     */
    public function virtualMeter(string $virtualMeterId): VirtualMeter {
        $virtualMeters = new VirtualMeters($this);
        $virtualMeterIdEndpoint = new DeviceId($virtualMeters, $virtualMeterId);
        return new VirtualMeter($virtualMeterIdEndpoint);
    }

    /**
     * @return Satellite
     */
    public function satellite(): Satellite {
        return new Satellite($this);
    }
}
