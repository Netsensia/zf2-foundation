<?php
namespace Netsensia\Model;

use Zend\Validator\EmailAddress;
use PDO;
use Zend\Validator\StringLength;
use Zend\Validator\NotEmpty;

class User extends DatabaseTableModel
{
    public function __construct()
    {
        $this->setTableName('user');
        
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
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Email is required',
                            )
                        ),
                        'break_chain_on_failure' => true,
                    ],
                    [ 
                        'name' => 'EmailAddress', 
                        'options' => [ 
                            'encoding' => 'UTF-8', 
                            'min'      => 5, 
                            'max'      => 255, 
                            'messages' => array( 
                                EmailAddress::INVALID_FORMAT => 'Email address format is invalid' 
                            ) 
                        ],
                    ],
                ], 
            ]
        );
    
        $this->addInputFilter(
            [
                'name'     => 'username',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Username is required',
                            )
                        ),
                    ),
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max'      => 25,
                            'messages' => array(
                                StringLength::TOO_LONG => 'Username can not be more than 25 characters long',
                                StringLength::TOO_SHORT => 'Username must be at least two characters long',
                            )
                        ),
                    ),
                ),
            ]
        );
    
        $this->addInputFilter(
            [
                'name'     => 'realname',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max'      => 100,
                            'messages' => array(
                                StringLength::TOO_LONG => 'Real name can not be more than 100 characters long',
                            )
                        ),
                    ),
                ),
            ]
        );
    }
    
    public function init($userId = null)
    {
        $this->setPrimaryKey(array("userid" => $userId));
        if ($userId != null) {
            $this->load();
        }
        return $this;
    }
    
    public function isEmailTaken($email)
    {
        $sql =
            "SELECT userid " .
            "FROM " . $this->getTableName() . " " .
            "WHERE email = :email";
        
        $query = $this->getConnection()->prepare($sql);
        
        $query->execute(
            array(
                ':email' => $email,
            )
        );
        
        return ($query->rowCount() == 1); 
    }

    public function isUsernameTaken($username)
    {
        $sql =
            "SELECT userid " .
            "FROM " . $this->getTableName() . " " .
            "WHERE name = :name";
    
        $query = $this->getConnection()->prepare($sql);
    
        $query->execute(
            array(
                ':name' => $username,
            )
        );
    
        return ($query->rowCount() == 1);
    }
    
    public function getUserId()
    {
        $primaryKey = $this->getPrimaryKey();
        return $primaryKey['userid'];
    }
}
