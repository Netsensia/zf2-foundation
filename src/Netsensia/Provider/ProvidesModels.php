<?php
namespace Netsensia\Provider;

trait ProvidesModels
{
    protected function newModel($modelName)
    {
        return $this->getServiceLocator()->get($modelName . 'Model')->init();
    }
    
    protected function loadModel($modelName, $id)
    {
        $sl = $this->getServiceLocator();
        $model = $sl->get($modelName . 'Model');

        return $model->init($id);
    }
    
}

?>