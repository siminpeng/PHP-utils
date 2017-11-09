<?php // 

//排序算法
$sort_arr = array(49,38,65,97,76,13,27);
/*
 * 交换两个值的位置
 */
function change( &$arr, $i,$j )
{
  $temp = $arr[$i];
  $arr[$i]= $arr[$j];
  $arr[$j] = $temp;
}

function swap( &$arr, $i,$j )
{
  $arr[$i]= $arr[$i]+$arr[$j];
  $arr[$j] = $arr[$i] - $arr[$j];
  $arr[$i] = $arr[$i] - $arr[$j];
}


function  orswap( &$arr, $i,$j )
{
  $arr[$i]= $arr[$i]^$arr[$j];
  $arr[$j] = $arr[$i]^$arr[$j];
  $arr[$i] = $arr[$i]^$arr[$j];
}
/*
 * 冒泡排序算法
 */
function BubbleSort( &$arr )
{
  $flag= FALSE;
  $n = count($arr);
  for( $i= 0;$i<$n;$i++)
  {
    //从后往前面比，把最小的值放到i
    for( $j= $n-1 ;$j>$i;$j--)
    {
      if( $arr[ $i ] > $arr[ $j ] )
      {
        change( $arr,$i,$j );
        $flag = TRUE;
      }
    }
  }
  return $flag;
}

/*
 * 选择排序
 */
function SelectSort( &$arr )
{
  $flag= FALSE;
  $n = count($arr);
  for( $i= 0; $i< $n; $i++)
  {
    $min = $i;//最小位置的下标
    //从后往前面比，把最小的值放到i
    for( $j= $n-1 ;$j>$i;$j--)
    {
      if( $arr[ $min ] > $arr[ $j ] )
      {
        $min = $j;//记录最小的位置
        $flag = TRUE;
      }
    }
    if( $i != $min )
    {
      change( $arr,$i,$min); //交换位置 选择排序是不稳定的排序
    }
  }
  return $flag;
}

/*
 * 快速排序
 * 递归每次划分为不同的分组
 * 结束条件 $low < $high
 */
function QuickSort( &$arr,$low,$high)
{
  //选择数组的第一个值为基准点
  if( $high >$low )
  {
    $pivotpos = Partition( $arr,$low,$high );//返回划分点的位置
    QuickSort( $arr, $low, $pivotpos-1);//左边的排序
    QuickSort( $arr, $pivotpos+1, $high);//左边的排序
  }
}
//返回划分点的位置
function Partition( &$arr,$low,$high )
{
  //将第一个值作为划分的基准点
  $pivot = $arr[ $low ];
  //将第一个值与后面的值比较，直到low>high停止
  while ( $low <$high )
  {
    //比基准点小，就移到前面
    while( $low < $high && $pivot <= $arr[ $high ] )
    {
      $high--;
    }
     $arr[ $low ] = $arr[ $high ];
    //比基准点大，就移到后面
    while( $low < $high && $pivot >= $arr[ $low ] )
    {
      $low++;
    }
    $arr[ $high ] = $arr[ $low ];
  }
  $arr[ $low ] = $pivot;
  return $low;
}

/*
 * 直接插入排序
 * 我自己写的，写的好恶心
 */
function MyInsertSort( &$arr)
{
  $n = count( $arr);
  //将2..到第n个值插入到前面的数组中
  for( $i = 1; $i<$n; $i++ )
  {
    for( $j = 0; $j<$i;$j++)
    {
      $insert = $i;
      $temp = $arr[$i];
      if( $temp < $arr[$j] )//将$arr[i]插入到 $arr[j]之前，$arr[j]之后值后移
      {
        $insert = $j;
        break;
      }
      if( $temp >= $arr[$j] )//将$arr[i]插入到 $arr[j]之后，$arr[j]之后的值后移
      {
        $insert = $j+1;
      }
    }
    
    for( $j=$i; $j> $insert; $j-- )
    {
      $arr[$j]=$arr[$j-1];
    }
    $arr[$insert] = $temp;
  }
}

/*
 * 书上的插入排序，将arr[0],设置为哨兵，不使用
 */
function InsertSort( &$arr)
{
  //先在数组的前面加一哨兵
  array_unshift( $arr ,0);
  $n = count( $arr);
  //将2..到第n个值插入到前面的数组中
  for( $i = 2; $i<$n; $i++ )
  {
    //如果当前要插入的数字小于最后一位的数据，就插入到前面合适的位置
    if( $arr[ $i ] < $arr[ $i-1 ] )
    {
      $arr[ 0 ] = $arr[ $i ];
      //从后往前边移动边比较
      for( $j = $i-1; $arr[0] < $arr[ $j ]; $j-- )
      {
        $arr[ $j+1 ] = $arr[ $j ];
      }
      //将插入的数值移动到该移动的位置
      $arr[ $j+1 ] = $arr[ 0 ];
    }
  }
  //将数组的第一个值删除
  array_shift( $arr );
}

/*
 * 折半插入排序
 */
function InsertSort2( &$arr)
{
  //先在数组的前面加一哨兵
  array_unshift( $arr ,0);
  $n = count( $arr);
  //将2..到第n个值插入到前面的数组中
  for( $i = 2; $i<$n; $i++ )
  {  
    //如果当前要插入的数字小于最后一位的数据，就插入到前面合适的位置
    //从已经排好顺序的部分中间开始查找插入位置
    if( $arr[ $i ] < $arr[ $i-1 ] )
    {
      $arr[ 0 ] = $arr[ $i ];
      $low = 1;
      $high = $i-1;
      while( $low <= $high ) 
      {
        $mid = ( $low + $high )/2;//中间点
        if( $arr[ 0 ] < $arr[ $mid ])
        {
          $high = $mid - 1;
        }
        else
        {
          $low =  $mid + 1;
        }
      }
      
      //将high后面的值向后移动
      for( $j = $i-1; $j >= $high +1; $j-- )
      {
        $arr[ $j+1 ] = $arr[ $j ];
      }
      //将插入的数值移动到该移动的位置
      $arr[ $high+1 ] = $arr[ 0 ];
    }
  }
  //将数组的第一个值删除
  array_shift( $arr );
}

//BubbleSort($sort_arr);
//SelectSort($sort_arr);
//QuickSort( $sort_arr, 0 ,count($sort_arr)-1);
InsertSort2($sort_arr);
var_dump($sort_arr);



