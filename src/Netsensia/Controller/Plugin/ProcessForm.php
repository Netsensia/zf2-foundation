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
                
                $addressColumns = [];
                
                foreach ($formData as $key => $value) {
                    if ($key != 'form-submit') {
                        $modelField = preg_replace('/^' . $prefix . '/', '', $key);
                        $modelData[$modelField] = $value;
                    }
                    
                    if (strpos($modelField, 'addresslineone') !== false) {
                        $addressColumns[] = str_replace('addresslineone', '', $modelField);
                    }
                }
                
                foreach ($addressColumns as $addressColumn) {
                	$modelData = $this->saveAddress(
                	    $modelData, 
                	    $addressColumn
                    );
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
    
    private function saveAddress(
        $modelData,
        $addressColumn
    )
    {
    	$sl = $this->controller->getServiceLocator();
    	
    	$addressModel = $sl->get('AddressModel');
    	$addressModel->init();
    	
    	$addressData['address1']   = $modelData[$addressColumn . 'addresslineone'];
    	$addressData['address2']   = $modelData[$addressColumn . 'addresslinetwo'];
    	$addressData['town'] 	   = $modelData[$addressColumn . 'addresstown'];
    	$addressData['county'] 	   = $modelData[$addressColumn . 'addresscounty'];
    	$addressData['countryid']  = $modelData[$addressColumn . 'addresscountryid'];
    	$addressData['postcode']   = $modelData[$addressColumn . 'addresspostcode'];
    	
    	$addressModel->setData($addressData);
    	$modelData[$addressColumn] = $addressModel->create();

    	unset($modelData[$addressColumn . 'addresslineone']);
    	unset($modelData[$addressColumn . 'addresslinetwo']);
    	unset($modelData[$addressColumn . 'addresslinethree']);
    	unset($modelData[$addressColumn . 'addresstown']);
    	unset($modelData[$addressColumn . 'addresscounty']);
    	unset($modelData[$addressColumn . 'addresscountryid']);
    	unset($modelData[$addressColumn . 'addresspostcode']);
    	    	
    	return $modelData;
    }
}

