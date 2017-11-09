<?php

/*
 * redis的使用相关
 */

$redis = new Redis();

//$host = '192.128.18.176';
$host = '127.0.0.1';
$port = 6379;
$redis->connect( $host, $port );

//string类型
$redis->set('test','hello world');//设置值
//echo $redis->get('test');

//incr 自增操作
$redis->set('age', 23 );
$redis->incr('age');
$redis->incrBy('age',5);
$redis->decr('age');
$redis->decrBy('age',5);
//echo $redis->get('age');

$keys =  $redis->keys('*');
//var_dump($keys);
