<?php
namespace Netsensia\Provider;

use Zend\Uri\Uri;

trait ProvidesHttpUtils
{
    protected function getDomain($uriString = null)
    {
        if ($uriString == null) {
            $uriString = $this->getRequest()->getUri();
        }
        
        $uri = new Uri($uriString);
        
        return $uri->getHost();
    }
    
    protected function sameDomain($uri1, $uri2 = null)
    {
        return $this->getDomain($uri1) == $this->getDomain($uri2);
    }
}

?>