<?php

use Netsensia\Form\UpdatePasswordForm;
use Zend\Form\Element;

class UpdatePasswordFormTest extends PHPUnit_Framework_TestCase 
{

    public function testCanCreateUpdatePasswordForm() 
    {
        $updatePasswordForm = new UpdatePasswordForm('testform');
        $updatePasswordForm->prepare();
        
        $this->assertTrue($updatePasswordForm->get('password') instanceof Element);
        $this->assertTrue($updatePasswordForm->get('confirmpassword') instanceof Element);
        $this->assertTrue($updatePasswordForm->get('send') instanceof Element);

    }
    
}

