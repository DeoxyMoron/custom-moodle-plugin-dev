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
 * The main scratchencore configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
 *
 * @package    mod_scratchencore
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 *
 * @package    mod_scratchencore
 * @copyright  2016 Your Name <your@email.address>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_scratchencore_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('scratchencorename', 'scratchencore'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'scratchencorename', 'scratchencore');

        // Adding the standard "intro" and "introformat" fields.
        if ($CFG->branch >= 29) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor();
        }

        // Adding the rest of scratchencore settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        $mform->addElement('static', 'label1', 'scratchencoresetting1', 'Add your link to the scratch project below pls');

        //OLD CODE
        ////$mform->addElement('text', 'foobar', get_string('foobar', 'scratchencore'));
        ////$mform->setType('foobar', PARAM_NOTAGS);


        //DUPLICATION OF NAME BUT FOR FOOBAR
        $mform->addElement('text', 'foobar', get_string('foobar', 'scratchencore'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('foobar', PARAM_TEXT);
        } else {
            $mform->setType('foobar', PARAM_CLEANHTML);
        }
        $mform->addRule('foobar', null, 'required', null, 'client');
        $mform->addRule('foobar', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        //$mform->addHelpButton('foobar', 'scratchencorename', 'scratchencore');



        //$mform->addElement('url', 'externalurl', get_string('externalurl', 'url'), array('size'=>'60'), array('usefilepicker'=>true));
        //$mform->setType('externalurl', PARAM_RAW_TRIMMED);
        //$mform->addRule('externalurl', null, 'required', null, 'client');

        $mform->addElement('header', 'scratchencorefieldset', get_string('scratchencorefieldset', 'scratchencore'));
        $mform->addElement('static', 'label2', 'scratchencoresetting2', 'Your scratchencore fields go here. Replace me!!!!!');

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements, common to all modules.
        $this->standard_coursemodule_elements();

        // Add standard buttons, common to all modules.
        $this->add_action_buttons();
    }
}
