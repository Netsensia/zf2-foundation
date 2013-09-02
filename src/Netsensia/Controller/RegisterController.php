<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/User for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Netsensia\Controller;

use Zend\InputFilter\Factory as InputFactory;
use Netsensia\Form\RegisterForm;
use Zend\Mvc\MvcEvent;
use Zend\Validator\NotEmpty;
use Zend\Validator\Identical;
use Netsensia\Form\PasswordResetForm;
use Netsensia\Form\UpdatePasswordForm;
use Zend\InputFilter\InputFilter;
use Zend\Validator\EmailAddress;
use Zend\Math\Rand;

class RegisterController extends NetsensiaActionController
{
    public function onDispatch(MvcEvent $mvcEvent)
    {
        $renderer = $this->serviceLocator->get('Zend\View\Renderer\PhpRenderer');
        $jsFile = 'pwstrength.js';
        $baseUrl = $mvcEvent->getRouter()->getBaseUrl();
        $renderer->headScript()->appendFile($baseUrl . '/js/' . $jsFile);
    
        return parent::onDispatch($mvcEvent);
    }
      
    public function indexAction()
    {
        if ($this->isLoggedOn()) {
            $this->redirect()->toRoute('home');
        }
        
        $form = new RegisterForm(
            'register', 
            [
                'showCaptcha' => $this->isCaptchaForm('register'),
                'captchaConfig' => $this->getCaptchaConfig(),
            ]
        );
        
        $form->setTranslator($this->getServiceLocator()->get('translator'));
        
        $form->prepare();
    
        $request = $this->getRequest();
    
        if ($request->isPost()) {
            
            $data = $request->getPost();
            $form->setData($data);
                        
            $user = $this->newModel('User');
            
            $inputFilter = $user->getInputFilter();
            $inputFilter->add($this->getPasswordFilter());
            $inputFilter->add($this->getConfirmPasswordFilter());
            $form->setInputFilter($inputFilter);
            
            $locationService = $this->getServiceLocator()->get('LocationService');
            $remoteAddress = $locationService->getRemoteAddress();
            $isoCode = $locationService->getIsoCodeFromIpAddress($remoteAddress);
            
            if ($form->isValid()) {
                
                $password = $this->getUserService()->encryptPassword($data['password']);
                
                $activationCode = $this->getUserService()->generateActivationCode(); 

                $user->setData(
                    array(
                        'email' => $data['email'],
                        'name' => $data['username'],
                        'isocountry_fromregip' => $isoCode,
                        'createddate' => date('Y/m/d H:i:s'),
                        'password' => $password,
                        'regipaddress' => $remoteAddress,
                        'emailverifycode' => $activationCode,
                        'activated' => 'N',
                    )
                );
                
                if ($user->isEmailTaken($data['email'])) {
                    $form->get('email')->setMessages(
                        array($this->translate('The email address is already registered'))
                    );
                } elseif ($user->isUsernameTaken($data['username'])) {
                    $form->get('username')->setMessages(
                        array($this->translate('The username is already registered'))
                    );
                } else {
                    $userId = $user->create();
                    $this->getUserService()->insertDefaultValues($userId);
                    
                    $registrationConfig = $this->getServiceLocator()->get('config')['registration'];
                    
                    if ($registrationConfig['requireEmailVerification']) {
                        $this->getUserService()->sendActivationEmail($userId);
                        return $this->redirect()->toRoute('new-user');
                    } else {
                        return $this->redirect()->toRoute('validate-email', array('code'=>$activationCode));
                    }
                }
            }
        }
    
        return array(
            "form" => $form
        );
    }

    public function validateEmailAction()
    {
        $code = $this->getEvent()->getRouteMatch()->getParam('code');
    
        $userService = $this->getUserService();
    
        return ['activated' => $userService->activateAccount($code)];
    }
    
    public function newUserAction()
    {
    }
    
    public function updatePasswordAction()
    {

        $passwordResetCode = $this->getEvent()->getRouteMatch()->getParam('password-reset-code');
        
        if (empty($passwordResetCode)) {
            $this->redirect()->toRoute('home');
        }
    
        $userId = $this->getUserService()->getUserIdFromPasswordResetCode($passwordResetCode);
        
        if (!$userId) {
            $this->flashMessenger()->addErrorMessage(
                $this->translate('Your password reset request has expired.  Please try again.')
            );
            $this->redirect()->toRoute('home');
        }
        
        $form = new UpdatePasswordForm('update-password');
        $form->setTranslator($this->getServiceLocator()->get('translator'));
        $form->prepare();
        
        $inputFilter = new InputFilter();
        $inputFilter->add($this->getPasswordFilter());
        $inputFilter->add($this->getConfirmPasswordFilter());
        
        $form->setInputFilter($inputFilter);
        
        $request = $this->getRequest();
        
        if ($request->isPost()) {
            $data = $request->getPost();
            $form->setData($data);
            
            if ($form->isValid()) {
                $password = $this->getUserService()->encryptPassword($data['password']);
                                
                $user = $this->loadModel('User', $userId);
                $user->set('passwordresetcode', '0');
                $user->set('password', $password);
                
                $user->save();
                
                $this->flashMessenger()->addSuccessMessage(
                    $this->translate('Your password was successfully updated.')
                );
                
                $this->redirect()->toRoute('home');
            }
        }
        
        return array(
            "form" => $form,
            "code" => $passwordResetCode,
        );
    }
    
    public function passwordResetAction()
    {
        $form = new PasswordResetForm();
        $form->setTranslator($this->getServiceLocator()->get('translator'));
        $form->prepare();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $inputFilter = new InputFilter();
            
            $inputFilter->add($this->getEmailFilter());
            
            $form->setInputFilter($inputFilter);
            
            $data = $request->getPost();
            $form->setData($data);
            
            if ($form->isValid()) {
                $userService = $this->getUserService();
                $userId = $userService->getUserIdFromEmail(
                    $data['passwordreset-email']
                );
                
                if ($userId != null) {
                    $passwordResetCode = $userService->processPasswordResetRequest($userId);
                }
                
                $this->flashMessenger()->addInfoMessage(
                    $this->translate(
                        'Please check your email.  ' .
                        'If you have an account with us, ' .
                        'we have sent you instructions on ' .
                        'how to reset your password.'
                    )
                );
                
                if ($this->getServiceLocator()->get('config')['netsensia']['debugMode']) {
                    $this->flashMessenger()->addInfoMessage(
                        $this->translate(
                            '<a href="http://netsensia.zf2.skeleton/update-password/' . $passwordResetCode . '">' .
                            'Here is the link' .
                            '</a> - seeings as this is only an example site.'
                        )
                    );
                }
            
                $this->redirect()->toRoute('login');
            }
        }
         
        return array(
            "form" => $form,
        );        
    }
    
    private function getEmailFilter()
    {
        $inputFactory = new InputFactory();
    
        return $inputFactory->createInput(
            [
                'name' => 'passwordreset-email',
                'required' => true,
                'filters' => [
                    ['name' => 'StripTags'],
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => 'NotEmpty',
                        'options' => array(
                            'messages' => array(
                                NotEmpty::IS_EMPTY => 'Email is required',
                            )
                        ),
                        'break_chain_on_failure' => true,
                    ],
                    [
                        'name' => 'EmailAddress',
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 5,
                            'max'      => 255,
                            'messages' => array(
                                EmailAddress::INVALID_FORMAT => 'Email address format is invalid'
                            ),
                        ],
                    ],
                ],
            ]
        );
    }
    
    private function getPasswordFilter()
    {
        $inputFactory = new InputFactory();
        
        return $inputFactory->createInput(
            [
                'name'     => 'password',
                'required' => true,
                'validators' => array(
                    [
                    'name'    => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => $this->translate('Please choose a password'),
                        )
                    ),
                    'break_chain_on_failure' => true,
                    ],
                ),
            ]
        );
    }
    
    private function getConfirmPasswordFilter()
    {
        $inputFactory = new InputFactory();
        
        return $inputFactory->createInput(
            [
                'name'     => 'confirmpassword',
                'required' => true,
                'validators' => array(
                    [
                    'name'    => 'NotEmpty',
                    'options' => array(
                        'messages' => array(
                            NotEmpty::IS_EMPTY => $this->translate('Please confirm your password'),
                        )
                    ),
                    'break_chain_on_failure' => true,
                    ],
                    array(
                        'name'    => 'Identical',
                        'options' => array(
                            'token' => 'password',
                            'messages' => array(
                                Identical::NOT_SAME => $this->translate("Passwords don't match"),
                            )
                        ),
                    ),
                ),
            ]
        );
    }
}
