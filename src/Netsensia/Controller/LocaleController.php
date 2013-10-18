<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/User for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Netsensia\Controller;

use Zend\Session\Container as SessionContainer;

class LocaleController extends NetsensiaActionController
{
    public function setAction()
    {
        $locale = $this->getEvent()->getRouteMatch()->getParam('locale');
        
        if ($this->isLoggedOn()) {
            $translator = $this->getServiceLocator()->get('translator');
            $translator->setLocale($locale);
            
            $user = $this->getUserModel();
            
            $user->set('locale', $locale);
            
            $user->save();
        } else {
            $translator = $this->getServiceLocator()->get('translator');
            $translator->setLocale($locale);
            
            $this->session = new SessionContainer('locale');
            $this->session->locale = $locale;
        }
        
        $referer = $this->getRequest()->getHeader('Referer');
        
        if ($referer) {
            $redirectUri = $this->getRequest()->getHeader('Referer')->getUri();
            
            if ($this->sameDomain($redirectUri)) {
                $this->redirect()->toUrl($redirectUri);
            }
        }
        
        $this->redirect()->toRoute('home');
    }
}
