<?php
namespace Netsensia\Form;

use Zend\Form\Form;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Hidden;
use Netsensia\Provider\ProvidesServiceLocator;
use Netsensia\Provider\ProvidesTranslator;

class NetsensiaForm extends Form
{
    private $translator;
    
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'POST');
    }
    
    public function addSubmit($label)
    {
        $submitButton = new Submit('form-submit');
        $submitButton->setValue($label);
        $this->add($submitButton);
    }
    
    public function addHidden($name, $value)
    {
        $hidden = new Hidden($name);
        $hidden->setValue($value);
        $this->add($hidden);
    }
    
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }
    
    protected function translate($text)
    {
        if (isset($this->translator)) {
            return $this->translator->translate($text);
        } else {
            return $text;
        }
    }
}
