<?php

use Zend\Form\Element;
use Netsensia\Form\CaptchaForm;
use Zend\Form\Fieldset;
use Netsensia\Form\NetsensiaForm;
use Netsensia\Form\View\Helper\BootstrapForm;
use Zend\Form\Element\Text;
use Zend\View\View;
use Zend\View\Helper\ViewModel;
use Zend\View\Renderer\PhpRenderer;
use Zend\Mvc\Controller\PluginManager;
use Zend\Form\View\Helper\FormElement;
use Zend\View\HelperPluginManager;

class BootstrapFormTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @outputBuffering disabled
     */
    public function testCanRenderFieldsets() 
    {
        $this->expectOutputRegex(
            '/<form(.*)<fieldset(.*)<\/fieldset>(.*)<fieldset(.*)<\/fieldset>(.*)<\/form>/'
        );
        
        $form = new NetsensiaForm();
        $form->addHidden('test1', 'testvalue');
        
        $hidden = new Element\Hidden('asdasd');
        $hidden->setValue('123');
        $form->add($hidden);
        
        $element1 = new Text('testelement1');
        $element1->setLabel('Test Element');
        $element1->setAttribute('icon', 'pencil');

        $element2 = new Text('testelement2');
        $element2->setLabel('Test Element 2');
        $element2->setAttribute('icon', 'pencil');
        
        $fieldset1 = new Fieldset('testfieldset1');
        $fieldset1->add($element1);

        $fieldset2 = new Fieldset('testfieldset2');
        $fieldset2->add($element2);
        
        $form->add($fieldset1);
        $form->add($fieldset2);
        
        $helpers = new HelperPluginManager();
        $helpers->setService('formElement', new FormElement());
        $view = new PhpRenderer();
        $view->setHelperPluginManager($helpers);

        $viewHelper = new BootstrapForm();
        $viewHelper->setView($view);
        $viewHelper($form, 'testform', '/');
    }
}

