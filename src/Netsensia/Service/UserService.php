<?php
namespace Netsensia\Service;

use Netsensia\Service\NetsensiaService;
use Zend\Math\Rand;
use Zend\Crypt\Password\Bcrypt;

class UserService extends NetsensiaService
{
    private $defaultColumnValues;
    
    public function __construct(
        $defaultColumnValues
    )
    {
        $this->defaultColumnValues = $defaultColumnValues;
    }
    
    public function insertDefaultValues($userId)
    {
        if (count($this->defaultColumnValues) > 0) {

            $sql = 'UPDATE user ';
            foreach ($this->defaultColumnValues as $column => $value) {
                $sql .= 'SET ' . $column . ' = :' . $column . ',';        
            }
            $sql = substr($sql, 0, -1);
            
            $sql .= ' WHERE userid = :userid';
            $query = $this->getPreparedQuery($sql);
            
            foreach ($this->defaultColumnValues as $column => $value) {
                $query->bindParam($column, $value);
            }
            
            $query->bindParam('userid', $userId);
            $query->execute();
        }
    }
    
    public function activateAccount($code)
    {
        $sql = 'SELECT userid FROM user WHERE emailverifycode = :code';
        
        $query = $this->getPreparedQuery($sql);
        
        $query->bindParam('code', $code);
        $query->execute();
        
        if ($query->fetch()) {
            $sql = 'UPDATE user SET activated = \'Y\' WHERE emailverifycode = :code';
            $query = $this->getPreparedQuery($sql);
            $query->bindParam('code', $code);
            $query->execute();
            
            return true;
        } else {
            return false;
        }
    }
    
    public function encryptPassword($password)
    {
        $bcrypt = new Bcrypt();
        return $bcrypt->create($password);
    }
    
    public function generateActivationCode()
    {
        return Rand::getString(
            32, 
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234556789'
        );        
    }
    
    public function sendActivationEmail($userId)
    {
        $user = $this->loadModel('User', $userId);
        
        $this->sendMail(
            $this->translate('Please activate your account'),
            $user->get('email'),
            'netsensia/email/template/activate',
            ['activation_code' => $user->get('emailverifycode')]
        );
    }
    
    public function setNewPasswordResetCode($userModel)
    {
        $passwordResetCode = $this->generateActivationCode();
        
        $userModel->set('passwordresetcode', $passwordResetCode);
        $userModel->save();

        return $passwordResetCode;
    }
    
    public function processPasswordResetRequest($userId)
    {
        $userModel = $this->loadModel('User', $userId);
        $passwordResetCode = $this->setNewPasswordResetCode($userModel);
        
        $this->sendMail(
            $this->translate('Password reset request'),
            $userModel->get('email'),
            'netsensia/email/template/password-reset',
            [
                'password_reset_code' => $passwordResetCode
            ]
        );
        
        return $passwordResetCode;
    }
    
    public function getUserIdFromEmail($email)
    {
        $sql = 'SELECT userid FROM user WHERE email = :email';
                
        $query = $this->getPreparedQuery($sql);
        $query->bindParam('email', $email);
        
        return $this->getUserIdFromQuery($query);
    }
    
    public function getUserIdFromPasswordResetCode($code)
    {
        $sql = 'SELECT userid FROM user WHERE passwordresetcode = :passwordresetcode';
    
        $query = $this->getPreparedQuery($sql);
        $query->bindParam('passwordresetcode', $code);
    
        return $this->getUserIdFromQuery($query);
    }
    
    private function getUserIdFromQuery($query)
    {
        $query->execute();
        
        if ($row = $query->fetch()) {
            return $row['userid'];
        } else {
            return null;
        }    
    }
}
