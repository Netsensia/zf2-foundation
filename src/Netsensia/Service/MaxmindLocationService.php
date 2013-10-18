<?php
namespace Netsensia\Service;

use Netsensia\Service\NetsensiaService;
use Netsensia\Service\ServiceInterface\LocationInterface;
use GeoIP2\WebService\Client;
use Zend\Http\PhpEnvironment\Request;

class MaxmindLocationService extends NetsensiaService implements LocationInterface
{
    private $userId = '';
    private $licenseKey = '';
    
    public function __construct(
        $userId,
        $licenseKey
    )
    {
        $this->userId = $userId;
        $this->licenseKey = $licenseKey;
    }
    
    public function getRemoteAddress()
    {
    	$request = new Request();
    	$serverParams = $request->getServer();
    	
    	$remoteAddress = $serverParams->get('REMOTE_ADDR');
    	
        if ($remoteAddress == '') {
            $remoteAddress = '127.0.0.1';
        }
        
        return $remoteAddress;
    }
    
    public function getIsoCodeFromIpAddress($ipAddress)
    {
        if ($this->userId == '') {
            return null;
        }
        
        if ($ipAddress == '127.0.0.1') {
            return null;
        } 
        
        // @codeCoverageIgnoreStart
        $client = new Client($this->userId, $this->licenseKey);
        
        $model = $client->country($ipAddress);
        $isoCode = $model->country->isoCode;
        
        return $isoCode;
        // @codeCoverageIgnoreEnd
   }
}
