<?php

use Netsensia\Test\NetsensiaTest;
use TestSuite\Bootstrap;

class ProvidesUserInfoTest extends NetsensiaTest
{
    use Netsensia\Provider\ProvidesUserInfo;
    
    public function testCanGetUserName()
    {
        $this->setServiceLocator(Bootstrap::getServiceManager());
        
        $username = "Test User";
        $this->mockLogin($username);
        $this->assertEquals($username, $this->getUsername());
        
        $this->assertEquals(
            $username,
            $this->getUserSessionModel()->getName()
        );
    }
}

