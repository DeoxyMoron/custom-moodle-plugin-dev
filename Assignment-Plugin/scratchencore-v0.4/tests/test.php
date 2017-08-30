<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Internal library of functions for module scratchencore
 *
 * All the scratchencore specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod_scratchencore
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function test_function_encore(){
  //echo $OUTPUT->box('lolol');
  //echo $OUTPUT->footer();
  //die;
  $foo = 'foobar';
  return $foo;
}

function get_scratch_data(){
  $scratch_url = 'https://api.scratch.mit.edu/users/djsanosa/projects/168702903';


  //  Initiate curl
  $ch = curl_init();
  // Disable SSL verification
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  // Will return the response, if false it print the response
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  // Set the url
  curl_setopt($ch, CURLOPT_URL,$scratch_url);
  // Execute
  $scratch_result=curl_exec($ch);
  // Closing
  curl_close($ch);


  //echo $scratch_result;
  //echo 'wwwwwwwwww\r\n';
  //echo $result;

  //echo $scratch_json['title'];
  //echo $scratch_json['image'];


  //print_r($scratch_json);

  return $scratch_result;
  //foreach($scratch_json as $item){
//    echo $item;
//  }
}

function get_scratch_thumbnail($scratch_result){
  $scratch_json = json_decode($scratch_result, true);
  $imageurl = $scratch_json['image'];
  $image_html = "<img src='{$imageurl}'>";
  return $image_html;
}

/*
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 *function scratchencore_do_something_useful(array $things) {
 *    return new stdClass();
 *}
 */
