<?php
namespace Netsensia\Form\View\Helper;

use Zend\View\Helper\AbstractHelper;

use Zend\Form\View\Helper\FormElement;
use Zend\Form\Element\Submit;

class BootstrapForm extends AbstractHelper 
{
    protected $form;
    protected $view;
    protected $options;
 
    /**
     * __invoke
     *
     * @access public
     * @param  Zend\Form\Form $form
     * @param  string $title
     * @return String
     */
    public function __invoke($form, $title, $action, $options = array())
    {
        $this->form     = $form;
        $this->view     = $this->getView();
        $this->options  = $options;
        
        $this->openForm($title, $action);
        
        $this->renderFieldsets();
        
        $this->renderAdditionalElements();
        $this->renderButton();
        $this->closeForm();
    }
    
    protected function renderElements($elements)
    {
        echo('<div class="well">');
        
        foreach ($elements as $element) {
            if ($element instanceof Submit) {
                continue;
            }
            echo('<div class="control-group">');
            echo('<label class="control-label">' . $element->getLabel() . '</label>');
            echo('<div class="controls">');
        
            if ($element->getAttribute('icon')) {
                echo('<div class="input-group">');
                echo('<span class="input-group-addon"><i class="glyphicon glyphicon-' . $element->getAttribute('icon') . '"></i></span>');
                echo $this->view->formElement($element);
                echo('</div>');
            } else {
                echo $this->view->formElement($element);
            }
            if ($element->getMessages()) {
                foreach ($element->getMessages() as $message) {
                    echo('<div class="form-field-error">');
                    echo($message);
                    echo('</div>');
                }
            }
            if ($element->getName() == 'password') {
                echo('<p id="password-strength" class="hint"/></p>');
            }
            echo('</div>');
            echo('</div>');
        }
        echo('</div>');
        
    }
    
    protected function renderFieldsets()
    {
        foreach ($this->form->getFieldsets() as $fieldset) {
            $elements = $fieldset->getElements();
            echo('<fieldset name="' . $fieldset->getName() . '">');
            $this->renderElements($elements);
            echo('</fieldset>');
        }
    }
    
    protected function renderSubmit($elements)
    {
        foreach ($elements as $element) {
            if ($element instanceof Submit) {
                $element->setAttribute('class', 'btn');
                echo $this->view->formElement($element);
            }
        }
    }
    
    protected function renderAdditionalElements() {
        $numVisibleElements = 0;
        foreach ($this->form->getElements() as $element) {
            if ($element instanceof Submit) {
                continue;
            }
            $numVisibleElements ++;
        }
        if ($numVisibleElements > 0) {
            $this->renderElements($this->form->getElements());
        }
    }
    
    protected function renderButton() {
        if ($this->form->getElements()) {
            $this->renderSubmit($this->form->getElements());
        }
    }
    
    protected function renderMessages()
    {
        if ($this->form->getMessages()) {
            echo('<div class="alert alert-error">');
            echo('<button type="button" class="close" data-dismiss="alert">&times;</button>');
            echo($this->getView()->translate('There were some errors on the form.  Please review your entries and try again.'));
            echo('</div>');
        }        
    }
    
    protected function openForm($title, $action) {
        
        echo('<div class="container">');
        echo('<legend>' . $title . '</legend>');
        
        $this->renderMessages();
        
        echo(
            '<form class="form-horizontal" ' .
                  'name="' . $this->form->getName() . '" ' .
                  'id="' . $this->form->getName() . '" ' .
                  'action="' . $action . '" ' .
                  'method="post">'
        );
    }
    
    protected function closeForm() {
        echo('</form>');
        
        echo('</div>');
    }
}
