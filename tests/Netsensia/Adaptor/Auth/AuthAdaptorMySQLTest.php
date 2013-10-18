<?php

use Netsensia\Adaptor\Auth\AuthAdaptorMySQL;
use Netsensia\Test\DatabaseInitializer;
use TestSuite\Bootstrap;
use Zend\Authentication\Result;

/**
 * AuthAdaptorMySQL test case.
 */
class AuthAdaptorMySQLTest extends PHPUnit_Framework_TestCase 
{

    /**
     * @var AuthAdaptorMySQL
     */
    private $AuthAdaptorMySQL;
    
    /**
     * @var ServiceLocator $serviceManager
     */
    private $serviceManager;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp() {
        parent::setUp();

        $this->AuthAdaptorMySQL = new AuthAdaptorMySQL(/* parameters */);
        $this->serviceManager = Bootstrap::getServiceManager();
        $this->AuthAdaptorMySQL->setServiceLocator($this->serviceManager);
        $this->assertTrue($this->AuthAdaptorMySQL->getServiceLocator() == $this->serviceManager);
        
        Bootstrap::initDatabase();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown() {
        // TODO Auto-generated AuthAdaptorMySQLTest::tearDown()

        $this->AuthAdaptorMySQL = null;

        parent::tearDown();
    }

    /**
     * Constructs the test case.
     */
    public function __construct() {
        // TODO Auto-generated constructor
    }
    
    public function testAuthenticateSuccess() {
        $this->AuthAdaptorMySQL->setCredentials('test@netsensia.com', 'testpassword');

        $result = $this->AuthAdaptorMySQL->authenticate();
        
        $this->assertTrue($result->getCode() == Result::SUCCESS);
    }
    
    public function testAuthenticateFailure() {
        $this->AuthAdaptorMySQL->setCredentials('test@netsensia.com', 'wrongpassword');
    
        $result = $this->AuthAdaptorMySQL->authenticate();
    
        $this->assertTrue($result->getCode() == Result::FAILURE);
    }

}

