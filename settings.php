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
 * easyonamejs question renderer class.
 *
 * @package    qtype
 * @subpackage easyonamejs
 * @copyright  2014 onwards Carl LeBlond 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;
$PAGE->requires->js_call_amd('qtype_easyonamejs/marvincontrols', 'initsettings'); 
if ($ADMIN->fulltree) {
    $settings->add(new admin_setting_configtext('qtype_easyonamejs/path',
                   get_string('easyonamejs_options', 'qtype_easyonamejs'),
                   get_string('configeasyonamejsoptions', 'qtype_easyonamejs'), '/marvin4js', PARAM_TEXT));
    
    $settings->add(new admin_setting_configcheckbox('qtype_easyonamejs/usews',
                   get_string('easyonamejs_options_usews', 'qtype_easyonamejs'),
                   get_string('easyonamejs_options_usews_desc', 'qtype_easyonamejs'), true));
    
    $settings->add(new admin_setting_configtext('qtype_easyonamejs/wsurl',
                   get_string('easyonamejs_options_wsurl', 'qtype_easyonamejs'),
                   get_string('easyonamejs_options_wsurl_desc', 'qtype_easyonamejs'), '', PARAM_URL));
    
    $settings->add(new admin_setting_configtext('qtype_easyonamejs/obabelpath',
                   get_string('easyonamejsobabel_options', 'qtype_easyonamejs'),
                   get_string('configeasyonamejsobabeloptions', 'qtype_easyonamejs'), '/usr/bin/obabel', PARAM_TEXT));
    
    $settings->add(new admin_setting_configtextarea('qtype_easyonamejs/defaultsettings',
                   get_string('easyonamejsobabel_options_default_settings', 'qtype_easyonamejs'),
                   get_string('easyonamejsobabel_options_default_settings_desc', 'qtype_easyonamejs'), '', PARAM_TEXT));
    
    
}
