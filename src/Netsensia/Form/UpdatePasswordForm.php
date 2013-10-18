<?php
namespace Netsensia\Form;

use Zend\Form\Element;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Netsensia\Form\CaptchaForm;

class UpdatePasswordForm extends NetsensiaForm
{
    
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }
    
    public function prepare()
    {
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
        
        $confirmPassword = new Element('confirm-password');
        $confirmPassword->setLabel($this->translate('Confirm Password'));
        $confirmPassword->setAttributes(
            [
            'type'  => 'password',
            'icon'  => 'lock',
            'class' => 'form-control',
            ]
        );
        
        $send = new Element('send');
        $send->setValue($this->translate('Update password'));
        $send->setAttributes(
            [
            'type'  => 'submit',
            'class' => 'btn btn-success'
            ]
        );
        
        $this->add($password);
        $this->add($confirmPassword);
        $this->add($send);

        parent::prepare();
    }
}
