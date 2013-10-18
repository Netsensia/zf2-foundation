<?php

use Zend\Form\Element;
use Netsensia\Form\NetsensiaForm;

class NetsensiaFormTest extends PHPUnit_Framework_TestCase 
{

    public function testCanCreateUpdatePasswordForm() 
    {
        $form = new NetsensiaForm();
        $form->addHidden('test', 'testvalue');
        
        $this->assertTrue($form->get('test') instanceof Element);
    }
    
}

