<?php

namespace Netsensia\Service;

use PDO;

class PdoConnectionService
{
    private $db = null;
    
    ### CONSTRUCTOR

    public function __construct(
        $dataSourceName,
        $dataSourceUsername = null,
        $dataSourcePassword = null
    )
    {
        $this->dataSourceName = $dataSourceName;
        $this->dataSourceUsername = $dataSourceUsername;
        $this->dataSourcePassword = $dataSourcePassword;
    }

    ### PUBLIC METHODS

    public function getConnection()
    {
        if (!$this->db) {
            
            try {
                $this->db = new PDO(
                    $this->dataSourceName,
                    $this->dataSourceUsername,
                    $this->dataSourcePassword,
                    array(
                        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
                    )
                );
            } catch (\PDOException $e) {
                // @todo
            }
            
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        
        return $this->db;
    }

    ### PRIVATE MEMBERS

    /**
     * @var string
     */
    private $dataSourceName;

    /**
     * @var string
     */
    private $dataSourceUsername;

    /**
     * @var string
     */
    private $dataSourcePassword;
}
