<?php

require_once __DIR__ . '/BaseModel.php';
//mongodb://su:qwer+123@api.data.shalangzhen.cn
function update($arr=[], $total=0)
{
    print date('H:i:s') . " ...new start done " . $total . PHP_EOL;
    $propagationModel = new PropagationModel();
    $pipeline = [
        ['$sort' => ['time' => 1]],
        ['$group' => ['_id'=>'$activity_id', 'arr' => ['$push'=>['id'=>'$_id']]]],
        ['$limit' => 3000]
    ];
    if (!empty($arr))
    {
        array_unshift($pipeline, ['$match' => ['activity_id' => ['$not' => ['$in' => $arr]]]]);
    }
    $res = $propagationModel->aggregate(
        $pipeline,
        [
            'allowDiskUse' => true,
            'readPreference' => new \MongoDB\Driver\ReadPreference(\MongoDB\Driver\ReadPreference::PRIMARY)
        ]);
    $i = 0;
    foreach ($res as $value)
    {
            $propagationNum = 1;
            $activity = $value->_id;//获取优惠券id
            foreach ($value->arr as $item)
            {
                $id = $item['id'];
                try {
                    $propagationModel->updateOne(['_id' => $id], ['$set' => ['counters' => $propagationNum]]);
                    $propagationNum += 1;
                }catch (\Exception $exception)
                {
                    $err[] = $activity;
                    print $activity . PHP_EOL;
                    print 0001;
                    print PHP_EOL;
                    continue;
                }
            }
            $arr[] = $activity;
            $i += 1;
            if ($i % 100 == 0)
            {
                print $i . "  current number...".PHP_EOL;
            }
    }

    if ($i<1)
    {
        file_put_contents('error.txt',isset($err) ? json_encode($err) : 0);
        return $i;
    }

    if (!empty($arr))
    {
        return update($arr, $i);
    }
}
file_put_contents('current_time.txt', date('Y-m-d H:i:s') ." start time \n", FILE_APPEND);
$res = update();
file_put_contents('current_time.txt', date('Y-m-d H:i:s') ." end time \n", FILE_APPEND);
print PHP_EOL;

var_dump($res);