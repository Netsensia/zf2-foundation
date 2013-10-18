<?php
namespace Netsensia\Form;

use Zend\Captcha\Dumb as CaptchaDumb;
use Zend\Form\Element\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Netsensia\Form\CaptchaForm;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

class ContactForm extends CaptchaForm
{
    protected $captcha;
    
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }
    
    public function prepare()
    {
        $name = new Element('email');
        $name->setLabel('Your email');
        $name->setAttributes(
            [
            'type'  => 'text',
            'icon' => 'envelope',
            'class' => 'form-control',
            ]
        );
        
        $message = new Element('message');
        $message->setLabel($this->translate('Your question or feedback'));
        $message->setAttributes(
            [
            'type'  => 'textarea',
            'class' => 'form-control',
            ]
        );
        
        $send = new Element('send');
        $send->setValue('Submit');
        $send->setAttributes(
            [
            'type'  => 'submit',
            'class' => 'btn btn-default',
            ]
        );
        
        $this->add($name);
        $this->add($message);
        $this->addCaptcha();
        $this->add($send);  

        parent::prepare();
    }
}
