<?php

use Netsensia\Test\NetsensiaTest;
use TestSuite\Bootstrap;

class FeedbackModelTest extends NetsensiaTest 
{
    public function testCanSaveAndLoadFeedback()
    {
        $feedback = Bootstrap::getServiceManager()->get('FeedbackModel')->init();
        $feedback->setData([
            "email" => "test@netsensia.com",
            "message" => "Test message",
        ]);
        
        $feedbackSavedId = $feedback->create();
        
        $sm = Bootstrap::getServiceManager();
        $feedbackLoaded = $sm->get('FeedbackModel');
        $feedbackLoaded->init($feedbackSavedId);

        $loadedId = $feedbackLoaded->getId();
        
        $this->assertEquals($feedbackSavedId, $loadedId);
    }
}

