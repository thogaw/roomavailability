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

    $availabilities = json_encode(query_availabilities());
    $script = '<script type="text/javascript">' .
            'var availabilities = ' . $availabilities . ";\r\n" .
            'window.onload = function(){render_roomavailability()};' .
            '</script>';

    $room = $a['room'];
    if ($room === NULL) {
        // TODO Render overview
    } else {
        // TODO Render room
    }

    return $script . '<div id="roomavailability"></div>';
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
    $ids = array();
    foreach ($pages as $page) {
        array_push($ids, array('ID' => $page->ID,
            'post_title' => $page->post_title));
    }
    return $ids;
}

function filter_availability_dates($dates) {
    $historic = array();
    foreach($dates as $date) {
        $current = date_parse_from_format('j.m.Y', $date);
        if($current['year'] < date('Y')) {
            array_push($historic, $current);
        } else if($current['year'] == date('Y')) {
            if($current['month'] < date('m')) {
                array_push($historic, $current);
            } else if($current['month'] == date('m')) {
                if($current['day'] < date('j')) {
                    array_push($historic, $date);
                }
            }
        }
    }
    return array_values(array_diff($dates, $historic));
}

function query_availabilities() {
    $rooms = query_rooms();
    $availabilities = array();
    foreach ($rooms as $room) {
        $value = get_post_meta($room['ID'], 'availability', true);
        $dates = explode("\r\n", $value);
        array_push($availabilities, array(
            'room_name' => $room['post_title'],
            'availability' => filter_availability_dates($dates)
        ));
    }
    return $availabilities;
}

function register_scripts() {
    wp_enqueue_script('room-availability', plugins_url('js/room-availability.js', __FILE__), array('jquery'));
    wp_enqueue_style('room-availability', plugins_url('css/site.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'register_scripts');
add_shortcode('roomavailability', 'render_roomavailability');
