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
 * Defines the editing form for the Stack question type.
 *
 * @package    qtype_stack
 * @copyright  2012 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/question/type/edit_question_form.php');
require_once($CFG->dirroot . '/question/type/stack/question.php');
require_once($CFG->dirroot . '/question/type/stack/stack/input/factory.class.php');
require_once($CFG->dirroot . '/question/type/stack/stack/answertest/controller.class.php');

require_once($CFG->dirroot . '/question/type/stack/stack/cas/keyval.class.php');
require_once($CFG->dirroot . '/question/type/stack/stack/cas/castext.class.php');

/**
 * Stack question editing form definition.
 *
 * @copyright  2012 The Open University.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qtype_stack_edit_form extends question_edit_form {

    protected function definition_inner(MoodleQuickForm $mform) {

        // Note that for the editor elements, we are using
        // $mform->getElement('prtincorrect')->setValue(...);
        // instead of setDefault, because setDefault does not work for editors.

        $mform->addHelpButton('questiontext', 'questiontext', 'qtype_stack');
        $mform->addRule('questiontext', get_string('questiontextnonempty','qtype_stack'), 'required', '', 'client', false, false);

        $qvars = $mform->createElement('textarea', 'questionvariables',
                get_string('questionvariables', 'qtype_stack'), array('rows' => 5, 'cols' => 80));
        $mform->insertElementBefore($qvars, 'questiontext');
        $mform->addHelpButton('questionvariables', 'questionvariables', 'qtype_stack');

        $sf = $mform->createElement('editor', 'specificfeedback',
                get_string('specificfeedback', 'question'), array('rows' => 10), $this->editoroptions);
        $mform->insertElementBefore($sf, 'generalfeedback');
        $mform->addHelpButton('generalfeedback', 'generalfeedback', 'qtype_stack');

        $mform->getElement('specificfeedback')->setValue(array('text' => '[[feedback:prt1]]'));
        $mform->addHelpButton('specificfeedback', 'specificfeedback', 'qtype_stack');
        
        $mform->addElement('textarea', 'questionnote',
                get_string('questionnote', 'qtype_stack'), array('rows' => 2, 'cols' => 80));
        $mform->addHelpButton('questionnote', 'questionnote', 'qtype_stack');
        
        // Inputs - for now, hard-code it to one input.
        $types = stack_input_factory::get_available_types();
        $typechoices = array();
        foreach ($types as $type => $notused) {
            $typechoices[$type] = get_string('inputtype' . $type, 'qtype_stack');
        }

        $inputname = 'ans1'; // TODO generalise this.
        $mform->addElement('header', 'answerhdr' . $inputname, get_string('inputheading', 'qtype_stack', $inputname));

        $mform->addElement('select', $inputname . 'type', get_string('inputtype', 'qtype_stack'), $typechoices);
        $mform->addHelpButton($inputname . 'type', 'inputtype', 'qtype_stack');
        
        $mform->addElement('text', $inputname . 'tans', get_string('teachersanswer', 'qtype_stack'), array('size' => 20));
        $mform->addRule($inputname . 'tans', get_string('teachersanswer','qtype_stack'), 'required', '', 'client', false, false);
        $mform->addHelpButton($inputname . 'tans', 'teachersanswer', 'qtype_stack');

        $mform->addElement('text', $inputname . 'boxsize', get_string('boxsize', 'qtype_stack'), array('size' => 3));
        $mform->setDefault($inputname . 'boxsize', 15);
        $mform->setType($inputname . 'boxsize', PARAM_INT);
        $mform->addHelpButton($inputname . 'boxsize', 'boxsize', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'strictsyntax',
                get_string('strictsyntax', 'qtype_stack'));
        $mform->setDefault($inputname . 'strictsyntax', true);
        $mform->addHelpButton($inputname . 'strictsyntax', 'strictsyntax', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'insertstars',
                get_string('insertstars', 'qtype_stack'));
        $mform->setDefault($inputname . 'insertstars', false);
        $mform->addHelpButton($inputname . 'insertstars', 'insertstars', 'qtype_stack');

        $mform->addElement('text', $inputname . 'syntaxhint', get_string('syntaxhint', 'qtype_stack'), array('size' => 20));
        $mform->addHelpButton($inputname . 'syntaxhint', 'syntaxhint', 'qtype_stack');

        $mform->addElement('text', $inputname . 'forbidwords', get_string('forbidwords', 'qtype_stack'), array('size' => 20));
        $mform->addHelpButton($inputname . 'forbidwords', 'forbidwords', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'forbidfloat',
                get_string('forbidfloat', 'qtype_stack'));
        $mform->setDefault($inputname . 'forbidfloat', true);
        $mform->addHelpButton($inputname . 'forbidfloat', 'forbidfloat', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'requirelowestterms',
                get_string('requirelowestterms', 'qtype_stack'));
        $mform->setDefault($inputname . 'requirelowestterms', false);
        $mform->addHelpButton($inputname . 'requirelowestterms', 'requirelowestterms', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'checkanswertype',
                get_string('checkanswertype', 'qtype_stack'));
        $mform->setDefault($inputname . 'checkanswertype', false);
        $mform->addHelpButton($inputname . 'checkanswertype', 'checkanswertype', 'qtype_stack');

        $mform->addElement('selectyesno', $inputname . 'mustverify',
                get_string('mustverify', 'qtype_stack'));
        $mform->setDefault($inputname . 'mustverify', true);
        $mform->addHelpButton($inputname . 'mustverify', 'mustverify', 'qtype_stack');
        
        $mform->addElement('selectyesno', $inputname . 'showvalidation',
                get_string('showvalidation', 'qtype_stack'));
        $mform->setDefault($inputname . 'showvalidation', true);
        $mform->addHelpButton($inputname . 'showvalidation', 'showvalidation', 'qtype_stack');

        // PRTs
        $prtname = 'prt1'; // TODO generalise this.

        $answertests = stack_ans_test_controller::get_available_ans_tests();
        $answertestchoices = array();
        foreach ($answertests as $test => $string) {
            $answertestchoices[$test] = get_string($string, 'qtype_stack');
        }

        $scoremodechoices = array(
            '=' => '=',
            '+' => '+',
            '-' => '-',
            '=AT' => '=AT',
        );

        $numnodes = optional_param($prtname . 'numnodes', 1, PARAM_INT) +
                optional_param($prtname . 'addnode', 0, PARAM_BOOL);
        $nextnodechoices = array('-1' => get_string('stop', 'qtype_stack'));
        for ($i = 0; $i < $numnodes; $i += 1) {
            $nextnodechoices[$i] = get_string('nodex', 'qtype_stack', $i + 1);
        }

        $mform->addElement('header', 'answerhdr' . $prtname, get_string('prtheading', 'qtype_stack', $prtname));

        $mform->addElement('text', $prtname . 'value', get_string('questionvalue', 'qtype_stack'), array('size' => 3));
        $mform->setDefault($prtname . 'value', 1);

        $mform->addElement('selectyesno', $prtname . 'autosimplify',
                get_string('autosimplify', 'qtype_stack'));
        $mform->setDefault($prtname . 'autosimplify', true);
        $mform->addHelpButton($prtname . 'autosimplify', 'autosimplify', 'qtype_stack');

        $mform->addElement('textarea', $prtname . 'feedbackvariables',
                get_string('feedbackvariables', 'qtype_stack'), array('rows' => 3, 'cols' => 80));

        $mform->addElement('static', $prtname . 'inputsnote', '',
                get_string('prtwillbecomeactivewhen', 'qtype_stack', html_writer::tag('b', $inputname)));

        // Create the section of the form for each node - general bits.
        $repeatoptions = array();

        $elements = array();

        $nodegroup = array();
        $nodegroup[] = $mform->createElement('select', $prtname . 'answertest',
                get_string('answertest', 'qtype_stack'), $answertestchoices);
        $nodegroup[] = $mform->createElement('text', $prtname . 'sans',
                get_string('sans', 'qtype_stack'), array('size' => 5));
        $nodegroup[] = $mform->createElement('text', $prtname . 'tans',
                get_string('tans', 'qtype_stack'), array('size' => 5));
        $nodegroup[] = $mform->createElement('text', $prtname . 'testoptions',
                get_string('testoptions', 'qtype_stack'), array('size' => 5));
        $nodegroup[] = $mform->createElement('selectyesno', $prtname . 'quiet',
                get_string('quiet', 'qtype_stack'));

        $elements[] = $mform->createElement('group', $prtname . 'node',
                html_writer::tag('b', get_string('nodex', 'qtype_stack', '{no}')),
                $nodegroup, null, false);

        // Create the section of the form for each node - the branches.
        foreach (array('true', 'false') as $branch) {
            $branchgroup = array();
            $branchgroup[] = $mform->createElement('select', $prtname . $branch . 'scoremode',
                    get_string('scoremode', 'qtype_stack'), $scoremodechoices);
            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'score',
                    get_string('score', 'qtype_stack'), array('size' => 2));
            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'penalty',
                    get_string('penalty', 'qtype_stack'), array('size' => 2));
            $branchgroup[] = $mform->createElement('select', $prtname . $branch . 'nextnode',
                    get_string('next', 'qtype_stack'), $nextnodechoices);
            $branchgroup[] = $mform->createElement('text', $prtname . $branch . 'answernote',
                    get_string('answernote', 'qtype_stack'), array('size' => 10));

            $elements[] = $mform->createElement('group', $prtname . 'nodewhen' . $branch,
                    get_string('nodexwhen' . $branch, 'qtype_stack'), $branchgroup, null, false);

            $elements[] = $mform->createElement('editor', $prtname . $branch . 'feedback',
                    get_string('nodex' . $branch . 'feedback', 'qtype_stack'), array('rows' => 1), $this->editoroptions);
        }

        $repeatoptions[$prtname . 'truescore']['default'] = 1;
        $repeatoptions[$prtname . 'falsescore']['default'] = 0;
        $repeatoptions[$prtname . 'trueanswernote']['default'] = $prtname . '-{no}-T';
        $repeatoptions[$prtname . 'falseanswernote']['default'] = $prtname . '-{no}-F';

        $this->repeat_elements($elements, 1, $repeatoptions, $prtname . 'numnodes',
                $prtname . 'addnode', 1, get_string('addanothernode', 'qtype_stack'), true);

        // Options
        $mform->addElement('header', 'optionsheader', get_string('options', 'qtype_stack'));

        $mform->addElement('selectyesno', 'questionsimplify',
                get_string('questionsimplify', 'qtype_stack'));
        $mform->setDefault('questionsimplify', true);
        $mform->addHelpButton('questionsimplify', 'autosimplify', 'qtype_stack');

        $mform->addElement('selectyesno', 'assumepositive',
                get_string('assumepositive', 'qtype_stack'));
        $mform->addHelpButton('assumepositive', 'assumepositive', 'qtype_stack');

        $mform->addElement('select', 'markmode',
                get_string('markmode', 'qtype_stack'), array(
                    qtype_stack_question::MARK_MODE_PENALTY => get_string('markmodepenalty', 'qtype_stack'),
                    qtype_stack_question::MARK_MODE_FIRST   => get_string('markmodefirst', 'qtype_stack'),
                    qtype_stack_question::MARK_MODE_LAST    => get_string('markmodelast', 'qtype_stack')));
        $mform->addHelpButton('markmode', 'markmode', 'qtype_stack');

        $mform->addElement('editor', 'prtcorrect',
                get_string('prtcorrectfeedback', 'qtype_stack'),
                array('rows' => 1), $this->editoroptions);
        $mform->getElement('prtcorrect')->setValue(array(
                'text' => get_string('defaultprtcorrectfeedback', 'qtype_stack')));

        $mform->addElement('editor', 'prtpartiallycorrect',
                get_string('prtpartiallycorrectfeedback', 'qtype_stack'),
                array('rows' => 1), $this->editoroptions);
        $mform->getElement('prtpartiallycorrect')->setValue(array(
                        'text' => get_string('defaultprtpartiallycorrectfeedback', 'qtype_stack')));

        $mform->addElement('editor', 'prtincorrect',
                get_string('prtincorrectfeedback', 'qtype_stack'),
                array('rows' => 1), $this->editoroptions);
        $mform->getElement('prtincorrect')->setValue(array(
                        'text' => get_string('defaultprtincorrectfeedback', 'qtype_stack')));

        $mform->addElement('select', 'multiplicationsign',
                get_string('multiplicationsign', 'qtype_stack'), array(
                    'dot'   => get_string('multdot', 'qtype_stack'),
                    'cross' => get_string('multcross', 'qtype_stack'),
                    'none'  => get_string('none')));
        $mform->addHelpButton('multiplicationsign', 'multiplicationsign', 'qtype_stack');

        $mform->addElement('selectyesno', 'sqrtsign',
                get_string('sqrtsign', 'qtype_stack'));
        $mform->setDefault('sqrtsign', true);
        $mform->addHelpButton('sqrtsign', 'sqrtsign', 'qtype_stack');

        $mform->addElement('select', 'complexno',
                get_string('complexno', 'qtype_stack'), array(
                    'i' => 'i', 'j' => 'j', 'symi' => 'symi', 'symj' => 'symj'));
        $mform->addHelpButton('complexno', 'complexno', 'qtype_stack');

        // Question tests.

        // To stop Moodle compaining.
        $mform->addElement('hidden', 'penalty', 0);
    }

    public function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        $question = $this->data_preprocessing_options($question);
        $question = $this->data_preprocessing_inputs($question);
        $question = $this->data_preprocessing_prts($question);

        if ($question->questiontext['text'] === '') {
            // Nasty hack to override what the base class does. The way it
            // prepares the questiontext field overwrites the default.
            $question->questiontext['text'] = '<p>[[input:ans1]]</p><div>[[validation:ans1]]</div>';
        }

        return $question;
    }

    /**
     * Do the bit of {@link data_preprocessing()} for the data in the qtype_stack table.
     * @param object $question the raw data.
     * @return object the updated $question updated object closer to being ready to send to the form.
     */
    protected function data_preprocessing_options($question) {
        if (!isset($question->options)) {
            return $question;
        }
        $opt = $question->options;

        $question->questionvariables   = $opt->questionvariables;
        $question->questionnote        = $opt->questionnote;
        $question->specificfeedback    = $this->prepare_text_field('specificfeedback',
                                            $opt->specificfeedback, $opt->specificfeedbackformat, $question->id);
        $question->prtcorrect          = $this->prepare_text_field('prtcorrect',
                                            $opt->prtcorrect, $opt->prtcorrectformat, $question->id);
        $question->prtpartiallycorrect = $this->prepare_text_field('prtpartiallycorrect',
                                            $opt->prtpartiallycorrect, $opt->prtpartiallycorrectformat, $question->id);
        $question->prtincorrect        = $this->prepare_text_field('prtincorrect',
                                            $opt->prtincorrect, $opt->prtincorrectformat, $question->id);
        $question->markmode            = $opt->markmode;
        $question->multiplicationsign  = $opt->multiplicationsign;
        $question->complexno           = $opt->complexno;
        $question->sqrtsign            = $opt->sqrtsign;
        $question->questionsimplify    = $opt->questionsimplify;
        $question->assumepositive      = $opt->assumepositive;

        return $question;
    }

    /**
     * Do the bit of {@link data_preprocessing()} for the data in the qtype_stack_inputs table.
     * @param object $question the raw data.
     * @return object the updated $question updated object closer to being ready to send to the form.
     */
    protected function data_preprocessing_inputs($question) {
        if (!isset($question->inputs)) {
            return $question;
        }

        foreach ($question->inputs as $inputname => $input) {
            $question->{$inputname . 'type'}               = $input->type;
            $question->{$inputname . 'tans'}               = $input->tans;
            $question->{$inputname . 'boxsize'}            = $input->boxsize;
            $question->{$inputname . 'strictsyntax'}       = $input->strictsyntax;
            $question->{$inputname . 'insertstars'}        = $input->insertstars;
            $question->{$inputname . 'syntaxhint'}         = $input->syntaxhint;
            $question->{$inputname . 'forbidfloat'}        = $input->forbidfloat;
            $question->{$inputname . 'requirelowestterms'} = $input->requirelowestterms;
            $question->{$inputname . 'checkanswertype'}    = $input->checkanswertype;
            $question->{$inputname . 'showvalidation'}     = $input->showvalidation;
        }

        return $question;
    }

    /**
     * Do the bit of {@link data_preprocessing()} for the data in the qtype_stack_prts table.
     * @param object $question the raw data.
     * @return object the updated $question updated object closer to being ready to send to the form.
     */
    protected function data_preprocessing_prts($question){
        if (!isset($question->prts)) {
            return $question;
        }

        foreach ($question->prts as $prtname => $prt) {
            $question->{$prtname . 'value'}             = 0 + $prt->value; // Remove excess decimals.
            $question->{$prtname . 'autosimplify'}      = $prt->autosimplify;
            $question->{$prtname . 'feedbackvariables'} = $prt->feedbackvariables;

            foreach ($prt->nodes as $node) {
                $question = $this->data_preprocessing_node($question, $prtname, $node);
            }
        }

        return $question;
    }

    /**
     * Do the bit of {@link data_preprocessing()} for one PRT node.
     * @param object $question the raw question data.
     * @param string $prtname the name of this PRT.
     * @param object $node the raw data about this node.
     * @return object the updated $question updated object closer to being ready to send to the form.
     */
    protected function data_preprocessing_node($question, $prtname, $node) {
        $nodename = $node->nodename;

        $question->{$prtname . 'answertest' }[$nodename] = $node->answertest;
        $question->{$prtname . 'sans'       }[$nodename] = $node->sans;
        $question->{$prtname . 'tans'       }[$nodename] = $node->tans;
        $question->{$prtname . 'testoptions'}[$nodename] = $node->testoptions;
        $question->{$prtname . 'quiet'      }[$nodename] = $node->quiet;

        // 0 + bit is to eliminate excessive decimal places from the DB.
        $question->{$prtname . 'truescoremode' }[$nodename] = $node->truescoremode;
        $question->{$prtname . 'truescore'     }[$nodename] = 0 + $node->truescore;
        $question->{$prtname . 'truepenalty'   }[$nodename] = 0 + $node->truepenalty;
        $question->{$prtname . 'truenextnode'  }[$nodename] = $node->truenextnode;
        $question->{$prtname . 'trueanswernote'}[$nodename] = $node->trueanswernote;
        $question->{$prtname . 'truefeedback'  }[$nodename] = $this->prepare_text_field(
                $prtname . 'truefeedback[' . $nodename . ']', $node->truefeedback,
                $node->truefeedbackformat, $node->id, 'prtnodetruefeedback');

        $question->{$prtname . 'falsescoremode' }[$nodename] = $node->falsescoremode;
        $question->{$prtname . 'falsescore'     }[$nodename] = 0 + $node->falsescore;
        $question->{$prtname . 'falsepenalty'   }[$nodename] = 0 + $node->falsepenalty;
        $question->{$prtname . 'falsenextnode'  }[$nodename] = $node->falsenextnode;
        $question->{$prtname . 'falseanswernote'}[$nodename] = $node->falseanswernote;
        $question->{$prtname . 'falsefeedback'  }[$nodename] = $this->prepare_text_field(
                $prtname . 'falsefeedback[' . $nodename . ']', $node->falsefeedback,
                $node->falsefeedbackformat, $node->id, 'prtnodefalsefeedback');

        return $question;
    }

    /**
     * Do the necessary data_preprocessing work for one text field.
     * @param string $field the field / file-area name. (These are assumed to be the same.)
     * @param string $text the raw text contents of this field.
     * @param int $format the text format (one of the FORMAT_... constants.)
     * @param int $itemid file area itemid.
     * @param string $filearea the file area name. Defaults to $field.
     * @return array in the format needed by the form.
     */
    protected function prepare_text_field($field, $text, $format, $itemid, $filearea = '') {
        if ($filearea === '') {
            $filearea = $field;
        }

        $data = array();
        $data['itemid'] = file_get_submitted_draft_itemid($field);
        $data['text'] = file_prepare_draft_area($data['itemid'], $this->context->id,
                'qtype_stack', $filearea, $itemid, $this->fileoptions, $text);
        $data['format'] = $format;
        return $data;
    }

    public function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);

        echo "<pre>";
        print_r($fromform);
        echo "</pre>";

        // (1) Validate all the fixes question fields.
        $questionvars = new stack_cas_keyval($fromform['questionvariables']);
        if (!$questionvars->get_valid()) {
            $errors['questionvariables'] = $questionvars->get_errors();
        }

        $generalfeedback = new stack_cas_text($fromform['generalfeedback']['text']);
        if (!$generalfeedback->get_valid()) {
            $errors['generalfeedback'] = $generalfeedback->get_errors();
        }

        if ('' == $fromform['questionnote']) {
            if (!(false === strpos($fromform['questionvariables'], 'rand'))) {
                $errors['questionnote'] = get_string('questionnotempty', 'qtype_stack');
            }
        } else {
            $questionnote = new stack_cas_text($fromform['questionnote']);
            if (!$questionnote->get_valid()) {
                $errors['questionnote'] = $questionnote->get_errors();
            }
        }

        //TODO: populate these arrays correctly!
        $inputs = array('ans1');
        $potentialresponsetrees = array('prt1');

        // (2) Validate all inputs.
        foreach ($inputs as $inputname ) {
            $teacheranswer = new stack_cas_casstring($fromform[$inputname . 'tans']);
            if (!$teacheranswer->get_valid('t')) {
                $errors[$inputname . 'tans'] = $teacheranswer->get_errors();
            }
        }
        // (3) Validate all prts.

        // (4) Validate queston text and specific feedback - depends on inputs and prts.
        $specificfeedback = new stack_cas_text($fromform['specificfeedback']['text']);
        if (!$specificfeedback->get_valid()) {
            $errors['specificfeedback'] = $specificfeedback->get_errors();
        }

        $questiontext = new stack_cas_text($fromform['questiontext']['text']);
        if (!$questiontext->get_valid()) {
            $errors['questiontext'] = $questiontext->get_errors();
        }
        // TODO: loop over the inputs...
        //[[input:ans1]]
        //[[validation:ans1]]

        $inputplaceholder = '[[input:ans1]]';
        if (false === strpos($fromform['questiontext']['text'], $inputplaceholder)) {
            $errors['questiontext'] .= get_string('questiontextmustcontain', 'qtype_stack', $inputplaceholder);
        }
        return $errors;
    }

    public function qtype() {
        return 'stack';
    }
}
