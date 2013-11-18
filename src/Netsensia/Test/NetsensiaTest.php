<?php
namespace Netsensia\Test;

use \PHPUnit_Framework_TestCase;
use Netsensia\Adaptor\Auth\UserSessionModel;

class NetsensiaTest extends PHPUnit_Framework_TestCase
{
    private $serviceLocator;
    
    public function setUp()
    {
        parent::setUp();
    }
   
    protected function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    protected function getServiceLocator()
    {
        return $this->serviceLocator;
    }
        
    protected function mockLogin($username)
    {
        $userSessionModel = new UserSessionModel();
        $userSessionModel->setUserId(1);
        $userSessionModel->setName($username);
    
        $authService = $this->getMock('Zend\Authentication\AuthenticationService');
        $authService->expects($this->any())
            ->method('getIdentity')
            ->will($this->returnValue($userSessionModel));
    
        $authService->expects($this->any())
            ->method('hasIdentity')
            ->will($this->returnValue(true));
        
        $this->getServiceLocator()->setAllowOverride(true);
        $this->getServiceLocator()->setService('Zend\Authentication\AuthenticationService', $authService);
    }

    protected function getAdapterMock()
    {
        $adapter = \Mockery::mock('Zend\Db\Adapter\Adapter');
        $platform = \Mockery::mock('Zend\Db\Adapter\Platform\Mysql[getName]');
        $stmt = \Mockery::mock('Zend\Db\Adapter\Driver\Pdo\Statement');
        $paramContainer = \Mockery::mock('Zend\Db\Adapter\ParameterContainer');
    
        $platform->shouldReceive('getName')
            ->once()
            ->andReturn('MySQL');
    
        $stmt->shouldReceive('getParameterContainer')
            ->once()
            ->andReturn($paramContainer);
    
        $stmt->shouldReceive('setSql')
            ->once()
            ->andReturn($stmt);
    
        $stmt->shouldReceive('execute')
            ->once()
            ->andReturn(array());
    
        $adapter->shouldReceive('getPlatform')
            ->once()
            ->andReturn($platform);
    
        $adapter->shouldReceive('createStatement')
            ->once()
            ->andReturn($stmt);
    
        return $adapter;
    }
}
