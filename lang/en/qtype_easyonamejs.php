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
 * Strings for component 'qtype_easyonamejs', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage easyonamejs
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Chemistry - Name to Structure or Reaction (MarvinJS)';
$string['pluginname_help'] = 'Students must draw  structure in which you predefine. You can ask questions such as;<ul><li>Draw (1S,2R)-2-methylcyclohexanol below?</li><li>Please draw the structure of water showing correct lone pairs"</li><li>Draw acetic acid and its dissociation?"</li></ul>Supports lone pair and radical electrons, charges, reactions, R/S, E/Z and many other features!';
$string['easyonamejs_options'] = 'Path to MarvinJS installation';
$string['easyonamejsobabel_options'] = 'Path to OpenBabel installation';
$string['easyonamejs_options_usews'] = 'Use chemaxon webservices?';
$string['easyonamejs_options_usews_desc'] = 'If checked Chemaxon webservices will be used as a main conversion tool. You need to have Chemaxon webservices properly set up (webservices.js file in MarvinJS folder)';
$string['easyonamejs_options_wsurl'] = 'URL of Chemaxon webservices';
$string['easyonamejs_options_wsurl_desc'] = 'This is the base URL for Chemaxon webservices, defaults to <code>/webservices</code>';
$string['easyonamejsobabel_options_default_settings'] = 'Default display settings of MarvinJS editor';
$string['easyonamejsobabel_options_default_settings_desc'] = 'Json string with default editor settings (each question allows separate editor settings). If empty, default editor settings will be used. Example: <code>{
"cpkColoring": true,
"lonePairsVisible": true,
"toolbars": "reporting"
}</code>';

$string['pluginname_link'] = 'question/type/easyonamejs';
$string['pluginnameadding'] = 'Adding a Name-to-Structure question';
$string['pluginnameediting'] = 'Editing a Name-to-Structure question';
$string['pluginnamesummary'] = 'Students must draw  structure in which you predefine. You can ask questions such as;<ul><li>Draw (1S,2R)-2-methylcyclohexanol below?</li><li>Please draw the structure of water showing correct lone pairs"</li><li>Draw acetic acid and its dissociation?"</li></ul>Supports lone pair and radical electrons, charges, reactions, R/S, E/Z and many other features!';
$string['loading'] = 'Loading MarvinJS editor...<br/> If this message does not get replaced by the MarvinJS editor then you have not got javascript working in your browser.';
$string['configeasyonamejsoptions'] = 'The path of your MarvinJS installation relative to your web root.  (e.g. If your moodle is installed at /var/www/moodle and you install your MarvinJS at /var/www/marvinjs then you should use the default /marvinjs)';

$string['configeasyonamejsobabeloptions'] = 'The path to your openbabel (obabel) executable.';
$string['filloutoneanswer'] = '<b><ul>
<li>Input your question text in Question Text box above.  (e.g. Draw water in the applet below.)</li>
<li>Draw the structure or reaction in the applet below.</li>
<li>Press the "Insert from editor" button to export the structure. Image should appear in the answer.</li>
<li>Add additional correct or incorrect answers in the optional fields below along with there respective feedbacks.</li>
</ul></b>';
$string['editorquestionsettings'] = 'MarvinJS display settings for this question. If empty global settings will be used.';
$string['marvinsettingsget'] = 'Use current settings from editor below.';
$string['marvinsettingsset'] = 'Set these options to editor below.';
$string['insertfromeditor'] = 'Insert from editor';
$string['instructions'] = 'Instructions';

$string['easyonamejseditor'] = 'MarvinJS Editor';
$string['author'] = 'Question type courtesy of<br />CL and JF,<br />Indiana University of Pennsylvania';

$string['insert'] = 'Insert from editor';
$string['view'] = 'View in editor';
$string['delete'] = 'Delete';
$string['correct_answer'] = 'Show correct Answer';
$string['my_answer'] = 'Show my answer';

$string['initfailure'] = 'Initialization of MarvinJS editor was not successful, please contact your admin.';
$string['initfailuretitle'] = 'MarvinJS initialization failure!';

$string['importfailure'] = 'Import of structure to MarvinJS was not successful, please contact your admin.';
$string['importfailuretitle'] = 'Cannot import structure to MarvinJS';

$string['importfailure'] = 'Export of structure to MarvinJS was not successful, question won\'t work as expected, please contact your admin.';
$string['exportfailuretitle'] = 'Cannot export structure from MarvinJS';

$string['marvinsettingsimportfailure'] = 'Json with settings seems to be corrupted. MarvinJS was not able to import it. Try to check syntax, or set settings in editor and import.';
$string['marvinsettingsimportfailuretitle'] = 'Cannot set these settings to MarvinJS.';