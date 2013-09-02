<?php
namespace Netsensia\Provider;

use Zend\ServiceManager\ServiceLocatorInterface;

trait ProvidesServiceLocator
{
    protected $serviceLocator;
    
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
}

?>