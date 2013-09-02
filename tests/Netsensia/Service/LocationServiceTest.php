<?php

use TestSuite\Bootstrap;
use Netsensia\Test\NetsensiaTest;
use Netsensia\Service\MaxmindLocationService;
use Zend\Http\PhpEnvironment\Request;

class LocationServiceTest extends NetsensiaTest 
{
    private $locationService;
    
    public function setUp()
    {
        $serviceManager = Bootstrap::getServiceManager();
        
        $this->locationService = $serviceManager->get('LocationService');    
    }
    
    /**
     * @SuppressWarnings(PHPMD)
     */
    private function setRemoteAddress($remoteAddress)
    {
        $_SERVER['REMOTE_ADDR'] = $remoteAddress;
    }
    
    /**
     * @SuppressWarnings(PHPMD)
     */
    public function testGetRemoteAddress()
    {
        $localHost = '127.0.0.1';
        $remoteHost = '173.194.34.174';

        $this->setRemoteAddress($localHost);
        $this->assertEquals($localHost, $this->locationService->getRemoteAddress());
        
        $this->setRemoteAddress(null);
        $this->assertEquals($localHost, $this->locationService->getRemoteAddress());
        
        $this->setRemoteAddress($remoteHost);
        $this->assertEquals($remoteHost, $this->locationService->getRemoteAddress());
        
        unset($_SERVER['REMOTE_ADDR']);
        $this->assertTrue(!isset($_SERVER['REMOTE_ADDR']));
        $this->assertEquals($localHost, $this->locationService->getRemoteAddress());
    }
    
    public function testGetIsoCodeFromIpAddress()
    {
        $locationService = new MaxmindLocationService('', '');
                        
        $this->assertEquals(null, $locationService->getIsoCodeFromIpAddress('127.0.0.1'));
    }
}

