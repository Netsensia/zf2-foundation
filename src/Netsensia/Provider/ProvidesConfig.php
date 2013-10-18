<?php
namespace Netsensia\Provider;

trait ProvidesConfig
{
    protected function getConfig()
    {
        return $this->getServiceLocator()->get('config');
    }
    
    protected function isCaptchaForm($formName)
    {
        return
            isset($this->getConfig()['netsensia']['captchaForms'][$formName]) &&
            $this->getConfig()['netsensia']['captchaForms'][$formName];
    }
    
    protected function getCaptchaConfig()
    {
        return $this->getConfig()['captcha'];
    }    
}

?>