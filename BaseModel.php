<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2020/7/5
 * Time: 22:24
 */
require_once  __DIR__ . "/loadConfig.php";
define('DATABASE_NAME',"product");
define('HOST',"mongodb://192.168.56.34");

class BaseModel extends \MongoDB\Collection
{
    protected $collectionName;
    protected $connection = HOST;
    protected $databaseName = DATABASE_NAME;
    public function __construct()
    {
        $manager = (new \MongoDB\Client($this->connection));
        parent::__construct($manager->getManager(), $this->databaseName, $this->collectionName);
    }

    public static function onInstance()
    {
        return new static;
    }
}

class PropagationModel extends BaseModel
{
    protected $collectionName = 'promotion';
}

class CommissionModel extends BaseModel
{
    protected $collectionName = 'commission';
}

class OrderModel extends BaseModel
{
    protected $collectionName = 'orders';
}