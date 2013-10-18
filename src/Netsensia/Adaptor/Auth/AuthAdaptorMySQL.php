<?php
namespace Netsensia\Adaptor\Auth;

use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Application\Model\User;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Netsensia\Provider\ProvidesServiceLocator;
use Netsensia\Provider\ProvidesConnection;
use Netsensia\Provider\ProvidesTranslator;
use Netsensia\Provider\ProvidesModels;
use Zend\Crypt\Password\Bcrypt;

class AuthAdaptorMySQL implements AdapterInterface, ServiceLocatorAwareInterface
{
    use ProvidesServiceLocator, ProvidesConnection, ProvidesModels, ProvidesTranslator;
    
    private $username;
    
    private $password;
    
    /**
     * Sets username and password for authentication
     *
     * @return void
     */
    public function setCredentials(
        $username, 
        $password
    )
    {
        $this->username = $username;
        $this->password = $password;
    }
    
    /**
     * Performs an authentication attempt
     */
    public function authenticate()
    {
        $userId = $this->getUserIdFromEmailAndPassword();
        if (!$userId ) { 
            $userId = $this->getUserIdFromUsernameAndPassword();
        }
        
        if ($userId) {
            $userModel = $this->loadModel('User', $userId);
            
            $userSessionModel = new UserSessionModel();
            $userSessionModel->setUserId($userId);
            $userSessionModel->setName($userModel->get('name'));
            
            $result = new Result(
                Result::SUCCESS,
                $userSessionModel
            );
        } else {
            $result = new Result(
                Result::FAILURE,
                null,
                [$this->translate('Login failed.  Please try again.')]
            );
        }
        
        return $result;
    }
    
    private function getUserIdFromEmailAndPassword()
    {
        $sql =
            "SELECT userid, password " .
            "FROM user " .
            "WHERE email = :email";
        
        $query = $this->getConnection()->prepare($sql);
        
        $query->execute(
            array(
                ':email' => $this->username,
            )
        );
        
        return $this->verifyPasswordHash($query);
    }
    
    private function getUserIdFromUsernameAndPassword()
    {
        $sql =
            "SELECT userid, password " .
            "FROM user " .
            "WHERE name = :name";
        
        $query = $this->getConnection()->prepare($sql);
    
        $query->execute(
            array(
                ':name' => $this->username,
            )
        );
    
        return $this->verifyPasswordHash($query);
    }
    
    private function verifyPasswordHash($query)
    {
        if ($row = $query->fetch()) {
            $bcrypt = new Bcrypt();
            if ($bcrypt->verify($this->password, $row['password'])) {
                return $row['userid'];
            }
        }
        
        return false;
    }
}
