<?php

use Zend\Form\Element;
use Netsensia\Form\CaptchaForm;

class CaptchaFormTest extends PHPUnit_Framework_TestCase 
{

    public function testCanAddCaptchaToForm() 
    {
        $testOptions = [
            'showCaptcha' => true,
            'captchaConfig' =>
                [
                    'private-key' => 'test',
                    'public-key' => 'test',
                ]
        ];
        
        $form = new CaptchaForm('testname', $testOptions);
        $form->addCaptcha();
        
        $this->assertTrue($form->get('captcha') instanceof Element);
    }
    
}

