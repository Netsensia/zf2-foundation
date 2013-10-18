<?php
namespace Netsensia\Provider;

trait ProvidesUserInfo
{
    public function isLoggedOn()
    {
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        return $authService->hasIdentity();
    }
    
    public function getUserIdentity()
    {
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        return $authService->getIdentity();
    }
    
    public function getUsername()
    {
        return $this->getUserIdentity()->getName();
    }
    
    public function getUserId()
    {
        return $this->getUserIdentity()->getUserId();
    }
        
    public function getUserSessionModel()
    {
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        return $authService->getIdentity();
    }
    
    public function getUserModel()
    {
         $userModel = $this->getServiceLocator()->get('UserModel')->init($this->getUserId());
         return $userModel;   
    }
}

?>