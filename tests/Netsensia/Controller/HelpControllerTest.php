<?php
namespace Tests\Netsensia\Controller;

use PHPUnit_Framework_TestCase;
use Netsensia\Test\NetsensiaControllerTest;
use Netsensia\Controller\HelpController;
use Zend\Http\Request;

class HelpControllerTest extends NetsensiaControllerTest
{
    public function setup()
    {
        $this->setController(new HelpController(), 'help');
        parent::setup();
    }
    
    public function testRoutesAreAvailable()
    {
        $this->isRouteAvailable('index');
        $this->isRouteAvailable('contact');
    }
    
    public function testValidContactFormProcessesOk()
    {
        $this->routeMatch->setParam('action', 'contact');
        
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getPost()->set('email', 'test@netsensia.com');
        $this->request->getPost()->set('message', 'This is a message for you');
        
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        
        $this->assertEquals(302, $response->getStatusCode());
    }
    
    public function testInvalidEmailAddressMakesFormFail()
    {
        $this->routeMatch->setParam('action', 'contact');
        
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getPost()->set('email', 'inc@rrectemailaddress');
        $this->request->getPost()->set('message', 'This is a message for you');
    
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
    
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testEmptyMessageMakesFormFail()
    {
        $this->routeMatch->setParam('action', 'contact');
    
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getPost()->set('email', 'test@netsensia.com');
        $this->request->getPost()->set('message', '');
    
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
    
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testShortMessageMakesFormFail()
    {
        $this->routeMatch->setParam('action', 'contact');
    
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getPost()->set('email', 'test@netsensia.com');
        $this->request->getPost()->set('message', '123123');
    
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
    
        $this->assertEquals(200, $response->getStatusCode());
    }
 }
