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
 * Copyright (C) 2007-2011 Catalyst IT (http://www.catalyst.net.nz)
 *
 * @package    mod
 * @subpackage facetoface
 * @copyright 2024 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    shamiso.jaravaza@blindsidenetworks.com (shamiso [dt] jaravaza [at] blindsidenetworks [dt] com)
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once('lib.php');

$s = required_param('s', PARAM_INT); // Facetoface session ID.
$backtoallsessions = optional_param('backtoallsessions', 0, PARAM_INT); // Face-to-face activity to return to.

if (!$session = facetoface_get_session($s)) {
    throw new moodle_exception('error:incorrectcoursemodulesession', 'facetoface');
}
if (!$session->allowcancellations) {
    throw new moodle_exception('error:cancellationsnotallowed', 'facetoface');
}
if (!$facetoface = $DB->get_record('facetoface', ['id' => $session->facetoface])) {
    throw new moodle_exception('error:incorrectfacetofaceid', 'facetoface');
}
if (!$course = $DB->get_record('course', ['id' => $facetoface->course])) {
    throw new moodle_exception('error:coursemisconfigured', 'facetoface');
}
if (!$cm = get_coursemodule_from_instance("facetoface", $facetoface->id, $course->id)) {
    throw new moodle_exception('error:incorrectcoursemoduleid', 'facetoface');
}

require_course_login($course);
$context = context_course::instance($course->id);
$contextmodule = context_module::instance($cm->id);
require_capability('mod/facetoface:view', $context);

$pagetitle = format_string($facetoface->name);

$PAGE->set_cm($cm);
$PAGE->set_url('/mod/facetoface/bbbrooms.php', ['s' => $s, 'backtoallsessions' => $backtoallsessions]);

$PAGE->set_title($pagetitle);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

$heading = get_string('bbbroomfor', 'facetoface', format_string($facetoface->name));

echo $OUTPUT->box_start();
echo $OUTPUT->heading($heading);

$buttontext = get_string('joinbbb', 'facetoface');
$button = html_writer::tag(
    'button',
    $buttontext,
    [
        'type' => 'button',
        'class' => 'btn btn-primary',
    ]
);
echo html_writer::div($button, 'text-center');

echo $OUTPUT->box_end();
echo $OUTPUT->footer($course);
