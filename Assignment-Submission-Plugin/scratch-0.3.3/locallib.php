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
 * This file contains the definition for the library class for scratch submission plugin
 *
 * This class provides all the functionality for the new assign module.
 *
 * @package assignsubmission_scratch
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// File area for Scratch submission assignment.
define('ASSIGNSUBMISSION_scratch_FILEAREA', 'submissions_scratch');
//require_once("$CFG->dirroot/submission/scratch/tests/test.php");
/**
 * library class for scratch submission plugin extending submission plugin base class
 *
 * @package assignsubmission_scratch
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_scratch extends assign_submission_plugin {

    /**
     * Get the name of the Scratch submission plugin
     * @return string
     */
    public function get_name() {
        return get_string('scratch', 'assignsubmission_scratch');
    }


    /**
     * Get scratch submission information from the database
     *
     * @param  int $submissionid
     * @return mixed
     */
    private function get_scratch_submission($submissionid) {
        global $DB;

        return $DB->get_record('assignsubmission_scratch', array('submission'=>$submissionid));
    }

    /**
     * Get the settings for scratch submission plugin
     *
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     */
    public function get_settings(MoodleQuickForm $mform) {
        global $CFG, $COURSE;

        $defaultwordlimit = $this->get_config('wordlimit') == 0 ? '' : $this->get_config('wordlimit');
        $defaultwordlimitenabled = $this->get_config('wordlimitenabled');

        $options = array('size' => '6', 'maxlength' => '6');
        $name = get_string('wordlimit', 'assignsubmission_scratch');

        // Create a text box that can be enabled/disabled for scratch word limit.
        $wordlimitgrp = array();
        $wordlimitgrp[] = $mform->createElement('text', 'assignsubmission_scratch_wordlimit', '', $options);
        $wordlimitgrp[] = $mform->createElement('checkbox', 'assignsubmission_scratch_wordlimit_enabled',
                '', get_string('enable'));
        $mform->addGroup($wordlimitgrp, 'assignsubmission_scratch_wordlimit_group', $name, ' ', false);
        $mform->addHelpButton('assignsubmission_scratch_wordlimit_group',
                              'wordlimit',
                              'assignsubmission_scratch');
        $mform->disabledIf('assignsubmission_scratch_wordlimit',
                           'assignsubmission_scratch_wordlimit_enabled',
                           'notchecked');

        // Add numeric rule to text field.
        $wordlimitgrprules = array();
        $wordlimitgrprules['assignsubmission_scratch_wordlimit'][] = array(null, 'numeric', null, 'client');
        $mform->addGroupRule('assignsubmission_scratch_wordlimit_group', $wordlimitgrprules);

        // Rest of group setup.
        $mform->setDefault('assignsubmission_scratch_wordlimit', $defaultwordlimit);
        $mform->setDefault('assignsubmission_scratch_wordlimit_enabled', $defaultwordlimitenabled);
        $mform->setType('assignsubmission_scratch_wordlimit', PARAM_INT);
        $mform->disabledIf('assignsubmission_scratch_wordlimit_group',
                           'assignsubmission_scratch_enabled',
                           'notchecked');
    }

    /**
     * Save the settings for scratch submission plugin
     *
     * @param stdClass $data
     * @return bool
     */
    public function save_settings(stdClass $data) {
        if (empty($data->assignsubmission_scratch_wordlimit) || empty($data->assignsubmission_scratch_wordlimit_enabled)) {
            $wordlimit = 0;
            $wordlimitenabled = 0;
        } else {
            $wordlimit = $data->assignsubmission_scratch_wordlimit;
            $wordlimitenabled = 1;
        }

        $this->set_config('wordlimit', $wordlimit);
        $this->set_config('wordlimitenabled', $wordlimitenabled);

        return true;
    }

    /**
     * Add form elements for settings
     *
     * @param mixed $submission can be null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return true if elements were added to the form
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        global $USER, $DB, $CFG;
        //$PAGE->requires->js_call_amd('menu', 'init');
        //$PAGE->requires->js()
        $elements = array();

        $editoroptions = $this->get_edit_options();
        $submissionid = $submission ? $submission->id : 0;

        if (!isset($data->scratch)) {
            $data->scratch = '';
        }
        if (!isset($data->json)) {
            $data->json = '';
        }

        if (!isset($data->projremix)) {
            $data->projremix = '';
        }

        if (!isset($data->radioar)) {
            $data->radio_selection = '';
        }

        if (!isset($data->scratchformat)) {
            $data->scratchformat = editors_get_preferred_format();
        }

        if ($submission) {
            $scratchsubmission = $this->get_scratch_submission($submission->id);

            if ($scratchsubmission) {
                $data->scratch = $scratchsubmission->scratch;
                $data->scratchformat = $scratchsubmission->onlineformat;
                $data->projremix = $scratchsubmission->projremix;
                //$data-> = $scratchsubmission->radio_selection;
            }

        }

        $data = file_prepare_standard_editor($data,
                                             'scratch',
                                             $editoroptions,
                                             $this->assignment->get_context(),
                                             'assignsubmission_scratch',
                                             ASSIGNSUBMISSION_scratch_FILEAREA,
                                             $submissionid);
        $mform->addElement('editor', 'scratch_editor', $this->get_name(), null, $editoroptions);

        debugging();
        $mform->addElement('text', 'projremix', "Project Remix URL!", array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('projremix', PARAM_TEXT);
        } else {
            $mform->setType('projremix', PARAM_CLEANHTML);
        }

        //gets the username for the scratch website
        $scratchid = $USER->profile['scratchid'];
        $userjson = file_get_contents('https://api.scratch.mit.edu/users/' . $scratchid . '/projects');

        //Custom HTML
        $html = '<div class="block" style="width: 60%; height: 300px;"><div class="header"><div class="title"><h2>'
. 'insert custom HTML - ' . $scratchid . '</h2></div></div>';
        //debugging($this->get_json_from_url('https://api.scratch.mit.edu/users/djsanosa/projects'));
        $mform->addElement('static', 'div_scratch', '', $html);

        debugging();
        //MENU

        //Get images and project ids to populate the selection.
        //Returns key value pairs of ids and img urls.
        $list = $this->get_student_project_data($scratchid);

        //Generate list of text boxes
        $radioarray=array();
        foreach ($list as $id => $img){
          $imagehtml = '<img src="' . $img .  '" alt="" width="240" height="180" >';
          //$mform->addElement('advcheckbox', 'ratingtime', $id, $imagehtml, array('group' => $id), array(0, 1));
          $radioarray[] = $mform->createElement('radio', 'selection', $imagehtml, '', $id);


        }
        $mform->addGroup($radioarray, 'radioar', '', array(' '), false);

        return true;
    }

    /**
     * Editor format options
     *
     * @return array
     */
    private function get_edit_options() {
         $editoroptions = array(
           'noclean' => false,
           'maxfiles' => EDITOR_UNLIMITED_FILES,
           'maxbytes' => $this->assignment->get_course()->maxbytes,
           'context' => $this->assignment->get_context(),
           'return_types' => (FILE_INTERNAL | FILE_EXTERNAL | FILE_CONTROLLED_LINK)
        );
        return $editoroptions;
    }

    /**
     * Save data to the database and trigger plagiarism plugin,
     * if enabled, to scan the uploaded content via events trigger
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data) {
        global $USER, $DB;

        $editoroptions = $this->get_edit_options();

        $data = file_postupdate_standard_editor($data,
                                                'scratch',
                                                $editoroptions,
                                                $this->assignment->get_context(),
                                                'assignsubmission_scratch',
                                                ASSIGNSUBMISSION_scratch_FILEAREA,
                                                $submission->id);

        $scratchsubmission = $this->get_scratch_submission($submission->id);

        $fs = get_file_storage();

        $files = $fs->get_area_files($this->assignment->get_context()->id,
                                     'assignsubmission_scratch',
                                     ASSIGNSUBMISSION_scratch_FILEAREA,
                                     $submission->id,
                                     'id',
                                     false);

        // Check word count before submitting anything.
        $exceeded = $this->check_word_count(trim($data->scratch));
        if ($exceeded) {
            $this->set_error($exceeded);
            return false;
        }

        $params = array(
            'context' => context_module::instance($this->assignment->get_course_module()->id),
            'courseid' => $this->assignment->get_course()->id,
            'objectid' => $submission->id,
            'other' => array(
                'pathnamehashes' => array_keys($files),
                'content' => trim($data->scratch),
                'format' => $data->scratch_editor['format']
            )
        );
        if (!empty($submission->userid) && ($submission->userid != $USER->id)) {
            $params['relateduserid'] = $submission->userid;
        }
        $event = \assignsubmission_scratch\event\assessable_uploaded::create($params);
        $event->trigger();

        $groupname = null;
        $groupid = 0;
        // Get the group name as other fields are not transcribed in the logs and this information is important.
        if (empty($submission->userid) && !empty($submission->groupid)) {
            $groupname = $DB->get_field('groups', 'name', array('id' => $submission->groupid), '*', MUST_EXIST);
            $groupid = $submission->groupid;
        } else {
            $params['relateduserid'] = $submission->userid;
        }

        $count = count_words($data->scratch);

        // Unset the objectid and other field from params for use in submission events.
        unset($params['objectid']);
        unset($params['other']);
        $params['other'] = array(
            'submissionid' => $submission->id,
            'submissionattempt' => $submission->attemptnumber,
            'submissionstatus' => $submission->status,
            'scratchwordcount' => $count,
            'groupid' => $groupid,
            'groupname' => $groupname
        );

        if ($scratchsubmission) {
            //If editing

            $scratchsubmission->scratch = $data->scratch;
            $scratchsubmission->onlineformat = $data->scratch_editor['format'];

            // Save project URL and get and store JSON
            $scratchsubmission->radio_selection = 'http://projects.scratch.mit.edu/internalapi/project/'. $data->selection . '/get/';
            $scratchsubmission->projremix = 'http://projects.scratch.mit.edu/internalapi/project/'. $data->selection . '/get/';
            //$scratchsubmission->projremix = $data->projremix;
            $scratchsubmission->json = file_get_contents($scratchsubmission->projremix);

            $scratchsubmission->radio_selection = $data->selection;


            $params['objectid'] = $scratchsubmission->id;
            $updatestatus = $DB->update_record('assignsubmission_scratch', $scratchsubmission);
            $event = \assignsubmission_scratch\event\submission_updated::create($params);
            $event->set_assign($this->assignment);
            $event->trigger();

            //test
            debugging();
            print_r($data->selection);

            return $updatestatus;
        } else {
            //If submitting for the first time
            $scratchsubmission = new stdClass();
            $scratchsubmission->scratch = $data->scratch;
            $scratchsubmission->onlineformat = $data->scratch_editor['format'];

            // Save project URL and get and store JSON
            $scratchsubmission->projremix = 'http://projects.scratch.mit.edu/internalapi/project/'. $data->selection . '/get/';
            //$scratchsubmission->projremix = $data->projremix;
            $scratchsubmission->json = file_get_contents($scratchsubmission->projremix);



            ///Save to database
            $scratchsubmission->submission = $submission->id;
            $scratchsubmission->assignment = $this->assignment->get_instance()->id;
            $scratchsubmission->id = $DB->insert_record('assignsubmission_scratch', $scratchsubmission);
            $params['objectid'] = $scratchsubmission->id;
            $event = \assignsubmission_scratch\event\submission_created::create($params);
            $event->set_assign($this->assignment);
            $event->trigger();
            return $scratchsubmission->id > 0;
        }
    }

    /**
     * Return a list of the text fields that can be imported/exported by this plugin
     *
     * @return array An array of field names and descriptions. (name=>description, ...)
     */
    public function get_editor_fields() {
        return array('scratch' => get_string('pluginname', 'assignsubmission_scratch'));
    }

    /**
     * Get the saved text content from the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return string
     */
    public function get_editor_text($name, $submissionid) {
        if ($name == 'scratch') {
            $scratchsubmission = $this->get_scratch_submission($submissionid);
            if ($scratchsubmission) {
                return $scratchsubmission->scratch;
            }
        }

        return '';
    }

    /**
     * Get the content format for the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return int
     */
    public function get_editor_format($name, $submissionid) {
        if ($name == 'scratch') {
            $scratchsubmission = $this->get_scratch_submission($submissionid);
            if ($scratchsubmission) {
                return $scratchsubmission->onlineformat;
            }
        }

        return 0;
    }


     /**
      * Display scratch word count in the submission status table
      *
      * @param stdClass $submission
      * @param bool $showviewlink - If the summary has been truncated set this to true
      * @return string
      */
    public function view_summary(stdClass $submission, & $showviewlink) {
        global $CFG;

        $scratchsubmission = $this->get_scratch_submission($submission->id);
        // Always show the view link.
        $showviewlink = true;

        if ($scratchsubmission) {
            $text = $this->assignment->render_editor_content(ASSIGNSUBMISSION_scratch_FILEAREA,
                                                             $scratchsubmission->submission,
                                                             $this->get_type(),
                                                             'scratch',
                                                             'assignsubmission_scratch');

            $shorttext = shorten_text($text, 140);
            $plagiarismlinks = '';

            if (!empty($CFG->enableplagiarism)) {
                require_once($CFG->libdir . '/plagiarismlib.php');

                $plagiarismlinks .= plagiarism_get_links(array('userid' => $submission->userid,
                    'content' => trim($text),
                    'cmid' => $this->assignment->get_course_module()->id,
                    'course' => $this->assignment->get_course()->id,
                    'assignment' => $submission->assignment));
            }
            if ($text != $shorttext) {
                $wordcount = get_string('numwords', 'assignsubmission_scratch', count_words($text));

                return $plagiarismlinks . $wordcount . $shorttext;
            } else {
                return $plagiarismlinks . $shorttext;
            }
        }
        return '';
    }

    private function foo(){
      return true;
    }
    /**
     * Produce a list of files suitable for export that represent this submission.
     *
     * @param stdClass $submission - For this is the submission data
     * @param stdClass $user - This is the user record for this submission
     * @return array - return an array of files indexed by filename
     */
    public function get_files(stdClass $submission, stdClass $user) {
        //Used for Export
        global $DB;

        $files = array();
        $scratchsubmission = $this->get_scratch_submission($submission->id);

        if ($scratchsubmission) {
            $finaltext = $this->assignment->download_rewrite_pluginfile_urls($scratchsubmission->scratch, $user, $this);
            $formattedtext = format_text($finaltext,
                                         $scratchsubmission->onlineformat,
                                         array('context'=>$this->assignment->get_context()));
            $head = '<head><meta charset="UTF-8"></head>';
            $submissioncontent = '<!DOCTYPE html><html>' . $head . '<body>'. $formattedtext . '</body></html>';

            $url = $scratchsubmission->projremix;
            //$result = file_get_contents($url);
            //$submissioncontent .= $result;

            $submissioncontent .= $url;
            $submissioncontent .= "<p>-----------------</p><br>";


            //OVERWRITE SUBMISSION CONTENT FOR NOW
            $submissioncontent = $scratchsubmission->json;
            //$submissioncontent .= $scratchsubmission->scratch;
            //$submissioncontent .= $finaltext;
            //foo();

            $filename = get_string('scratchfilename', 'assignsubmission_scratch');
            $files[$filename] = array($submissioncontent);

            $fs = get_file_storage();

            $fsfiles = $fs->get_area_files($this->assignment->get_context()->id,
                                           'assignsubmission_scratch',
                                           ASSIGNSUBMISSION_scratch_FILEAREA,
                                           $submission->id,
                                           'timemodified',
                                           false);

            foreach ($fsfiles as $file) {
                $files[$file->get_filename()] = $file;
            }
        }

        return $files;
    }

    /**
     * Display the saved text content from the editor in the view table
     *
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission) {
        global $CFG;
        $result = '';

        $scratchsubmission = $this->get_scratch_submission($submission->id);

        if ($scratchsubmission) {

            // Render for portfolio API.
            $result .= $this->assignment->render_editor_content(ASSIGNSUBMISSION_scratch_FILEAREA,
                                                                $scratchsubmission->submission,
                                                                $this->get_type(),
                                                                'scratch',
                                                                'assignsubmission_scratch');

            $plagiarismlinks = '';

            if (!empty($CFG->enableplagiarism)) {
                require_once($CFG->libdir . '/plagiarismlib.php');

                $plagiarismlinks .= plagiarism_get_links(array('userid' => $submission->userid,
                    'content' => trim($result),
                    'cmid' => $this->assignment->get_course_module()->id,
                    'course' => $this->assignment->get_course()->id,
                    'assignment' => $submission->assignment));
            }
        }

        return $plagiarismlinks . $result;
    }

    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 assignment of this type and version.
     *
     * @param string $type old assignment subtype
     * @param int $version old assignment version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version) {
        if ($type == 'online' && $version >= 2011112900) {
            return true;
        }
        return false;
    }


    /**
     * Upgrade the settings from the old assignment to the new plugin based one
     *
     * @param context $oldcontext - the database for the old assignment context
     * @param stdClass $oldassignment - the database for the old assignment instance
     * @param string $log record log events here
     * @return bool Was it a success?
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldassignment, & $log) {
        // No settings to upgrade.
        return true;
    }

    /**
     * Upgrade the submission from the old assignment to the new one
     *
     * @param context $oldcontext - the database for the old assignment context
     * @param stdClass $oldassignment The data record for the old assignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $submission The data record for the new submission
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(context $oldcontext,
                            stdClass $oldassignment,
                            stdClass $oldsubmission,
                            stdClass $submission,
                            & $log) {
        global $DB;

        $scratchsubmission = new stdClass();
        $scratchsubmission->scratch = $oldsubmission->data1;
        $scratchsubmission->onlineformat = $oldsubmission->data2;

        $scratchsubmission->submission = $submission->id;
        $scratchsubmission->assignment = $this->assignment->get_instance()->id;

        if ($scratchsubmission->scratch === null) {
            $scratchsubmission->scratch = '';
        }

        if ($scratchsubmission->onlineformat === null) {
            $scratchsubmission->onlineformat = editors_get_preferred_format();
        }

        if (!$DB->insert_record('assignsubmission_scratch', $scratchsubmission) > 0) {
            $log .= get_string('couldnotconvertsubmission', 'mod_assign', $submission->userid);
            return false;
        }

        // Now copy the area files.
        $this->assignment->copy_area_files_for_upgrade($oldcontext->id,
                                                        'mod_assignment',
                                                        'submission',
                                                        $oldsubmission->id,
                                                        $this->assignment->get_context()->id,
                                                        'assignsubmission_scratch',
                                                        ASSIGNSUBMISSION_scratch_FILEAREA,
                                                        $submission->id);
        return true;
    }

    /**
     * Formatting for log info
     *
     * @param stdClass $submission The new submission
     * @return string
     */
    public function format_for_log(stdClass $submission) {
        // Format the info for each submission plugin (will be logged).
        $scratchsubmission = $this->get_scratch_submission($submission->id);
        $scratchloginfo = '';
        $scratchloginfo .= get_string('numwordsforlog',
                                         'assignsubmission_scratch',
                                         count_words($scratchsubmission->scratch));

        return $scratchloginfo;
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        $DB->delete_records('assignsubmission_scratch',
                            array('assignment'=>$this->assignment->get_instance()->id));

        return true;
    }

    /**
     * No text is set for this plugin
     *
     * @param stdClass $submission
     * @return bool
     */
    public function is_empty(stdClass $submission) {
        $scratchsubmission = $this->get_scratch_submission($submission->id);

        return empty($scratchsubmission->scratch);
    }

    /**
     * Determine if a submission is empty
     *
     * This is distinct from is_empty in that it is intended to be used to
     * determine if a submission made before saving is empty.
     *
     * @param stdClass $data The submission data
     * @return bool
     */
    public function submission_is_empty(stdClass $data) {
        if (!isset($data->scratch_editor)) {
            return true;
        }
        return !strlen((string)$data->scratch_editor['text']);
    }

    /**
     * Get file areas returns a list of areas this plugin stores files
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return array(ASSIGNSUBMISSION_scratch_FILEAREA=>$this->get_name());
    }

    /**
     * Copy the student's submission from a previous submission. Used when a student opts to base their resubmission
     * on the last submission.
     * @param stdClass $sourcesubmission
     * @param stdClass $destsubmission
     */
    public function copy_submission(stdClass $sourcesubmission, stdClass $destsubmission) {
        global $DB;

        // Copy the files across (attached via the text editor).
        $contextid = $this->assignment->get_context()->id;
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'assignsubmission_scratch',
                                     ASSIGNSUBMISSION_scratch_FILEAREA, $sourcesubmission->id, 'id', false);
        foreach ($files as $file) {
            $fieldupdates = array('itemid' => $destsubmission->id);
            $fs->create_file_from_storedfile($fieldupdates, $file);
        }

        // Copy the assignsubmission_scratch record.
        $scratchsubmission = $this->get_scratch_submission($sourcesubmission->id);
        if ($scratchsubmission) {
            unset($scratchsubmission->id);
            $scratchsubmission->submission = $destsubmission->id;
            $DB->insert_record('assignsubmission_scratch', $scratchsubmission);
        }
        return true;
    }

    /**
     * Return a description of external params suitable for uploading an scratch submission from a webservice.
     *
     * @return external_description|null
     */
    public function get_external_parameters() {
        $editorparams = array('text' => new external_value(PARAM_RAW, 'The text for this submission.'),
                              'format' => new external_value(PARAM_INT, 'The format for this submission'),
                              'itemid' => new external_value(PARAM_INT, 'The draft area id for files attached to the submission'));
        $editorstructure = new external_single_structure($editorparams, 'Editor structure', VALUE_OPTIONAL);
        return array('scratch_editor' => $editorstructure);
    }

    /**
     * Compare word count of scratch submission to word limit, and return result.
     *
     * @param string $submissiontext scratch submission text from editor
     * @return string Error message if limit is enabled and exceeded, otherwise null
     */
    public function check_word_count($submissiontext) {
        global $OUTPUT;

        $wordlimitenabled = $this->get_config('wordlimitenabled');
        $wordlimit = $this->get_config('wordlimit');

        if ($wordlimitenabled == 0) {
            return null;
        }

        // Count words and compare to limit.
        $wordcount = count_words($submissiontext);
        if ($wordcount <= $wordlimit) {
            return null;
        } else {
            $errormsg = get_string('wordlimitexceeded', 'assignsubmission_scratch',
                    array('limit' => $wordlimit, 'count' => $wordcount));
            return $OUTPUT->error_text($errormsg);
        }
    }

    public function get_student_project_data($scratchid){


      $userjson = file_get_contents('https://api.scratch.mit.edu/health ');
      $projjson = file_get_contents('https://api.scratch.mit.edu/users/'.$scratchid . '/projects');

      //debugging($projjson);

      $jsonIterator = new RecursiveIteratorIterator(
        new RecursiveArrayIterator(json_decode($projjson, TRUE)),
        RecursiveIteratorIterator::SELF_FIRST);

      $project_ids = array();
      $thumbnails = array();
      $data = array();
      foreach ($jsonIterator as $key => $val) {
        //if value is an array, just print the key
          //Checks for depth
          $d = $jsonIterator->getDepth();

          if ($d==1){

            //ProjectID
            if($key=='id'){
              $id = $val;
              array_push($project_ids,$val);
            }
            //Thumbnail
            if($key=='image'){
              $img = $val;
              array_push($thumbnails,$val);
              $data[$id] = $img;
            }
          }
      }

      /*
      foreach ($data as $d=>$i){
        print_r($d);
      }*/

      return $data;
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of settings
     * @since Moodle 3.2
     */
    public function get_config_for_external() {
        return (array) $this->get_config();
    }

    /**
    * @param string $targeturl
    * @return string
    */
    public function get_json_from_url($targeturl) {
      $result = file_get_contents($targeturl);
      return $result;
    }
}
