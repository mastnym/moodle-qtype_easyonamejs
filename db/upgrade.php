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
 * Easyoname question type upgrade code.
 *
 * @package    qtype
 * @subpackage easyoname
 * @copyright  1999 onwards Martin Dougiamas {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade code for the calculated question type.
 * @param int $oldversion the version we are upgrading from.
 */
function xmldb_qtype_easyonamejs_upgrade($oldversion) {
    global $CFG, $DB;
    $dbman = $DB->get_manager();
    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.
    
     if ($oldversion < 2017041102) {

        // Define field marvinsettings to be added to question_easyonamejs.
        $table = new xmldb_table('question_easyonamejs');
        $field = new xmldb_field('marvinsettings', XMLDB_TYPE_TEXT, null, null, null, null, null, 'answers');

        // Conditionally launch add field marvinsettings.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Easyonamejs savepoint reached.
        upgrade_plugin_savepoint(true, 2017041102, 'qtype', 'easyonamejs');
    }

    return true;
}
