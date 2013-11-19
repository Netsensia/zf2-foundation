<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Netsensia for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Netsensia;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Mvc\ModuleRouteListener;
use Zend\Log\Writer\FirePhp;
use Zend\Log\Writer\FirePhp\FirePhpBridge;
use Zend\Log\Writer\Stream;
use Zend\Log\Logger;
    

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    public function getViewHelperConfig()   
    { 
        return array(
            'invokables' => array(
                'BootstrapForm' => 'Netsensia\Form\View\Helper\BootstrapForm',
            ),
            'factories' => array(
                'config' => function($serviceManager) {
                    $helper = new \Netsensia\View\Helper\Config($serviceManager);
                    return $helper;
                },
            )
        );
    }
    
    public function getServiceConfig()
    {
        return [
            'factories' => array(
                'Zend\Log' => function($sm) {
                    $log = new Logger();
 
                    $firephp_writer = new FirePhp(new FirePhpBridge(\FirePHP::getInstance(true)));
                    $log->addWriter($firephp_writer);
     
                    $stream_writer = new Stream('./data/log/application.log');
                    $log->addWriter($stream_writer);
     
                    $log->info('FirePHP logging enabled');
     
                    return $log;
                },
                'UserModel' => function (\Zend\ServiceManager\ServiceLocatorInterface $sl) {
                    $instance = new \Application\Model\User();
                    $instance->setServiceLocator($sl);
                    
                    $instance->setRelation('addressid', 'address');
                    
                    return $instance;
                },
                'FeedbackModel' => function (\Zend\ServiceManager\ServiceLocatorInterface $sl) {
                    $instance = new \Netsensia\Model\Feedback();
                    $instance->setServiceLocator($sl);
                    return $instance;
                },
                'AddressModel' => function (\Zend\ServiceManager\ServiceLocatorInterface $sl) {
                	$instance = new \Netsensia\Model\Address();
                	$instance->setServiceLocator($sl);
                	return $instance;
                },
            ),
        ];        
    }

    public function onBootstrap($e)
    {
        // You may not need to do this if you're doing it elsewhere in your
        // application
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
}
