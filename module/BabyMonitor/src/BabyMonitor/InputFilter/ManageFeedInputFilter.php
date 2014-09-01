<?php
/**
 * Created by PhpStorm.
 * User: mattsetter
 * Date: 29/08/14
 * Time: 22:02
 */

namespace BabyMonitor\InputFilter;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Zend\Filter\HtmlEntities;
use Zend\Filter\StringTrim;
use Zend\Filter\StripNewlines;
use Zend\Filter\StripTags;
use Zend\Filter\FilterChain;
use Zend\Validator;
use Zend\I18n\Validator\Float;

class ManageFeedInputFilter extends InputFilter
{
    protected $_requiredFields = array(
        "feedDate",
        "feedTime",
        "feedAmount",
    );

    protected $_optionalFields = array(
        "feedId",
        "feedTemperature",
        "feedNotes",
    );

    public function __construct()
    {
        // add the fields to the input filter
        $this->_addRequiredFields()
            ->_addOptionalFields();
    }

    protected function _addOptionalFields()
    {
        foreach ($this->_optionalFields as $fieldName) {
            $input = new Input($fieldName);
            $input->setRequired(true)
                ->setAllowEmpty(true)
                ->setFilterChain($this->_getStandardFilter());
            switch ($fieldName) {

                case ("feedId"):
                    $input->getValidatorChain()
                        ->attach(
                            new Validator\Digits(
                                array(
                                    'messageTemplates' => array(
                                        Validator\Digits::NOT_DIGITS => 'The value supplied is not a valid number',
                                        Validator\Digits::STRING_EMPTY => 'A value must be supplied',
                                        Validator\Digits::INVALID => 'The value supplied is not a valid number',
                                    )
                                )
                            )
                        );
                    break;

                case ("feedTemperature"):
                    $input->getValidatorChain()
                        ->attach(
                            new Float(
                                array(
                                    'messageTemplates' => array(
                                        Float::NOT_FLOAT => 'The value supplied is not a valid number',
                                        Float::INVALID => 'The value supplied is not a valid number',
                                    )
                                )
                            )
                        );
                    break;
            }
            $this->add($input);
        }
        return $this;
    }

    protected function _addRequiredFields()
    {
        foreach ($this->_optionalFields as $fieldName) {
            $input = new Input($fieldName);
            $input->setRequired(true)
                ->setAllowEmpty(true)
                ->setFilterChain($this->_getStandardFilter());
            switch ($fieldName) {

                case ("feedDate"):
                    $input->getValidatorChain()
                        ->attach(new Validator\Date(array()));
                    break;

                case ("feedAmount"):
                    $input->getValidatorChain()
                        ->attach(
                            new Float(
                                array(
                                    'messageTemplates' => array(
                                        Float::NOT_FLOAT => 'The value supplied is not a valid number',
                                        Float::INVALID => 'The value supplied is not a valid number',
                                    )
                                )
                            )
                        )
                        ->attach(
                            new Validator\GreaterThan(
                                array(
                                    'min' => 0,
                                    'messageTemplates' => array(
                                        Validator\GreaterThan::NOT_GREATER => 'The value supplied must not be an empty string',
                                    )
                                )
                            )
                        );
                    break;
            }

            $this->add($input);

        }
        return $this;
    }

    protected function _getStandardFilter()
    {
        $baseInputFilterChain = new FilterChain();
        $baseInputFilterChain->attach(new HtmlEntities())
            ->attach(new StringTrim())
            ->attach(new StripNewlines())
            ->attach(new StripTags());

        return $baseInputFilterChain;
    }

} 