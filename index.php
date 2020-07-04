<?php

require_once __DIR__ . "/vendor/autoload.php";
//mongodb://su:qwer+123@api.data.shalangzhen.cn


class PropagationModel extends \MongoDB\Collection
{
    protected $collectionName = 'promotion';
    public function __construct()
    {
        $manager = (new \MongoDB\Client('mongodb://192.168.56.33'));
        parent::__construct($manager->getManager(), 'datacenter', $this->collectionName);
    }
}

function update($id='', $total=0)
{
    print date('H:i:s') . " ...new start done " . $total . PHP_EOL;
    $propagationModel = new PropagationModel();
    $pipeline = [
        array('$group'      => array('_id' => '$activity_id', 'total'=> array('$sum'=>1),
            'promotions' => array('$push' => ['id'=>'$_id','activity_id'=>'$activity_id','time'=>'$time']))),
        array('$unwind'     => array('path' => '$promotions', 'includeArrayIndex' => 'counter')),
        array('$project'    => array(
            '_id'               => '$promotions.id',
            'counter'           => array('$add' => array('$counter', 1 )),
            'activity_id'       => '$promotions.activity_id',
            'time'              => '$promotions.time',
        ) ),
        array('$sort' => ['_id' => 1]),
        array('$limit' => 3000)
    ];
    if (!empty($id))
    {
        array_unshift($pipeline, ['$match' => ['_id'=> ['$gt' => $id]]]);
    }
    $res = $propagationModel->aggregate(
        $pipeline,
        [
            'allowDiskUse' => true,
            'readPreference' => new \MongoDB\Driver\ReadPreference(\MongoDB\Driver\ReadPreference::PRIMARY)
        ]);
    $i = 0;
    unset($id);
    foreach ($res as $value)
    {
            $id = $value['_id'];
            try {
                $propagationModel->updateOne(['_id' => $id], ['$set' => ['counters' => $value['counter']]]);
            }catch (\Exception $exception)
            {
                print 000;
                print PHP_EOL;
                continue;
            }
            $i += 1;
            if ($i % 100 == 0)
            {
                print $i . "  current number...".PHP_EOL;
            }
    }

    if ($i<1)
    {
        return $i;
    }

    if (isset($id))
    {
        return update($id, $i);
    }
}

$res = update();

print PHP_EOL;

var_dump($res);