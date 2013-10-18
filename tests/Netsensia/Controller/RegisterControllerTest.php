<?php
namespace Tests\Netsensia\Controller;

use PHPUnit_Framework_TestCase;
use Netsensia\Test\NetsensiaControllerTest;
use Netsensia\Controller\RegisterController;
use TestSuite\Bootstrap;
use Zend\Stdlib\Parameters;

/**
 * @SuppressWarnings(PHPMD)
 */
class RegisterControllerTest extends NetsensiaControllerTest
{
    public function setup()
    {
        $this->setController(new RegisterController(), 'register');
        parent::setUp();
    }
    
    public function testRoutesAreAvailable()
    {
        $this->isRouteAvailable('index');
        $this->isRouteAvailable('newUser');
        $this->isRouteAvailable('passwordReset');
        $this->isRouteAvailable('updatePassword');
        $this->isRouteAvailable('validateEmail');
    }
    
    public function testNotPossibleToRegisterWhenLoggedIn()
    {
        $this->mockLogin();
        $this->dispatch('/register');
        $this->assertRedirectTo('/');        
    }
    
    public function testCanRenderRegisterForm()
    {
        $this->routeMatch->setParam('action', 'index');
        
        $this->request->setMethod('GET');
        
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCanPostToRegisterFormWithNoEmailVerification()
    {
        $this->updateConfig(
            ['registration' => ['requireEmailVerification' => false]]
        );
                
        $this->routeMatch->setParam('action', 'index');

        $response = $this->postToRegisterForm(
            'tester1' . time(),
            'tester1' . time() . '@netsensia.com'
        );
        
        $uri = $response->getHeaders()->get('Location')->getUri();
        
        $this->assertContains(
            $this->controller->url()->fromRoute('validate-email'),
            $uri
        );
        
        $this->dispatch($uri);
        
        $redirectContent = $this->getResponse()->getContent();
        
        $this->assertContains(
            '<h1>Account activated</h1>',
            $redirectContent
        );
    }
    
    public function testCanPostToRegisterFormWithEmailVerification()
    {
        $this->updateConfig(
            ['registration' => ['requireEmailVerification' => true]]
        );

        $this->routeMatch->setParam('action', 'index');
    
        $response = $this->postToRegisterForm(
            'tester2' . time(),
            'tester2' . time() . '@netsensia.com'
        );
                
        $this->assertContains(
            $this->controller->url()->fromRoute('new-user'),
            $response->getHeaders()->get('Location')->getUri()
        );
    }
    
    public function testRegistrationRejectedWhenEmailExists()
    {
        $this->updateConfig(
            ['registration' => ['requireEmailVerification' => false]]
        );
                
        $this->routeMatch->setParam('action', 'index');

        $username = 'tester3' . time();
        $email = 'tester3' . time() . '@netsensia.com';
        
        $response = $this->postToRegisterForm(
            $username,
            $email
        );
        
        $uri = $response->getHeaders()->get('Location')->getUri();
        
        $this->assertContains(
            $this->controller->url()->fromRoute('validate-email'),
            $uri
        );
        
        $this->dispatch($uri);
        
        $redirectContent = $this->getResponse()->getContent();
        
        $this->assertContains(
            '<h1>Account activated</h1>',
            $redirectContent
        );
        
        $response = $this->postToRegisterForm(
            'new' . $username,
            $email
        );
        
        $this->assertEquals(200, $response->getStatusCode());
   }
   
   public function testRegistrationRejectedWhenUsernameExists()
   {
       $this->updateConfig(
           ['registration' => ['requireEmailVerification' => false]]
       );
   
       $this->routeMatch->setParam('action', 'index');
   
       $username = 'tester3' . time();
       $email = 'tester3' . time() . '@netsensia.com';
   
       $response = $this->postToRegisterForm(
           $username,
           $email
       );
   
       $uri = $response->getHeaders()->get('Location')->getUri();
   
       $this->assertContains(
           $this->controller->url()->fromRoute('validate-email'),
           $uri
       );
   
       $this->dispatch($uri);
   
       $redirectContent = $this->getResponse()->getContent();
   
       $this->assertContains(
           '<h1>Account activated</h1>',
           $redirectContent
       );
   
       $response = $this->postToRegisterForm(
           $username,
           'new' . $email
       );
   
       $this->assertEquals(200, $response->getStatusCode());
   }
    
   public function testPostingNoEmailToPasswordResetFormReturnsForm()
   {
        $this->routeMatch->setParam('action', 'password-reset');
        
        $this->request->setMethod('POST');
        
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        
        $this->assertEquals(200, $response->getStatusCode());
    }
    
    public function testPostingInvalidEmailToPasswordResetFormReturnsForm()
    {
        $this->getRequest()->setMethod('POST');
        $this->getRequest()->setPost(new Parameters(array('passwordreset-email' => 'iamnotanemailaddress')));
        
        $this->dispatch('/password-reset');
        $content = $this->getResponse()->getContent();
        
        $this->assertContains('Email address format is invalid', $content);
       
    }    
    
    public function testPostingValidDataToPasswordResetFormRedirects()
    {
        $userService = 
                $this->getMockBuilder('Netsensia\Service\UserService')
                     ->disableOriginalConstructor()
                     ->getMock();
    
        $userService->expects($this->any())
                    ->method('getUserIdFromEmail')
                    ->withAnyParameters()
                    ->will($this->returnValue(1));
    
        $userService->expects($this->any())
                    ->method('processPasswordResetRequest')
                    ->withAnyParameters()
                    ->will($this->returnValue(null));
    
        $serviceLocator = Bootstrap::getServiceManager();
        $serviceLocator->setService('Netsensia\Service\UserService', $userService);
        
        $this->routeMatch->setParam('action', 'password-reset');
        
        $this->request->setMethod('POST');
        $this->request->getPost()->set('passwordreset-email', 'test@netsensia.com');
        
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        
        $this->assertEquals(302, $response->getStatusCode());
    }    
     
    public function testCanPostToUpdatePasswordForm()
    {
        $mockUserId = 1;
        $mockPassword = 'testpassword';
        $mockEncryptedPassword = 'encpassword';
        $mockPasswordResetCode = 'testpasswordcode';
        
        $userService = 
                $this->getMockBuilder('Netsensia\Service\UserService')
                     ->disableOriginalConstructor()
                     ->getMock();
    
        $userService->expects($this->any())
                    ->method('getUserIdFromPasswordResetCode')
                    ->with($this->equalTo($mockPasswordResetCode))
                    ->will($this->returnValue($mockUserId));
    
        $userService->expects($this->any())
                    ->method('encryptPassword')
                    ->with($this->equalTo($mockPassword))
                    ->will($this->returnValue($mockEncryptedPassword));
        
        $userModel = $this->getMock('Netsensia\Model\User');
        
        $userModel->expects($this->once())
                  ->method('init')
                  ->with($this->equalTo($mockUserId))
                  ->will($this->returnValue($userModel));

        $map = array(
            array('passwordresetcode', 0, null),
            array('password', $mockEncryptedPassword, null)
        );
        
        $userModel->expects($this->exactly(2))
                  ->method('set')
                  ->will($this->returnValueMap($map));
        
        $userModel->expects($this->once())
                  ->method('save');
    
        $serviceLocator = Bootstrap::getServiceManager();
        $serviceLocator->setService('Netsensia\Service\UserService', $userService);
        $serviceLocator->setService('UserModel', $userModel);
        
        $this->routeMatch->setParam('password-reset-code', $mockPasswordResetCode);
        $this->routeMatch->setParam('action', 'update-password');
        
        $this->request->setMethod('POST');
        $this->request->getPost()->set('password', 'testpassword');
        $this->request->getPost()->set('confirmpassword', 'testpassword');
        
        $this->controller->setServiceLocator($serviceLocator);
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();
        
        $this->assertEquals(302, $response->getStatusCode());
    }    
    
    private function postToRegisterForm($username, $email)
    {
        $this->request->setMethod('POST');
        $this->request->getPost()->set('username', $username);
        $this->request->getPost()->set('email', $email);
        $this->request->getPost()->set('password', 'password');
        $this->request->getPost()->set('confirmpassword', 'password');
        
        $this->controller->dispatch($this->request);
        $response = $this->controller->getResponse();

        return $response;
    }
 }
