<?php
/**
 * Created by PhpStorm.
 * User: 张伟
 * Date: 2020/7/5
 * Time: 22:33
 */
require_once __DIR__ . '/BaseModel.php';

$model = new OrderModel();

$res = $model->find();

$arr = [];

foreach ($res as $k => $v)
{
    $arr[] = (array)$v;
}

var_dump($arr);

