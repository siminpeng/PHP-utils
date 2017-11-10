<?php

/* 
 * mongo的相关应用
 */

//实例化mongo客户端
function init()
{
  $connection = new MongoClient();
  $db = $connection->test;
  return $db;
}

/*
 * 插入数据
 */
function insert( $num )
{
  $db = init();
  $rs = $db->goods->insert(array('name'=>'reahat','msg'=>'test','num'=>$num));

  if( $rs['ok'] == 1 )
  {
    echo '插入成功';
  }
  else 
  {
    echo '插入失败';
    var_dump($rs);
  }
}

/*
 * 查询数据
 */
function find()
{
  $db = init();
  
  $rs = $db->goods->find(array('name'=>'reahat'));
  $num = 1;
  foreach ($rs as $key => $value) 
  {
    echo $num++ . '</br>';
    echo 'name:'.$value['name'].'</br>';
    echo 'msg:'.$value['msg'].'</br>';
    echo 'num:'.$value['num'].'</br>';
    echo '</br>';
  }
}

/*
 * 修改数据
 */
function  update()
{
  $db = init();
  $rs = $db->goods->update(array('name'=>'reahat'),array('$set'=>array('msg'=>'test lisisi')));
  var_dump($rs);
}

/*
 * 删除数据
 */
function del()
{
  $db = init();
  $rs = $db->goods->remove(array('name'=>'reahat'));
  var_dump($rs);
}


//测试
//update();
//insert( 1 );
find();
//del();
//insert( 2 );
//find();

