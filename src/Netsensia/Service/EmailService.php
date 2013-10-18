<?php
namespace Netsensia\Service;

use Zend\Mail\Message as MailMessage;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Netsensia\Service\NetsensiaService;

class EmailService extends NetsensiaService
{
    private $options;
    private $senderName;
    private $senderAddress;
    
    public function __construct(
        $smtpHostName,
        $smtpHost,
        $smtpPort,
        $smtpUsername,
        $smtpPassword,
        $senderName,
        $senderAddress
    )
    {
        $this->options = new SmtpOptions(array(
            'name' => $smtpHostName,
            'host' => $smtpHost,
            'port' => $smtpPort,
            'connection_class' => 'login',
            'connection_config' => array(
                'username' => $smtpUsername,
                'password' => $smtpPassword,
                'ssl'=> 'tls',
            ),
        ));

        $this->senderName = $senderName;
        $this->senderAddress = $senderAddress;

    }
    
    public function send($subject, $to, $template, $templateVars = null)
    {
        $sl = $this->getServiceLocator();
        
        // If there's no ViewRenderer, we could be in a headless test
        if ($sl->has('ViewRenderer')) {
            // @codeCoverageIgnoreStart
            $renderer = $this->getServiceLocator()->get('ViewRenderer');
            $content = $renderer->render($template, $templateVars);
             
            $html = new MimePart($content);
            $html->type = "text/html";
            $body = new MimeMessage();
            $body->setParts(array($html));
            
            $mail = new MailMessage();
            $mail->setBody($body); // will generate our code html from template.phtml
            $mail->setFrom($this->senderAddress, $this->senderName);
            $mail->setTo($to);
            $mail->setSubject($subject);
            
            $transport = new SmtpTransport($this->options);
            $transport->send($mail);
            // @codeCoverageIgnoreStop
        }
    }
}
