<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RoomAvailability
 *
 * @author thorsten
 */
class RoomAvailability {

    private $roomName;
    private $availableDates;

    function __construct($roomName, mixed $date) {
        $this->roomName = $roomName;

        if (is_string($date)) {
            $datesStrings = explode("\r\n", $date);
            $availableDates = array();
            foreach ($datesStrings as $value) {
                $valueDate = date_parse_from_format('j.m.Y', $value);
                array_push($availableDates, $valueDate);
            }
        }
    }

    function getRoomName() {
        return $this->roomName;
    }

    function getAllAvailableDates() {
        return $this->availableDates;
    }

    function getFutureAvailableDates() {
        $datesInFuture = array();
        
        foreach ($this->availableDates as $value) {
            if($this->isFutureDate($value)) {
                array_push($datesInFuture, $value);
            }
        }
        
        return $datesInFuture;
    }
    
    function toArray() {
        $val = array(
            'room_name' => $this->getRoomName(),
            'availability' => $this->getFutureAvailableDates()
        );
        return $val;
    }
    
    private function isFutureDate($date) {
        $now = mktime(0, 0, 0);
        $time = mktime(0, 0, 0, $date['month'], $date['day'], $date['year']);
        return $time >= $now;
    }

}
