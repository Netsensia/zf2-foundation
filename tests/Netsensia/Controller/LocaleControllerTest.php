<?php
namespace Tests\Netsensia\Controller;

use Zend\Http\Request;
use Netsensia\Test\NetsensiaControllerTest;
use Netsensia\Controller\LocaleController;
use Zend\Session\Container as SessionContainer;

class LocaleControllerTest extends NetsensiaControllerTest
{
    public function setUp()
    {
        $this->setController(new LocaleController(), 'locale');
        parent::setUp();
    }
    
    public function testRoutesAreAvailable()
    {
        $this->isRouteAvailable('set');
    }
    
    public function testCanSwitchLocaleToEnGb()
    {
        $this->dispatch('/locale/en_GB');

        $this->assertRedirect();
        
        $sessionContainer = new SessionContainer('locale');
        $this->assertEquals('en_GB', $sessionContainer->locale);
    }
    
    public function testCanSwitchLocaleToFrFr()
    {
        $this->dispatch('/locale/fr_FR');
        
        $this->assertRedirect();
    
        $sessionContainer = new SessionContainer('locale');
        $this->assertEquals('fr_FR', $sessionContainer->locale);
    }
    
    public function testCanSwitchLocaleWhenLoggedIn()
    {
        $this->mockLogin();
        $this->dispatch('/locale/fr_FR');
        $this->assertRedirectTo('/');
    }
    
    public function testRedirectToRefererOnLocaleChangeIfSameDomainReferer()
    {
        $referer = 'http://test.local/someroute';
        $this->getRequest()->getHeaders()->addHeaders(
            ['referer' => $referer]
        );
        
        $this->dispatch('http://test.local/locale/fr_FR');
        
        $this->assertRedirectTo($referer);
    }
    
    public function testRedirectHomeOnLocaleChangeIfNotSameDomainReferer()
    {
        $referer = 'http://www.google.co.uk';
        $this->getRequest()->getHeaders()->addHeaders(
            ['referer' => $referer]
        );
    
        $this->dispatch('http://test.local/locale/fr_FR');
    
        $this->assertRedirectTo('/');
    }    
 }
