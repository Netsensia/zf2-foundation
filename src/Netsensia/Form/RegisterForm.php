<?php
namespace Netsensia\Form;

use Zend\Form\Element;
use Netsensia\Form\CaptchaForm;

class RegisterForm extends CaptchaForm
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }
    
    public function prepare()
    {
        $username = new Element('username');
        $username->setLabel($this->translate('Your username'));
        $username->setAttributes(
            [
            'type'  => 'text',
            'icon'  => 'user',
            'class' => 'form-control',
            ]
        );
        
        $email = new Element('email');
        $email->setLabel($this->translate('Email (used to login)'));
        $email->setAttributes(
            [
            'type'  => 'text',
            'icon'  => 'envelope',
            'class' => 'form-control',
            ]
        );
        
        $password = new Element('password');
        $password->setLabel($this->translate('Password'));
        $password->setAttributes(
            [
            'type'  => 'password',
            'id'    => 'password',
            'icon'  => 'lock',
            'class' => 'form-control',
            ]
        );
        
        $confirmPassword = new Element('confirmpassword');
        $confirmPassword->setLabel($this->translate('Confirm Password'));
        $confirmPassword->setAttributes(
            [
            'type'  => 'password',
            'icon'  => 'lock',
            'class' => 'form-control',
            ]
        );
        
        $send = new Element('send');
        $send->setValue($this->translate('Create My Account'));
        $send->setAttributes(
            [
            'type'  => 'submit',
            'class' => 'btn btn-success',
            ]
        );
        
        $this->add($username);
        $this->add($email);
        $this->add($password);
        $this->add($confirmPassword);
        $this->addCaptcha();
        $this->add($send);  

        parent::prepare();
    }
}
