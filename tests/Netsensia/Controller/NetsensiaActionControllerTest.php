<?php
namespace Tests\Netsensia\Controller;

use Netsensia\Controller\NetsensiaActionController;
use Zend\Mvc\Controller\PluginManager;

class NetsensiaActionControllerTest extends \PHPUnit_Framework_TestCase
{
    private $testMessages;
    private $controller;
    
    public function setUp()
    {
        $this->controller = new NetsensiaActionController();
        
        $this->testMessages = [
            'Test message one',
            'Test message two',
        ];
    }
    
    private function setupMockFlashMessenger($messageType)
    {
        $flashMessenger = $this->getMock('Zend\Mvc\Controller\Plugin\FlashMessenger');
        
        $flashMessenger->expects($this->any())
                       ->method('has' . $messageType . 'Messages')
                       ->will($this->returnValue(true));
        
        $flashMessenger->expects($this->any())
                       ->method('get' . $messageType . 'Messages')
                       ->will($this->returnValue($this->testMessages));

        $plugins = new PluginManager();
        $plugins->setService('FlashMessenger', $flashMessenger);
        $this->controller->setPluginManager($plugins);
    }
    
    public function testFlashMessengerReturnsCorrectMessageTypes()
    {
        foreach (['', 'info', 'error', 'success'] as $messageType) {
            
        	$this->setupMockFlashMessenger($messageType);
            
            $messages = $this->controller->getFlashMessages();
            
            $this->assertEquals(2, count($messages));
            
            if ($messageType == '') {
                $messageSuffix = '';
            } else {
                $messageSuffix = '##' . $messageType;
            }
            
            $this->assertEquals($this->testMessages[0] . $messageSuffix, $messages[0]);
            $this->assertEquals($this->testMessages[1] . $messageSuffix, $messages[1]);            
        }
    } 
}
