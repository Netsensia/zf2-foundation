<?php
namespace Netsensia\Model;

use Zend\Validator\EmailAddress;
use PDO;
use Zend\Validator\StringLength;
use Zend\Validator\NotEmpty;

class Feedback extends DatabaseTableModel
{

    public function __construct()
    {
        $this->setTableName('feedback');

        parent::__construct();
        
        $this->addInputFilter(
            [ 
                'name' => 'email', 
                'required' => true, 
                'filters' => [ 
                    ['name' => 'StripTags'], 
                    ['name' => 'StringTrim'], 
                ], 
                'validators' => [
                    [
                        'name'    => 'NotEmpty',
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'Email is required',
                            ],
                        ],
                        'break_chain_on_failure' => true,
                    ],
                    [ 
                        'name' => 'EmailAddress', 
                        'options' => [ 
                            'encoding' => 'UTF-8', 
                            'min'      => 5, 
                            'max'      => 255, 
                            'messages' => [ 
                                EmailAddress::INVALID_FORMAT => 
                                    'Email address format is invalid' 
                            ], 
                        ],
                    ],
                ],
            ]
        );
        
        $this->addInputFilter(
            [
                'name'     => 'message',
                'required' => true,
                'filters'  => [
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ],
                'validators' => [
                    [
                        'name'    => 'NotEmpty',
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => 'message is required',
                            ],
                        ],
                    ],
                    [
                        'name'    => 'StringLength',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 10,
                            'max'      => 2000,
                            'messages' => [
                                StringLength::TOO_LONG => 
                                    'Message is too long',
                                StringLength::TOO_SHORT => 
                                    'Please provide a message',
                            ],
                        ],
                    ],
                ],
            ]
        );
    }
    
    public function init($feedbackId = null)
    {
        $this->setPrimaryKey(
            [
                "feedbackid" => $feedbackId
            ]
        );
        
        if ($feedbackId != null) {
            $this->load();
        }
        return $this;
    }
}
