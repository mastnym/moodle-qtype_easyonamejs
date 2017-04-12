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
 * Defines the editing form for the easyonamejs question type.
 *
 * @package    qtype
 * @subpackage easyonamejs
 * @copyright  2014 and onward Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/shortanswer/edit_shortanswer_form.php');
class qtype_easyonamejs_edit_form extends qtype_shortanswer_edit_form {
    protected function definition_inner($mform) {
        global $PAGE, $CFG;
        $marvinjsconfig = get_config('qtype_easyonamejs');
        $marvinjspath   = $marvinjsconfig->path;
        $protocol = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
        $PAGE->requires->js(new moodle_url($protocol . $_SERVER['HTTP_HOST'] . $marvinjspath . '/gui/lib/promise-1.0.0.min.js'));
        $PAGE->requires->js(new moodle_url($protocol . $_SERVER['HTTP_HOST'] . $marvinjspath . '/js/marvinjslauncher.js'));
        $mform->addElement('textarea', 'marvinsettings',
            get_string('editorquestionsettings', 'qtype_easyonamejs'), array("rows"=>6, 'cols'=>60));
        $mform->addElement('button', 'marvinsettingsset',get_string('marvinsettingsset', 'qtype_easyonamejs'));
        $mform->addElement('button', 'marvinsettingsget',get_string('marvinsettingsget', 'qtype_easyonamejs'));
        $mform->addElement('static', 'answersinstruct',
            get_string('instructions', 'qtype_easyonamejs'), get_string('filloutoneanswer', 'qtype_easyonamejs'));
        $mform->closeHeaderBefore('answersinstruct');
        $mform->addElement('html', html_writer::start_tag('div', array(
            'class' => 'easyonamejs resizable',
            'id' => 'appletdiv'
        )));
        $editor_attributes = array('id' => 'MSketch', 'class' => 'sketcher-frame',
            'src' => $marvinjspath . '/editor.html');
        if ($marvinjsconfig->usews){
            $editor_attributes['src'] = $marvinjspath . '/editorws.html';
        }
        $loading = html_writer::div(get_string('loading', 'qtype_easyonamejs'), 'loading');
        $mform->addElement('html', html_writer::div($loading, 'marvin-overlay')); 
        $mform->addElement('html', html_writer::start_tag('iframe', $editor_attributes));
        $mform->addElement('html', html_writer::end_tag('iframe'));
//        $mform->addElement('html', html_writer::start_tag('div', array(
//            'style' => 'float: left;font-style: italic ;'
//        )));
//        $mform->addElement('html', html_writer::start_tag('small'));
//        $mform->addElement('html', html_writer::link('http://www.chemaxon.com', get_string('easyonamejseditor', 'qtype_easyonamejs')));
//        $mform->addElement('html', html_writer::empty_tag('br'));
//        $mform->addElement('html', html_writer::tag('span', get_string('author', 'qtype_easyonamejs'), array(
//            'class' => 'easyonamejsauthor'
//        )));
//        $mform->addElement('html', html_writer::end_tag('small'));
//        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('div'));        
        $defaultsettings = trim($marvinjsconfig->defaultsettings);
        $PAGE->requires->js_call_amd('qtype_easyonamejs/marvincontrols', 'initedit', 
                array(array('editorid'=>$editor_attributes['id'],
                    'usews'=>$marvinjsconfig->usews,
                    'wsurl'=>$marvinjsconfig->wsurl,
                    'defaultsettings'=>$defaultsettings

            )));
        $this->add_per_answer_fields($mform,
            get_string('answerno', 'qtype_shortanswer', '{no}'), question_bank::fraction_options());
        $this->add_interactive_settings();
    }
    protected function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $answeroptions = array();
        $controls = array();
        
        $answeroptions[] = $mform->createElement('hidden', 'answer', '', array('class'=>'mol-answer'));
        $answeroptions[] = $mform->createElement('static', 'answersimg','',
                '<img class="marvin-image"/>');
        //$answeroptions[] = $mform->createElement('hidden', 'answer_smiles', '');
        $grade = $mform->createElement('select', 'fraction',
                get_string('grade'), $gradeoptions);
        $controls[] = $mform->createElement('button', 'insert',
            get_string('insertfromeditor', 'qtype_easyonamejs'), 'class = id_insert');
        $controls[] = $mform->createElement('button', 'view',
            get_string('view', 'qtype_easyonamejs'), 'class = id_view');        
        $controls[] = $mform->createElement('button', 'delete',
            get_string('delete', 'qtype_easyonamejs'), 'class = id_delete');
        
        $repeated[] = $mform->createElement('group', 'answeroptions',
                 $label, $answeroptions, null, false);
        $repeated[] = $mform->createElement('group', 'answercontrols',
                 '', $controls, null, false);
        $repeated[] = $mform->createElement('group', 'gradecontrols',
                 '', array($grade), null, false);
        $repeated[] = $mform->createElement('editor', 'feedback',
                get_string('feedback', 'question'), array('rows' => 5), $this->editoroptions);
        
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';   
        return $repeated;
    }
    public function qtype() {
        return 'easyonamejs';
    }
}
