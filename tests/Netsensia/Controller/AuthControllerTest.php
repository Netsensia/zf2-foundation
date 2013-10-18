<?php
namespace Tests\Netsensia\Controller;

use Netsensia\Controller\AuthController;
use Netsensia\Test\NetsensiaControllerTest;
use Zend\Http\Request;

class AuthControllerTest extends NetsensiaControllerTest
{
    public function setUp()
    {
        $this->setController(new AuthController(), 'auth');
        parent::setUp();
    }
    
    public function testRoutesAreAvailable()
    {
        $this->isRouteAvailable('login');
        $this->isRouteAvailable('logout');
    }
    
    public function testValidLogin()
    {
        $this->routeMatch->setParam('action', 'login');

        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getPost()->set('login-email', 'test@netsensia.com');
        $this->request->getPost()->set('login-password', 'testpassword');
        $this->request->getPost()->set('remember', 'remember');
        
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNotEmpty($this->controller->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity());
       
    }
    
    public function testInvalidLogin()
    {
        $this->routeMatch->setParam('action', 'login');
    
        $this->request->setMethod(Request::METHOD_POST);
        $this->request->getPost()->set('login-email', 'test@netsensia.com');
        $this->request->getPost()->set('login-password', 'wrongpassword');
        $this->request->getPost()->set('remember', 'remember');
    
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
    
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEmpty($this->controller->getServiceLocator()->get('Zend\Authentication\AuthenticationService')->getIdentity());
    }
 }
