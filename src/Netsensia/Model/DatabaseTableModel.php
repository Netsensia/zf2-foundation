<?php
namespace Netsensia\Model;

use Zend\InputFilter\Factory as InputFactory;
use Exception;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use PDO;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Netsensia\Provider\ProvidesConnection;
use Netsensia\Provider\ProvidesServiceLocator;
use Netsensia\Provider\ProvidesModels;
use Netsensia\Model\Exception\KeyNotFoundException;

abstract class DatabaseTableModel 
    implements InputFilterAwareInterface, ServiceLocatorAwareInterface
{
    use ProvidesServiceLocator, ProvidesConnection, ProvidesModels;

    abstract public function init($id);
    
    /**
     * @var array $relations
     */
    protected $relations = [];
    
    /**
     * @var string $tableName
     */
    private $tableName;
    
    /**
     * @var array $primaryKey 
     */
    private $primaryKey = null;

    /**
     * @var array $data
     */
    private $data = [];

    /**
     * @var Zend\InputFilter\InputFilter $inputFilter
     */
    private $inputFilter;
    
    /**
     * @var Zend\InputFilter\InputFactory $inputFactory
     */
    private $inputFactory;
    
    public function __construct()
    {
        $this->inputFactory = new InputFactory();
        $this->inputFilter = new InputFilter();
    }
    
    /**
     * @param array $def
     */
    protected function addInputFilter($def)
    {
        $this->inputFilter->add($this->inputFactory->createInput($def));
    }
    
    public function setRelation($column, $model) 
    {
        $this->relations[$column] = [
            'model' => $model,
        ];
    }
    
    /**
     * @return the $tableName
     */
    public function getTableName ()
    {
        return $this->tableName;
    }

    /**
     * @return the $primaryKey
     */
    public function getPrimaryKey ()
    {
        return $this->primaryKey;
    }

    /**
     * @param string $tableName
     */
    public function setTableName ($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @param multitype: $primaryKey
     */
    public function setPrimaryKey ($primaryKey)
    {
        if (!is_array($primaryKey)) {
            throw new Exception("Primary key must be an array");
        }
        $this->primaryKey = $primaryKey;
    }
    
    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;        
    }
    
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function get($key)
    {
        if (!isset($this->data[$key])) {
            throw new KeyNotFoundException(
                'Data key "' . $key . '" is ' .
                'unavailable in ' . print_r($this->data, true)
            );
        }
        return $this->data[$key];
    }
    
    public function set($key, $value)
    {
        if (!array_key_exists($key, $this->data)) {
            throw new Exception('Unexpected data key ' . $key . ' in set()');
        }
        $this->data[$key] = $value;
    }
    
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        $this->inputFilter = $inputFilter;
    }
    
    public function getInputFilter()
    {
        return $this->inputFilter;
    }
    
    public function createRelations()
    {
    	$relations = [];
    	foreach ($this->data as $key => $value) {
    		if (strpos($key, '_') !== false) {
    			$parts = explode('_', $key);
    			$relations[$parts[0] . '_' . $parts[1]][$parts[2]] = $value;
    			unset($this->data[$key]);
    		}
    	}
    	 
    	foreach ($relations as $columnAndModelName => $fieldData) {
    		$parts = explode('_', $columnAndModelName);
    		$columnName = $parts[0];
    		$modelName  = $parts[1];
    		$model = $this->newModel($modelName);
    		$model->setData($fieldData);
    		$this->data[$columnName] = $model->create();
    	}
    }
    
    public function create()
    {
    	$this->createRelations();
    	
        $data = $this->getPrimaryKey() + $this->data;
        
        $sql = 'INSERT INTO ' . $this->getTableName() . ' (';
        
        foreach ($data as $key => $value) {
            $sql .= $key . ',';
        }
        $sql = substr($sql, 0, -1);
        $sql .= ') VALUES (';
        foreach ($data as $key => $value) {
            $sql .= ':' . $key . ',';
        }
        $sql = substr($sql, 0, -1);
        $sql .= ')';
        
        $query = $this->getConnection()->prepare($sql);

        $bindArray = [];
        foreach ($data as $key => $value) {
            $bindArray[$key] = $this->getValueFromDataElement($value);
        }

        $query->execute($bindArray);
        return $this->getConnection()->lastInsertId();
    }
    
    public function save()
    {
    	$this->createRelations();
    	
        $sql = 'UPDATE ' . $this->getTableName() . ' SET ';
        foreach ($this->data as $key => $value) {
            $sql .= $key . '= :' . $key . ',';
        }
        $sql = substr($sql, 0, -1);
        $sql .= ' WHERE ';
        foreach ($this->getPrimaryKey() as $column => $value) {
            $sql .= $column . '=' . ':' . $column . ' AND ';
        }
        $sql = substr($sql, 0, -5);
        
        $query = $this->getConnection()->prepare($sql);
        
        $map = [];
        
        foreach ($this->data as $key => $value) {
            $map[$key] = $this->getValueFromDataElement($value);
        }
       
        $query->execute($map);
       
        return true;
    }
    
    public function isPopulated()
    {
        $pk = $this->getPrimaryKey();
        
        return ($pk != null && reset($pk) != null);
    }
    
    /**
     * Shortcut method for single-column primary keys
     */
    public function getId()
    {
        if (!$this->isPopulated()) {
            throw new Exception(
                "Model is not populated"
            );
        }
        
        if (count($this->primaryKey) != 1) {
            throw new Exception(
                'Shortcut method getId() may only be used ' .
            	'on models with single-column primary keys'
            );
        }
        
        $id = reset($this->primaryKey);
        
        return $id !== false ? $id : null;
    }

    public function load()
    {
        $sql =
            "SELECT * " .
            "FROM " . $this->getTableName() . " " .
            "WHERE ";
         
        $map = array();
        
        foreach ($this->getPrimaryKey() as $column => $value) {
            $sql .= $column . ' = :' . $column . ' AND ';
            $map[':' . $column] = $value;
        }
        
        $sql = substr($sql, 0, -5);
        $sql .= ' LIMIT 1';
        
        $query = $this->getConnection()->prepare($sql);
    
        $query->execute($map);
    
        $data = $query->fetch(PDO::FETCH_ASSOC);
        
        if ($data) {
            $this->setData($data);
            
            foreach ($this->relations as $column => $relation) {
                if ($this->data[$column] != '') {
                    $model = $this->loadModel(
                        $relation['model'],
                        $this->data[$column]
                    );
    
                    $modelData = $model->getData();
                    
                    if (is_array($modelData)) {
                        foreach ($modelData as $field => $value) {
                            $this->data[$column . '_' . $relation['model'] . '_' . $field] = $value;
                        }
                    }
                }
            }
            
            return true;
        }
        
        return false;
    }

    private function getValueFromDataElement($value)
    {
        if (isset($value['type'])) {
            $value = $value['value'];
        }
    
        return $value;
    }    
}
