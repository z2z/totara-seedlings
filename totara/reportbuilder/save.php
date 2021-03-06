<?php
/*
 * This file is part of Totara LMS
 *
 * Copyright (C) 2010 onwards Totara Learning Solutions LTD
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author Simon Coggins <simon.coggins@totaralms.com>
 * @package totara
 * @subpackage reportbuilder
 */

/**
 * Page containing save search form
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/totara/reportbuilder/lib.php');
require_once('report_forms.php');

require_login();

$id = optional_param('id', null, PARAM_INT); // id for report to save

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/totara/reportbuilder/save.php', array('id' => $id));
$PAGE->set_totara_menu_selected('myreports');

$report = new reportbuilder($id);
if (!$report->is_capable($id)) {
    print_error('nopermission', 'totara_reportbuilder');
}

$returnurl = $report->report_url();

$mform = new report_builder_save_form(null, compact('id', 'report'));

// form results check
if ($mform->is_cancelled()) {
    redirect($returnurl);
}
if ($fromform = $mform->get_data()) {
    if (empty($fromform->submitbutton)) {
        print_error('error:unknownbuttonclicked', 'totara_reportbuilder', $returnurl);
    }
    // handle form submission
    $todb = new stdClass();
    $todb->reportid = $fromform->id;
    $todb->userid = $fromform->creatoruserid;
    $todb->search = $fromform->search;
    $todb->name = $fromform->name;
    $todb->ispublic = $fromform->ispublic;
    $DB->insert_record('report_builder_saved', $todb);

    redirect($returnurl);
}

$fullname = $report->fullname;
$pagetitle = format_string(get_string('savesearch', 'totara_reportbuilder').': '.$fullname);

$PAGE->set_title($pagetitle);
$PAGE->navbar->add(get_string('report', 'totara_reportbuilder'));
$PAGE->navbar->add($fullname);
$PAGE->navbar->add(get_string('savesearch', 'totara_reportbuilder'));

echo $OUTPUT->header();

$mform->display();

echo $OUTPUT->footer();
