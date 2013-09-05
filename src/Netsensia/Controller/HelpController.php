<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Help for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Netsensia\Controller;

use Netsensia\Form\ContactForm;

class HelpController extends NetsensiaActionController
{
    public function __construct()
    {       
    }
    
    public function indexAction()
    {
        return array();
    }
    
    public function contactAction()
    {
        $form = new ContactForm(
            'contact', 
            [
                'showCaptcha' => $this->isCaptchaForm('contact'),
                'captchaConfig' => $this->getCaptchaConfig(),
            ]
        );
        $form->setTranslator($this->getServiceLocator()->get('translator'));
        $form->prepare();
        
        $form->setAttribute('action', $this->getEvent()->getRouteMatch()->getParam('contact'));
        
        $form->setAttribute('method', 'post');        
        
        $request = $this->getRequest();
        
        $feedbackId = null;
        $feedbackCode = null;
    
        if ($request->isPost()) {
            
            $feedback = $this->newModel('Feedback');
            
            $inputFilter = $feedback->getInputFilter();
            $form->setInputFilter($inputFilter);
            $data = $request->getPost();
            $form->setData($data);
                
            if ($form->isValid()) {
                
                $date = date('Y/m/d H:i:s');
                
                $feedback->setData(
                    array(
                        'email' => $data['email'],
                        'message' => $data['message'],
                        'created' => $date,
                        'status' => 'N',
                    )
                );
                
                $feedbackId = $feedback->create();
                $feedbackCode = md5($data['email'] . $data['message']);
                    
                $this->sendMail(
                    $this->translate('Thanks for your feedback'), 
                    $data['email'], 
                    'netsensia/email/template/feedback-thanks',
                    ['feedback_id' => $feedbackId, 'feedback_code' => $feedbackCode]
                );
                
                $this->flashMessenger()->addSuccessMessage(
                    $this->translate('Your message was sent successfully and should reach us shortly')
                );
                
                $this->redirect()->toRoute('home');
            }
        }
        
        return array(
            "form" => $form,
            "feedback_id" => $feedbackId,
            "feedback_code" => $feedbackCode,
        );
    }
}
