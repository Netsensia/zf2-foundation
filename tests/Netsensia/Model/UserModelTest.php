<?php

use Netsensia\Test\NetsensiaTest;
use TestSuite\Bootstrap;

class UserModelTest extends NetsensiaTest 
{
    public function testCanGetUserIdFromShortcutMethod()
    {
        $userModel = Bootstrap::getServiceManager()->get('UserModel')->init();
        $userModel->setPrimaryKey(['userid' => 1]);
        
        $this->assertEquals(1, $userModel->getUserId());
    }
}

