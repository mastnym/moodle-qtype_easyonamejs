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
defined('MOODLE_INTERNAL') || die();

/**
 * Generates the output for easyonamejs questions.
 *
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_easyonamejs_renderer extends qtype_renderer {

    public function formulation_and_controls(question_attempt $qa, question_display_options $options) {
        global $CFG, $PAGE, $OUTPUT;
        $question = $qa->get_question();
        $answerinputid = $qa->get_qt_field_name('answer');
        $answer = $qa->get_last_qt_var('answer', '');
        $feedbackimage = '';
        $feedbackclass = '';
        $marvinjsconfig = get_config('qtype_easyonamejs');

        $protocol = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
        $marvinjspath = $marvinjsconfig->path;
        $PAGE->requires->js(new moodle_url($protocol . $_SERVER['HTTP_HOST'] . $marvinjspath . '/gui/lib/promise-1.0.0.min.js'));
        $PAGE->requires->js(new moodle_url($protocol . $_SERVER['HTTP_HOST'] . $marvinjspath . '/js/marvinjslauncher.js'));


        //render of question text
        $result = html_writer::tag('div', $question->format_questiontext($qa), array(
                    'class' => 'qtext'
        ));
        // if answer is corrected
        if ($options->correctness) {
            $fraction = $this->fraction_for_last_response($qa);
            $feedbackimage = $this->feedback_image($fraction);
            $feedbackclass = $this->feedback_class($fraction);
        }
        
        // append html with "answer" text + div for MarvinJS iframe + iframe
        $iframe = html_writer::start_tag('div', array('id' => $qa->get_qt_field_name('applet'), 'class' => 'easyonamejs resizable'));
        $loading = html_writer::div(get_string('loading', 'qtype_easyonamejs'), 'loading');
        $iframe .= html_writer::div($loading, 'marvin-overlay');
        $editor_attributes = array('id' => 'MSketch', 'class' => 'sketcher-frame ' . $feedbackclass,
            'src' => $marvinjspath . '/editor.html');
        if ($marvinjsconfig->usews) {
            $editor_attributes['src'] = $marvinjspath . '/editorws.html';
        }
        if ($options->readonly) {
            $editor_attributes['class'] = $editor_attributes['class'] . ' marvin-readonly';
        }
        $iframe .= html_writer::start_tag('iframe', $editor_attributes);
        $iframe .= html_writer::end_tag('iframe');
        $iframe .= html_writer::end_tag('div');

        $answerlabel = html_writer::tag('span', get_string('answer', 'qtype_shortanswer', ''),
                        array(
                    'class' => 'answerlabel'
        ));
        $result .= html_writer::tag('div', $answerlabel . $iframe, array(
                    'class' => 'ablock'));

        // just append div with error
        if ($qa->get_state() == question_state::$invalid) {
            $lastresponse = $qa->get_last_qt_data();
            $result .= html_writer::nonempty_tag('div', $question->get_validation_error($lastresponse),
                            array(
                        'class' => 'validationerror'
            ));
        }

        $answerinput = html_writer::empty_tag('input',
                        array(
                    'type' => 'hidden',
                    'id' => $answerinputid,
                    'name' => $answerinputid,
                    'value' => $answer
        ));
        
        // this needs to be here to remember users answer - $answerinput is replaced on change
        // so user can correct answers
        $readonlyanswerinput = html_writer::empty_tag('input',
                        array(
                    'type' => 'hidden',
                    'id' => $qa->get_qt_field_name('currentanswer'),
                    'name' => $qa->get_qt_field_name('currentanswer'),
                    'value' => $answer
        ));

        $result .= html_writer::tag('div', $answerinput . $readonlyanswerinput, array(
                    'class' => 'inputcontrol'
        ));
        
        
        // add chemaxon logo
        $result .= html_writer::start_tag('div', array('class' => 'licence_logo'));
        $result .= html_writer::start_tag('a', array('href' => 'http://www.chemaxon.com'));
        $result .= html_writer::empty_tag('img',
                        array(
                    'src' => $OUTPUT->pix_url('chemaxon', 'qtype_easyonamejs'),
                    'alt' => 'ChemAxon Licence Logo',
                    'id' => 'chemaxon'));
        $result .= html_writer::end_tag('a');
        $result .= $feedbackimage;
        $result .= html_writer::end_tag('div');
        $defaultsettings = isset($question->marvinsettings) && trim($question->marvinsettings) ? $question->marvinsettings : $marvinjsconfig->defaultsettings;
        $PAGE->requires->js_call_amd('qtype_easyonamejs/marvincontrols', 'initquestion',
                array(array('editorid' => $editor_attributes['id'],
                    'answerinputid' => $answerinputid,
                    'defaultsettings'=>$defaultsettings)));   
        return $result;
    }

    protected function fraction_for_last_response(question_attempt $qa) {
        $question = $qa->get_question();
        $lastresponse = $qa->get_last_qt_data();
        $answer = $question->get_matching_answer($lastresponse);
        return $answer ? $answer->fraction : 0;
    }

    public function specific_feedback(question_attempt $qa) {
        $question = $qa->get_question();
        $answer = $question->get_matching_answer($qa->get_last_qt_data());
        if (!$answer) {
            return '';
        }
        $feedback = '';
        if ($answer->feedback) {
            $feedback .= $question->format_text($answer->feedback, $answer->feedbackformat, $qa, 'question',
                    'answerfeedback', $answer->id);
        }
        return $feedback;
    }

    public function correct_response(question_attempt $qa) {
        $question = $qa->get_question();
        $correct_response = $question->get_correct_response();
        $answer = $question->get_matching_answer($correct_response);
        if (!$answer){
            return '';
        }  
        $answerinput = html_writer::empty_tag('input',
                        array(
                    'type' => 'hidden',
                    'name' => $qa->get_qt_field_name('correctanswer'),
                    'value' => $correct_response['answer']
        ));
        $show_button =  html_writer::empty_tag('input',
                        array(
                    'type' => 'button',
                    'name' => $qa->get_qt_field_name('showcorrectanswer'),
                    'value' => get_string('correct_answer', 'qtype_easyonamejs'),
                    'data-label-my' => get_string('my_answer', 'qtype_easyonamejs'),
                    'data-label-correct' => get_string('correct_answer', 'qtype_easyonamejs')
        ));
        return $answerinput . $show_button;   
    }
    
    protected function info(question_attempt $qa, qbehaviour_renderer $behaviouroutput,
            qtype_renderer $qtoutput, question_display_options $options, $number) {
        return parent::info($qa, $behaviouroutput,$qtoutput, $options, $number) + "blah";
    }
}