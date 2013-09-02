<?php
namespace Netsensia\Form;

use Zend\Captcha\ReCaptcha as CaptchaWidget;
use Zend\Form\Element\Captcha;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Fieldset;

class CaptchaForm extends NetsensiaForm
{
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }
    
    public function addCaptcha()
    {
        $captcha = new CaptchaWidget();
        
        $captcha->setPrivkey($this->options['captchaConfig']['private-key']);
        $captcha->setPubkey($this->options['captchaConfig']['public-key']);
        
        if ($this->options['showCaptcha']) {
            $this->add(
                [
                    'type' => 'Zend\Form\Element\Captcha',
                    'name' => 'captcha',
                    'options' => [
                        'label' => $this->translate('Please verify you are human'),
                        'captcha' => $captcha,
                    ],
                ]
           );
        }
    }

}
