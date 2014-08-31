<?php

/*
  Plugin Name: Room Availability
  Plugin URI:
  Description:
  Version: 1.0.0
  Author: Thorsten Gawantka
  Author URI:
  License: GPLv2
 */

/*
  Copyright (C) 2014 Thorsten Gawantka

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

class Roomavailability {
    const bootstrap_script = <<< 'EOT'
<script type="text/javascript">
    var availabilities = %s;
    window.onload = function() {
        render_roomavailability()
    };
</script>
EOT;
    const container = '<div id="roomavailability">%s</div>';
    const optionTemplate = '<option value="%s" %s>%s</option>';
    const yearsToRender = 2;
    const controlForm = <<< 'EOT'
<form>
  <fieldset>
    <label>%s</label>
    <select id="month">
      %s
    </select>
    <label>%s</label>
    <select id="year">
      %s
    </select>
  </fieldset>
</form>
EOT;

    private $thisMonth;
    private $thisYear;

    function __construct() {
        $this->thisMonth = date('n');
        $this->thisYear = date('Y');
    }

    function render_controls() {
        $monthNames = array(
            "Januar", 
            "Februar", 
            "M&auml;rz", 
            "April", 
            "Mai", 
            "Juni", 
            "Juli", 
            "August", 
            "September", 
            "Oktober", 
            "November", 
            "Dezember");
        $monthOptions = array();
        for($i = 0; $i < count($monthNames); $i++) {
            $atts = '';
            if($i + 1 === $this->thisMonth) {
                $atts = 'selected';
            }
            array_push($monthOptions, sprintf(self::optionTemplate, $i + 1, $atts, $monthNames[$i]));
        }

        $yearOptions = array();
        for($i = 0; $i < self::yearsToRender; $i++) {
            $year = $this->thisYear + $i;
            $atts = '';
            if($year == $this->thisYear) {
                $atts = 'selected';
            }
            array_push($yearOptions, sprintf(self::optionTemplate, $year, $atts, $year));
        }

        return sprintf(self::controlForm, 'Monat', implode($monthOptions), 'Jahr', implode($yearOptions));
    }

    /**
     * Query for all rooms. Rooms are usually pages with custom field
     * 'availability'.
     * 
     * @return array An array of room objects.
     */
    function query_rooms() {
        $query = array(
            'sort_order' => 'ASC',
            'sort_column' => 'post_title',
            'hierarchical' => 0,
            'meta_key' => 'availability',
            'post_type' => 'page',
            'post_status' => 'publish'
        );
        $pages = get_pages($query);
        return $pages;
    }

    function query_availabilities() {
        $rooms = $this->query_rooms();
        $availabilities = array();
        foreach ($rooms as $room) {
            $value = get_post_meta($room->ID, 'availability', true);
            $dates = explode("\r\n", $value);
            array_push($availabilities, array(
                'room_name' => $room->post_title,
                'availability' => $this->filter_availability_dates($dates)
            ));
        }
        return $availabilities;
    }

    function filter_availability_dates($dates) {
        $historic = array();
        $now = mktime(0, 0, 0);
        foreach($dates as $date) {
            $current = date_parse_from_format('j.m.Y', $date);
            $time = mktime(0, 0, 0, $current['month'], $current['day'], $current['year']);
            if($now - $time > 0) {
               array_push($historic, $date);
            }
        }
        return array_values(array_diff($dates, $historic));
    }
}

/**
 * Root function of room-availability plugin. This function replaces the
 * shortcode [roomavailability] or variations of
 * [roomavailability room="Room 1"] with an overview of all rooms or the
 * specified room.
 * 
 * @param type $atts The shortcodes provided attributes.
 * @return string The replacement of the shortcode.
 */
function render_roomavailability($atts) {
    $a = shortcode_atts(array(
        'room' => NULL
            ), $atts);

    $roomav = new Roomavailability();
    $availabilities = json_encode($roomav->query_availabilities());

    $room = $a['room'];
    if ($room === NULL) {
        // TODO Render overview
    } else {
        // TODO Render room
    }

    $script = sprintf(Roomavailability::bootstrap_script, $availabilities);
    $container = sprintf(Roomavailability::container, $roomav->render_controls());
    return $script . $container;
}

function register_scripts() {
    wp_enqueue_script('room-availability', plugins_url('js/room-availability.js', __FILE__), array('jquery'));
    wp_enqueue_style('room-availability', plugins_url('css/site.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'register_scripts');
add_shortcode('roomavailability', 'render_roomavailability');
