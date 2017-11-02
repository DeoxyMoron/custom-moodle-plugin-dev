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
 * Prints a particular instance of scratchencore
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_scratchencore
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace scratchencore with the name of your module and remove this line.

require_once('../../config.php');
require_once("$CFG->dirroot/mod/scratchencore/lib.php");
require_once("$CFG->dirroot/mod/scratchencore/tests/test.php");
//require_once("$CFG->dirroot/mod/scratchencore/locallib.php");

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... scratchencore instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('scratchencore', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $scratchencore  = $DB->get_record('scratchencore', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $scratchencore  = $DB->get_record('scratchencore', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $scratchencore->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('scratchencore', $scratchencore->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}



require_login($course, true, $cm);

$event = \mod_scratchencore\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $scratchencore);
$event->trigger();

// Print the page header.

$PAGE->set_url('/mod/scratchencore/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($scratchencore->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->requires->js;

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('scratchencore-'.$somevar);
 */


 //get our javascript all ready to go
 //We can omit $jsmodule, but its nice to have it here,
 //if for example we need to include some funky YUI stuff


$jsmodule = array(
	'name'     => 'mod_scratchencore',
	'fullpath' => '/mod/scratchencore/module.js',
	'requires' => array()
);
//here we set up any info we need to pass into javascript
$opts =Array();
$opts['someinstancesetting'] = "bee";


//this inits the M.mod_@@newmodule@@ thingy, after the page has loaded.
$PAGE->requires->js_init_call('M.mod_scratchencore.helper.init', array($opts),false,$jsmodule);
//$PAGE->requires->js_init_call('[YOUR FUNCTION NAME]', $YOURPARAMS);
//$PAGE->requires->js_init_call('[YOUR FUNCTION NAME]', $YOURPARAMS);



// Output starts here.
echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever.
if ($scratchencore->intro) {
    echo $OUTPUT->box(format_module_intro('scratchencore', $scratchencore, $cm->id), 'generalbox mod_introbox', 'scratchencoreintro');
}

// Print the given API URL
echo $OUTPUT->heading('Scratch API URL:');
echo $OUTPUT->box(format_string($scratchencore->projstart));


// functions specified in tests/Test.Php
$json_result = get_json_from_url($scratchencore->projstart);

// debugs
//$json_result = file_get_contents("project1.json");

//-------VERSION 2--------//
echo $OUTPUT->heading('Scratch JSON data:');
echo $OUTPUT->box(format_string($json_result));

$opts['json'] = $json_result;
$finalcount = $PAGE->requires->js_init_call('M.mod_scratchencore.helper.main', array($opts),false,$jsmodule);

echo $OUTPUT->heading('Number of Green Flag blocks:');
//echo $OUTPUT->box(format_string("hhhhh"));
echo $OUTPUT->box(format_string($finalcount));

echo '<div id="foobar">';
echo '</div>';


//-- Test --//
echo $OUTPUT->heading('Test:');
$json_result2 = get_json_from_url_alternate($scratchencore->projstart);
echo $OUTPUT->box(format_string($json_result2));


// -----------VERSION 1---------- //
// Print the retrieved JSON result
////echo $OUTPUT->heading('Scratch JSON data:');
////echo $OUTPUT->box(format_string($json_result));

// Retrieve and display the thumbnail
// Print the retrieved JSON result
////echo $OUTPUT->heading('Project Thumbnail:');
////echo $OUTPUT->box(get_scratch_thumbnail($json_result));

//Get scratch JSON from hardcoded URL and retrieve thumbnail
//$scrach_json_encoded = get_scratch_data();
//echo $OUTPUT->box($scrach_json_encoded);
//echo $OUTPUT->box(get_scratch_thumbnail($scrach_json_encoded));

//Test
//echo $OUTPUT->box(test_function_encore());

// Finish the page.

//Database operations

$project_data = new stdClass();
$project_data->JSON_obj = '';
$project_data->test = 'foobar';

echo $OUTPUT->box(format_string($project_data->test));




echo $OUTPUT->footer();
