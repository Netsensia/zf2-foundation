<?php
namespace Netsensia\Form;

use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Select;
use Zend\Form\Element\Hidden;
use Zend\Form\Element;
use Zend\Db\TableGateway\TableGateway;
use Netsensia\Model\DatabaseTableModel;
use Zend\Validator\NotEmpty;
use Zend\Validator\Identical;

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
    
    public function addRelation($options)
    {
		foreach ($options['fields'] as $field) {
			// Model field, referenced table, referenced table field
			$field['name'] = $options['column'] . '_' . $options['table'] . '_' . $field['name'];
			if (!isset($field['icon'])) {
				$field['icon'] = $options['icon'];
			}
			if (!isset($field['type'])) {
				$field['type'] = 'text';
			}
			switch ($field['type']) {
				case 'text' : $this->addText($field);
					break;
				case 'select' : $this->addSelect($field);
					break;
			}
		}
    }
    
    public function addAddress($column)
    {
    	$this->addRelation(
    		[
			'column' => $column,
    		'table'  => 'address',
    		'icon'   => 'home',
    		'fields' => [
	    			[
	    	    	'name'=>'address-1', 
	            	],
	    			[
	    			'name'=>'address-2',
	    			],
	    			[
	    			'name'=>'address-3',
	    			],
	    			[
	    			'name'=>'town',
	    			],
	    			[
	    			'name'=>'county',
	    			],
	    			[
	    			'name'=>'postcode',
	    			],
	    			[
	    			'name'=>'country',
	    			'type'=>'select',
	    			],
    			]
    		]
    	);
    }
    
    public function addSelect($options)
    {

        if (!is_array($options)) {
            $options = [ 'name' => $options ];
        }
        
        $parts = explode('_', $options['name']);
        $ultimateName = $parts[count($parts)-1];
        
        $name = $this->fieldPrefix . str_replace('-', '', $options['name']);
        
        if (isset($options['label'])) {
            $label = $options['label'];
        } else {
            $label = ucwords(str_replace('-', ' ', $ultimateName));
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
            $table = $ultimateName;
        }
        
        if (isset($options['tableKey'])) {
            $tableKey = $options['tableKey'];
        } else {
            $tableKey = $table . 'id';
        }
        
        if (isset($options['tableValue'])) {
            $tableValue = $options['tableValue'];
        } else {
            $tableValue = $table;
        }
        
        $select = new Select($name . 'id');
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
                'id'    => $name,
                'type'  => 'select',
                'icon'  => $icon,
                'class' => $class,
            ]
        );
        
        $this->add($select);
    }
    
    public function addSelectWithInvisibleOther($options)
    {
        if (!is_array($options)) {
            $options = [ 'name' => $options ];
        }
        
        $this->addSelect($options);
        
        $name = $options['name'] . 'other';
        
        if (isset($options['label'])) {
            $label = $options['label'];
        } else {
            $label = ucwords(str_replace('-', ' ', $options['name']));
        }
        
        $label = 'Other ' . $label;

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

        $options = [
            'name'  => $name,
            'label' => $label,
            'icon'  => $icon,
            'class' => $class . ' invisible-other'
        ];
        
        $this->addText($options);
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
        	$parts = explode('_', $options['name']);
        	$label = $parts[count($parts)-1];
            $label = ucwords(str_replace('-', ' ', $label));
        }
        
        if (isset($options['type'])) {
            $type = $options['type'];
        } else {
            $type = 'text';
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
    
        $text = new Element($name);
        $text->setLabel($label);
    
        $text->setAttributes(
            [
                'id'    => $name,
                'type'  => $type,
                'icon'  => $icon,
                'class' => $class,
            ]
        );
    
        $this->add($text);
    }
    
    public function addTextArea($options)
    {
        if (is_array($options)) {
            $options['type'] = 'textarea';
        } else {
            $options = [ 
                'name' => $options,
                'type' => 'textarea', 
            ];
        }
        
        $this->addText($options);
    }    
    
    public function addPasswordPair()
    {
        $this->addText(
            [
                'name'=>'password', 
                'icon'=>'lock',
                'type'=>'password',
            ]
        );
        $this->addText(
            [
                'name'=>'confirm-password', 
                'icon'=>'lock',
                'type'=>'password',
                'label'=>$this->translate('Confirm Password'),
            ]
        );

        $inputFilter = $this->getInputFilter();
        
        $inputFactory = new InputFactory();
        
        $inputFilter->add($inputFactory->createInput(
                [
                'name'     => $this->fieldPrefix . 'password',
                'required' => true,
                'validators' => array(
                    [
                    'name'    => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => $this->translate('Please choose a password'),
                        )
                    ),
                    'break_chain_on_failure' => true,
                    ],
                ),
                ]
            )
        );
        
        $inputFilter->add($inputFactory->createInput(
                [
                'name'     => $this->fieldPrefix . 'confirmpassword',
                'required' => true,
                'validators' => array(
                    [
                    'name'    => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => $this->translate('Please confirm your password'),
                        )
                    ),
                    'break_chain_on_failure' => true,
                    ],
                    array(
                        'name'    => 'Identical',
                        'options' => array(
                            'token' => $this->fieldPrefix . 'password',
                            'messages' => array(
                                Identical::NOT_SAME => $this->translate("Passwords don't match"),
                            )
                        ),
                    ),
                ),
                ]
            )
        );

        $this->setInputFilter($inputFilter);
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
            if ($key != 'password') {
                $formData[$prefix . $key] = $value;
            }           
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
