<?php

use TestSuite\Bootstrap;
use Netsensia\Form\UpdatePasswordForm;
use Zend\Form\Element;
use Netsensia\Test\DatabaseInitializer;

class UserServiceTest extends PHPUnit_Framework_TestCase 
{

    public function testCanCreateUpdatePasswordForm() 
    {
        $updatePasswordForm = new UpdatePasswordForm('testform');
        $updatePasswordForm->prepare();
        
        $this->assertTrue($updatePasswordForm->get('password') instanceof Element);
        $this->assertTrue($updatePasswordForm->get('confirmpassword') instanceof Element);
        $this->assertTrue($updatePasswordForm->get('send') instanceof Element);
        
        Bootstrap::initDatabase();
    }
    
    public function testPasswordResetCode()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $userService = $serviceManager->get('Netsensia\Service\UserService');
        $userIdFromEmail = $userService->getUserIdFromEmail('test@netsensia.com');
        
        $this->assertTrue(is_numeric($userIdFromEmail) && $userIdFromEmail > 0);
        
        $userModel = $serviceManager->get('UserModel');
        $userModel->init($userIdFromEmail); 
        
        $passwordResetCode = $userService->setNewPasswordResetCode($userModel);
        $this->assertEquals(32, strlen($passwordResetCode));
        
        $userIdFromPasswordResetCode = 
            $userService->getUserIdFromPasswordResetCode(
                $passwordResetCode
            );
        
        $this->assertEquals(
            $userIdFromEmail,
            $userIdFromPasswordResetCode 
        );
    }
}

