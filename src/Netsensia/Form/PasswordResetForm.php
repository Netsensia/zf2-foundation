<?php
namespace Netsensia\Form;

use Zend\Form\Element;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Netsensia\Form\CaptchaForm;
use Zend\Form\Element\Checkbox;

class PasswordResetForm extends NetsensiaForm
{
    
    public function __construct($name = null, $options = array())
    {
       parent::__construct('passwordreset', $options);
    }
    
    public function prepare()
    {

        $email = new Element('passwordreset-email');
        $email->setLabel($this->translate('Email'));
        $email->setAttributes(
            [
            'id' => 'passwordreset-email',
            'type'  => 'text',
            'icon' => 'envelope',
            'class' => 'form-control',
            ]
        );
        
        $this->add($email);
        
        $send = new Element('send');
        $send->setValue('Request password reset');
        $send->setAttributes(
            [
            'type'  => 'submit',
            'class' => 'btn btn-default'
            ]
        );
                
        $this->add($send);

        parent::prepare();
    }
}
