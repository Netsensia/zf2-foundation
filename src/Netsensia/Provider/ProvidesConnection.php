<?php
namespace Netsensia\Provider;

trait ProvidesConnection
{
    protected function getConnection()
    {
        $pdoConnectionService = $this->getServiceLocator()->get('Netsensia\Service\PdoConnectionService');
        $connection = $pdoConnectionService->getConnection();

        return $connection;
    }
    
    protected function getPreparedQuery($sql)
    {
        $connection = $this->getConnection();
        $query = $connection->prepare($sql);

        return $query;
    }
    
    protected function getLastInsertedId()
    {
    	return $this->getConnection()->getLastInsertedId();
    }
}

?>