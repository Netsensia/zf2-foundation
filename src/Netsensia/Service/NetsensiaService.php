<?php
namespace Netsensia\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Netsensia\Provider\ProvidesServiceLocator;
use Netsensia\Provider\ProvidesConnection;
use Netsensia\Provider\ProvidesUserInfo;
use Netsensia\Provider\ProvidesModels;
use Netsensia\Provider\ProvidesTranslator;
use Netsensia\Provider\ProvidesEmail;

class NetsensiaService implements ServiceLocatorAwareInterface 
{
    use ProvidesServiceLocator, 
        ProvidesConnection, 
        ProvidesUserInfo,
        ProvidesEmail,
        ProvidesTranslator,
        ProvidesModels;
}
