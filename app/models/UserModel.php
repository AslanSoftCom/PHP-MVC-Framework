<?php

class UserModel {
    
    public $db;
    public $device;

    public function test() {
        echo "IP Adresi: " . $this->device->ip();
    }
}