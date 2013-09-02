<?php
/**
 * @author Chris Moreton
 */
namespace Netsensia\Controller;

use Netsensia\Form\LoginForm;

class AuthController extends NetsensiaActionController
{

    /**
     * Handle login, redirect on POST, otherwise provide form and any messages
     * @return array  
     */
    public function loginAction()
    {
        $authService = $this->getServiceLocator()->get("AuthenticationService");
        $authAdaptor = $this->getServiceLocator()->get("AuthAdaptorMySQL");
        
        if ($this->getRequest()->isPost()) {
            $authAdaptor->setCredentials(
                $this->params()->fromPost('login-email'),
                $this->params()->fromPost('login-password')
            );
            
            $result = $authService->authenticate($authAdaptor);
            
            // @codeCoverageIgnoreStart
            if (!headers_sent()) {
                if ($this->params()->fromPost('remember') != '') {
                    $this->getServiceLocator()
                         ->get("Zend\Session\SessionManager")
                         ->rememberMe(2419200);
                } else {
                    $this->getServiceLocator()
                         ->get("Zend\Session\SessionManager")
                         ->forgetMe();
                }
                
            }
            // @codeCoverageIgnoreEnd
            
            if (!$result->isValid()) {
                foreach ($result->getMessages() as $message) {
                    $this->flashMessenger()->addErrorMessage(
                        $message
                    );
                }
                $this->redirect()->toRoute('login');
            } else {
                $this->flashMessenger()->addSuccessMessage(
                    $this->translate('You are now logged in')
                );
                $this->redirect()->toRoute('home');
            }        
        }
        
        $form = new LoginForm();
        $form->setTranslator($this->getServiceLocator()->get('translator'));
        $form->prepare();
     
        return array(
            "form" => $form,
            'flashMessages' => $this->getFlashMessages(),
        );
    }
    

    /**
     * Clear user identity
     */
    public function logoutAction()
    {
        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        $authService->clearIdentity();
        $this->flashMessenger()->addInfoMessage(
            $this->translate('You have been logged out')
        );
        $this->redirect()->toRoute('home');
    }
}
