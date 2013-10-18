<?php
namespace Netsensia\Provider;

trait ProvidesEmail
{
    protected function sendMail(
        $subject, 
        $to, 
        $template, 
        $templateVars = null
    )
    {
        $email = $this->getServiceLocator()->get('Netsensia\Service\EmailService');
        $email->send($subject, $to, $template, $templateVars);
    }
}

?>