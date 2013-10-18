<?php
namespace Netsensia\Test;

use TestSuite\Bootstrap;
use Zend\Mvc\Router\Http\TreeRouteStack as HttpRouter;
use Zend\Http\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Netsensia\Adaptor\Auth\UserSessionModel;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class NetsensiaControllerTest extends AbstractHttpControllerTestCase
{
    
    protected $request;
    protected $response;
    protected $routeMatch;
    protected $event;
    
    protected $controller;
    
    private   $controllerName;
    
    protected function setController($controller, $controllerName)
    {
        $this->controller = $controller;
        $this->controllerName = $controllerName;
    }

    public function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        $this->request    = new Request();
        $this->routeMatch = new RouteMatch(array('controller' => $this->controllerName));
        $this->event      = new MvcEvent();
        $config = $serviceManager->get('Config');
        $routerConfig = isset($config['router']) ? $config['router'] : array();
        $router = HttpRouter::factory($routerConfig);

        $this->event->setRouter($router);
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
        
        $this->setApplicationConfig($serviceManager->get('ApplicationConfig'));
        parent::setUp();
    }
    
    protected function isRouteAvailable($actionName)
    {
        $this->routeMatch->setParam('action', $actionName);
        
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        
        $this->assertNotEquals(404, $response->getStatusCode());        
    }
    
    protected function mockLogin()
    {
        $userSessionModel = new UserSessionModel();
        $userSessionModel->setUserId(1);
        $userSessionModel->setName('Tester');
        
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $authService->expects($this->any())
                    ->method('getIdentity')
                    ->will($this->returnValue($userSessionModel));
        
        $authService->expects($this->any())
                    ->method('hasIdentity')
                    ->will($this->returnValue(true));
        
        $this->getApplicationServiceLocator()->setAllowOverride(true);
        $this->getApplicationServiceLocator()->setService('Zend\Authentication\AuthenticationService', $authService);

        $serviceLocator = $this->controller->getServiceLocator();
        $serviceLocator->setAllowOverride(true);
        $serviceLocator->setService('Zend\Authentication\AuthenticationService', $authService);
    }
    
    protected function updateConfig($configParts)
    {
        $this->controller->getServiceLocator()->setAllowOverride(true);
        
        $this->controller->getServiceLocator()->setService('config', 
            array_merge(
                $this->controller->getServiceLocator()->get('config'),
                $configParts
            )
        );
    }
 }
