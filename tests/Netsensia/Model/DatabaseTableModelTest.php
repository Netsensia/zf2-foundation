<?php

use Netsensia\Model\DatabaseTableModel;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Validator\NotEmpty;
use TestSuite\Bootstrap;

class DatabaseTableModelTest extends PHPUnit_Framework_TestCase 
{
    private $model;
    
    public function setUp()
    {
        $this->model = new DatabaseTableModel();
        $this->model->setServiceLocator(Bootstrap::getServiceManager());
        $this->model->setData(
            [
                'key1' => 'value1',
                'key2' => 'value2',
            ]
        );    
    }
    
    public function testExceptionThrownWhenSettingUnavailableKey()
    {
        $exceptionCaught = false;
        
        try {
            $this->model->set('key3', 'value3');
        } catch (Exception $e) {
            $exceptionCaught = true;
        }
        
        $this->assertTrue($exceptionCaught);
    }
    
    public function testCanSetAvailableKey()
    {
        $newValue = 'newvalue2';
        $this->model->set('key2', $newValue);
        $this->assertEquals($newValue, $this->model->get('key2'));
    }
    
    public function testCanSetAndGetInputFilter()
    {
        $inputFilter = new InputFilter();
        $inputFactory = new InputFactory();
        
        $inputFilter->add(
            $inputFactory->createInput(
                [
                    'name'     => 'password',
                    'required' => true,
                    'validators' => array(
                        [
                            'name'    => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    NotEmpty::IS_EMPTY => 'Test message',
                                )
                            ),
                            'break_chain_on_failure' => true,
                        ],
                    ),
                ]
            )
        );
        
        $this->model->setInputFilter($inputFilter);
        $this->assertEquals($inputFilter, $this->model->getInputFilter());
    }
    
    public function testReportsPopulatedStatusCorrectly()
    {
        $this->assertFalse($this->model->isPopulated());
        
        $this->model->setPrimaryKey(['key1'=>1]);
        $this->assertTrue($this->model->isPopulated());
        
        $this->model->setPrimaryKey(['key1'=>1, 'key2'=>2]);
        $this->assertTrue($this->model->isPopulated());
    }
    
    public function testThrowsExceptionIfPrimaryKeyNotArray()
    {
        $exceptionCaught = false;
        try {
            $this->model->setPrimaryKey(1);
        } catch (Exception $e) {
            $exceptionCaught = true;
        }
        
        $this->assertTrue($exceptionCaught);
    }
    
    public function testThrowsExceptionOnGetIdIfNotSingleColumnKey()
    {
        $this->model->setPrimaryKey(['key1'=>1, 'key2'=>2]);
        
        $exceptionCaught = false;
        try {
            $this->model->getId();
        } catch (Exception $e) {
            $exceptionCaught = true;
        }
    
        $this->assertTrue($exceptionCaught);
    }

    public function testThrowsExceptionOnGetIdIfNotPopulated()
    {
        $exceptionCaught = false;
        try {
            $this->model->getId();
        } catch (Exception $e) {
            $exceptionCaught = true;
        }
    
        $this->assertTrue($exceptionCaught);
    }
    
    public function testLoadReturnsFalseIfRecordNonExistent()
    {
        $this->model->setTableName('feedback');
        $this->model->setPrimaryKey(['feedbackid'=>-1]);
        
        $this->assertFalse($this->model->load());
    }
}

