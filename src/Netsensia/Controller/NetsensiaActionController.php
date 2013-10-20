<?php
namespace Netsensia\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Netsensia\Provider\ProvidesConnection;
use Netsensia\Provider\ProvidesUserInfo;
use Netsensia\Provider\ProvidesModels;
use Netsensia\Provider\ProvidesConfig;
use Netsensia\Provider\ProvidesTranslator;
use Netsensia\Provider\ProvidesHttpUtils;
use Netsensia\Provider\ProvidesEmail;

class NetsensiaActionController extends AbstractActionController
{
    use ProvidesConnection, 
        ProvidesUserInfo, 
        ProvidesModels, 
        ProvidesConfig, 
        ProvidesTranslator,
        ProvidesEmail, 
        ProvidesHttpUtils;
    
    protected function getUserService()
    {
        $userService = $this->getServiceLocator()->get('Netsensia\Service\UserService');
        return $userService;    
    }
    
    public function getFlashMessages()
    {
        $messages = [];
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
            foreach ($flashMessenger->getMessages() as $message) {
                $messages[] = $message;
            }
        }
        if ($flashMessenger->hasSuccessMessages()) {
            foreach ($flashMessenger->getSuccessMessages() as $message) {
                $messages[] = $message . '##success';
            }
        }
        if ($flashMessenger->hasInfoMessages()) {
            foreach ($flashMessenger->getInfoMessages() as $message) {
                $messages[] = $message . '##info';
            }
        }
        if ($flashMessenger->hasErrorMessages()) {
            foreach ($flashMessenger->getErrorMessages() as $message) {
                $messages[] = $message . '##danger';
            }
        }
        return $messages;
    }
}
