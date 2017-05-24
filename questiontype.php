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
 * Question type class for the easyonamejs question type.
 *
 * @package    qtype
 * @subpackage easyonamejs
 * @copyright  2014 onwards Carl LeBlond 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/engine/lib.php');
require_once($CFG->dirroot . '/question/type/shortanswer/questiontype.php');

class qtype_easyonamejs extends qtype_shortanswer {
    public function extra_question_fields() {
        return array('question_easyonamejs', 'answers', 'marvinsettings');
    }

    public function questionid_column_name() {
        return 'question';
    }

    protected function fill_answer_fields($answer, $questiondata, $key, $context) {
        //do not trim exported molfile molfile call grandparent
        return question_type::fill_answer_fields($answer, $questiondata, $key, $context);
    }
    public function import_from_xml($data, $question, \qformat_xml $format, $extra = null) {
        $qo = parent::import_from_xml($data, $question, $format, $extra);
        $new_answers = array();
        foreach ($qo->answer as $mol){
            $new_answers[] = str_replace("##first_chars##", "", $mol);
        }
        $qo->answer = $new_answers;
        return $qo;
    }
    public function export_to_xml($question, \qformat_xml $format, $extra = null) {
        foreach ($question->options->answers as $answer){
            // need to prepend string for export which is then striped out during import
            // question may begin with empty line/whitespace and we want to preserve these
            // there is no other reasonable way to hook
            $answer->answer = "##first_chars##" . $answer->answer;
        }
        return parent::export_to_xml($question, $format, $extra);
    }
}
