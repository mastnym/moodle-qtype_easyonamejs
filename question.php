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
 * easyonamejs Molecular Editor question definition class.
 *
 * @package    qtype
 * @subpackage easyonamejs
 * @copyright  2014 onwards Carl LeBlond 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
//global $qa;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/shortanswer/question.php');
$generatedfeedback = "";
class qtype_easyonamejs_question extends qtype_shortanswer_question {
    public function compare_response_with_answer(array $response, question_answer $answer) {
 // Check to see if correct or not.
	$usrsmiles = $this->openbabel_convert_molfile($response['answer'], 'can');
        $anssmiles = $this->openbabel_convert_molfile($answer->answer, 'can');

        //echo "response smiles = <pre>".$usrsmiles."</pre>";
        //echo "answer smiles = <pre>".$anssmiles."</pre>";

        if ($usrsmiles == $anssmiles) {
            return true;
        } else {
            return false;
	}
    }
    public function get_expected_data() {
        return array(
            'answer' => PARAM_RAW,
            'easyonamejs' => PARAM_RAW,
            'mol' => PARAM_RAW
        );
    }
    public function openbabel_convert_molfile($molfile, $format) {
        $marvinjsconfig = get_config('qtype_easyonamejs_options');
        $descriptorspec = array(
           0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
           1 => array("pipe", "w"),  // stdout is a pipe that the child will write to
           2 => array("pipe", "r") // stderr is a file to write to
        );
        $output = '';
//        $process = proc_open('/usr/bin/obabel -imol -o' . $format . ' --title', $descriptorspec, $pipes);
        $process = proc_open($marvinjsconfig->obabelpath . ' -imol -o' . $format . ' --title', $descriptorspec, $pipes);


        //echo $process;

        if (is_resource($process)) {
                    //echo "HERE9";
            // $pipes now looks like this:
            // 0 => writeable handle connected to child stdin
            // 1 => readable handle connected to child stdout
            // Any error output will be appended to /var/log/apache2/obabelerr.log
           

            fwrite($pipes[0], $molfile);
            fclose($pipes[0]);
            //var_dump($pipes[1]);
            $output = stream_get_contents($pipes[1]);
            //echo "inchi=".$inchi;
            fclose($pipes[1]);

            $err = stream_get_contents($pipes[2]);
            fclose($pipes[2]);

            // It is important that you close any pipes before calling
            // proc_close in order to avoid a deadlock
            $return_value = proc_close($process);
            //echo "command returned $return_value\n";
            //echo "error = $err";

            error_log("command returned $return_value\n");
            error_log("outpur = " . trim($output));
            error_log("mol = $molfile");
            error_log("error = $err");
        }
        return trim($output);
    }


}
