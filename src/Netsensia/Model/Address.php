<?php
namespace Netsensia\Model;

use PDO;

class Address extends DatabaseTableModel
{
    public function __construct()
    {
        $this->setTableName('address');
        
        parent::__construct();
  
    }
    
    public function init($addressId = null)
    {
        $this->setPrimaryKey(array("addressid" => $addressId));
        if ($addressId != null) {
            $this->load();
        }
        return $this;
    }
    
    public function getAddressId()
    {
        $primaryKey = $this->getPrimaryKey();
        return $primaryKey['userid'];
    }
}
