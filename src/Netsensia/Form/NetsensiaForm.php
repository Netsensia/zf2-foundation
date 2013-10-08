<?php
namespace Netsensia\Form;

use Zend\Form\Form;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Select;
use Zend\Form\Element\Hidden;
use Zend\Form\Element;
use Zend\Db\TableGateway\TableGateway;
use Netsensia\Model\DatabaseTableModel;

class NetsensiaForm extends Form
{
    private $translator;
    private $dbAdapter = null;
    private $fieldPrefix = '';
    private $defaultIcon = 'align-justify';
    private $defaultClass = 'form-control';
    
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('method', 'POST');
    }
    
    public function addSubmit($label)
    {
        $submitButton = new Submit('form-submit');
        $submitButton->setValue($label);
        
        $submitButton->setAttributes(
            [
                'type'  => 'submit',
                'class' => 'btn btn-default',
            ]
        );
        
        $this->add($submitButton);
    }
    
    public function addSelect($options)
    {

        if (!is_array($options)) {
            $options = [ 'name' => $options ];
        }
        
        $name = $this->fieldPrefix . str_replace('-', '', $options['name']);
        

        if (isset($options['label'])) {
            $label = $options['label'];
        } else {
            $label = ucwords(str_replace('-', ' ', $options['name']));
        }
        
        if (isset($options['icon'])) {
            $icon = $options['icon'];
        } else {
            $icon = $this->defaultIcon;
        }
        
        if (isset($options['class'])) {
            $class = $options['class'];
        } else {
            $class = $this->defaultClass;
        }
        
        if (isset($options['table'])) {
            $table = $options['table'];
        } else {
            $table = $options['name'];
        }
        
        if (isset($options['tableKey'])) {
            $tableKey = $options['tableKey'];
        } else {
            $tableKey = $options['name'] . 'id';
        }
        
        if (isset($options['tableValue'])) {
            $tableValue = $options['tableValue'];
        } else {
            $tableValue = $options['name'];
        }
        
        $select = new Select($name);
        $select->setLabel($label);
        
        if (!$this->dbAdapter) {
            throw new \Exception('DB Adapter is not set');
        }
        
        $table = new TableGateway($table, $this->dbAdapter);
        
        $rowset = $table->select();
        
        $optionsArray = [];

        foreach ($rowset as $row) {
            $optionsArray[$row[$tableKey]] = $row[$tableValue];
        }
        
        $select->setValueOptions($optionsArray);
        
        $select->setAttributes(
            [
                'type'  => 'select',
                'icon'  => $icon,
                'class' => $class,
            ]
        );
        
        $this->add($select);
    }
    
    public function addText($options)
    {
        if (!is_array($options)) {
            $options = [ 'name' => $options ];
        }
        
        $name = $this->fieldPrefix . str_replace('-', '', $options['name']);
        
        if (isset($options['label'])) {
            $label = $options['label'];
        } else {
            $label = ucwords(str_replace('-', ' ', $options['name']));
        }
        
        if (isset($options['icon'])) {
            $icon = $options['icon'];
        } else {
            $icon = $this->defaultIcon;
        }
        
        if (isset($options['class'])) {
            $class = $options['class'];
        } else {
            $class = $this->defaultClass;
        }
    
        $select = new Element($name);
        $select->setLabel($label);
    
        $select->setAttributes(
            [
                'type'  => 'text',
                'icon'  => $icon,
                'class' => $class,
            ]
        );
    
        $this->add($select);
    }
    
    public function addHidden($name, $value)
    {
        $hidden = new Hidden($name);
        $hidden->setValue($value);
        $this->add($hidden);
    }
    
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }    

    public function setFieldPrefix($fieldPrefix)
    {
        $this->fieldPrefix = $fieldPrefix;
    }
    
    public function getFieldPrefix()
    {
        return $this->fieldPrefix;
    }
    
    public function setDefaultIcon($icon)
    {
        $this->defaultIcon = $icon;
    }

    public function setDefaultClass($class)
    {
        $this->defaultClass = $class;
    }
    
    public function setDataFromModel(DatabaseTableModel $model)
    {
        $modelData = $model->getData();
        $formData = [];
        
        $prefix = $this->getFieldPrefix();
        
        foreach ($modelData as $key => $value) {
            $formData[$prefix . $key] = $value;           
        }
        
        $this->setData($formData);
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
