<?php
namespace Netsensia\Form;

use Zend\Form\Element;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Netsensia\Form\CaptchaForm;
use Zend\Form\Element\Checkbox;

class LoginForm extends NetsensiaForm
{
    
    public function __construct($name = null, $options = array())
    {
        parent::__construct('contact');
    }
    
    public function prepare()
    {
        $email = new Element('login-email');
        $email->setLabel($this->translate('Email'));
        $email->setAttributes(
            [
            'id' => 'login-email-standard',
            'type'  => 'text',
            'icon' => 'envelope',
            'class' => 'form-control',
            ]
        );
        
        $password = new Element('login-password');
        $password->setLabel($this->translate('Password'));
        $password->setAttributes(
            [
            'type'  => 'password',
            'id' => 'login-password-standard',
            'icon' => 'lock',
            'class' => 'form-control',
            ]
        );
        
        $rememberMe = new Checkbox('rememberme');
        $rememberMe->setLabel($this->translate('Remember me'));
        
        $send = new Element('send');
        $send->setValue('Login');
        $send->setAttributes(
            [
            'type'  => 'submit',
            'class' => 'btn btn-success'
            ]
        );
        
        $this->add($email);
        $this->add($password);
        $this->add($rememberMe);
         
        $this->add($send);

        parent::prepare();
    }
}
