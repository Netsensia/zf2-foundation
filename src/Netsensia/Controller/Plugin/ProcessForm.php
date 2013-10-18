<?php

namespace Netsensia\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ProcessForm extends AbstractPlugin
{
    public function __invoke(
        $formName,
        $modelName,
        $modelId
    ) 
    {

        $form = $this->controller->getServiceLocator()->get($formName);
        
        $form->prepare();
        
        $controller = $this->getController();
        
        $request = $controller->getRequest();
        
        $sl = $this->controller->getServiceLocator();
        $tableModel = $sl->get($modelName . 'Model');
        
        $tableModel->init($modelId);
        
        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
        
            $form->setData($formData);
        
            if ($form->isValid()) {
                $prefix = $form->getFieldPrefix();
                                
                $modelData = [];
                
                $isUsingAddress = false;
                
                foreach ($formData as $key => $value) {
                    if ($key != 'form-submit') {
                        $modelField = preg_replace('/^' . $prefix . '/', '', $key);
                        $modelData[$modelField] = $value;
                    }
                    
                    if ($modelField == 'addresslineone') {
                    	$isUsingAddress = true;
                    }
                }
                
                if ($isUsingAddress) {
                	$modelData = $this->saveAddress($modelData);
                }
                
                $data = array_merge(
                    $tableModel->getData(),
                    $modelData
                );
                
                if (isset($data['password'])) {
                    $userService = 
                        $this->controller->getServiceLocator()->get('Netsensia\Service\UserService');
                    
                    $data['password'] = $userService->encryptPassword($data['password']);
                    unset($data['confirmpassword']);
                }
                                
                $tableModel->setData($data);
        
                $tableModel->save();
            }
        
        } else {
            $form->setDataFromModel($tableModel);
        }        
        
        return $form;
        
    }
    
    private function saveAddress($data)
    {
    	$sl = $this->controller->getServiceLocator();
    	
    	$tableModel = $sl->get('AddressModel');
    	$tableModel->init();
    	
    	$data['address1'] 	= $data['addresslineone'];
    	$data['address2'] 	= $data['addresslinetwo'];
    	$data['town'] 		= $data['addresscity'];
    	$data['county'] 	= $data['addresscounty'];
    	$data['country'] 	= $data['addresscountry'];
    	$data['postcode']   = $data['addresspostcode'];
    	
    	$tableModel->setData($data);
    	$tableModel->save();
    	
    	$data['addressid'] = $tableModel->getLastInsertedId();
    	
    	unset($data['addresslineone']);
    	unset($data['addresslinetwo']);
    	unset($data['addresslinethree']);
    	unset($data['addresstown']);
    	unset($data['addresscounty']);
    	unset($data['addresscountry']);
    	unset($data['addresspostcode']);
    	    	
    	return $data;
    }
}

