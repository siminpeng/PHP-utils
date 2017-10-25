<?php

/* 
 * 2017-10-25
 * 与时间相关的常用工具函数
 */

/**
 * 将时间戳转换为通常用的时间格式
 * @param type $timestamp 时间戳
 * @return type 2017-10-25 10:00:00
 */
function fullFormatTime( $timestamp )
{
  return strftime( "%Y-%m-%d %H:%M:%S", $timestamp );
}

/**
* 转换为UNIX时间戳
*/
function gettime( $d )
{
  if ( is_numeric( $d ) )
    return $d;
  else
  {
    return strtotime( $d );
  }
}

/**
*
* DateAdd(interval,number,date)
* 返回已添加指定时间间隔的日期。
* Inetrval为表示要添加的时间间隔字符串表达式，例如分或天
* number为表示要添加的时间间隔的个数的数值表达式
* Date表示日期
*
* Interval(时间间隔字符串表达式)可以是以下任意值:
*  yyyy year年
*  q Quarter季度
*  m Month月
*  y Day of year一年的数
*  d Day天
*  w Weekday一周的天数
*  ww Week of year周
*  h Hour小时
*  n Minute分
*  s Second秒
*  w、y和d的作用是完全一样的，即在目前的日期上加一天，q加3个月，ww加7天。
*/
function DateAdd( $interval, $number, $date )
{
  $date = gettime( $date );
  $date_time_array = getdate( $date );
  $hours = $date_time_array["hours"];
  $minutes = $date_time_array["minutes"];
  $seconds = $date_time_array["seconds"];
  $month = $date_time_array["mon"];
  $day = $date_time_array["mday"];
  $year = $date_time_array["year"];
  switch ( $interval )
  {
    case "yyyy": $year +=$number;
      break;
    case "q": $month +=($number * 3);
      break;
    case "m": $month +=$number;
      break;
    case "y":
    case "d":
    case "w": $day+=$number;
      break;
    case "ww": $day+=($number * 7);
      break;
    case "h": $hours+=$number;
      break;
    case "n": $minutes+=$number;
      break;
    case "s": $seconds+=$number;
      break;
  }
  $timestamp = mktime( $hours, $minutes, $seconds, $month, $day, $year );
  return $timestamp;
}

/**
* DateDiff(interval,date1,date2)
* 返回两个日期之间的时间间隔
* intervals(时间间隔字符串表达式)可以是以下任意值:
*   w  周
*   d  天
*   h  小时
*   n  分钟
*   s  秒
*/
function DateDiff( $interval, $date1, $date2 )
{
  // 得到两日期之间间隔的秒数
  $timedifference = strtotime( $date2 ) - strtotime( $date1 );
  switch ( $interval )
  {
    case "w": $retval = bcdiv( $timedifference, 604800 );
      break;
    case "d": $retval = bcdiv( $timedifference, 86400 );
      break;
    case "h": $retval = bcdiv( $timedifference, 3600 );
      break;
    case "n": $retval = bcdiv( $timedifference, 60 );
      break;
    case "s": $retval = $timedifference;
      break;
  }
  return $retval;
}


/**
 * 计算时间差
 * @param string $start 格式 2011-03-12 12:32:03
 * @param <type> $end 格式 2011-03-12 12:32:03
 * @param <type> $unit 精确到 hour/minute/second
 * @return string
 */
function time2Units ( $start, $end , $unit = 'minute')
{
	$starttime = strtotime( $start );
	$endtime = strtotime( $end );

	$time = $endtime - $starttime ;

  //$year  = floor($time / 60 / 60 / 24 / 365);
  //$time  -= $year * 60 * 60 * 24 * 365;
  //$month  = floor($time / 60 / 60 / 24 / 30);
  //$time  -= $month * 60 * 60 * 24 * 30;
  //$week  = floor($time / 60 / 60 / 24 / 7);
  //$time  -= $week * 60 * 60 * 24 * 7;
  //$day    = floor($time / 60 / 60 / 24);
  //$time  -= $day * 60 * 60 * 24;
  $hour  = floor($time / 60 / 60);
  $time  -= $hour * 60 * 60;
  $minute = floor($time / 60);
  $time  -= $minute * 60;
  $second = $time;
  $elapse = '';

  $unitArr = array( '.'=>'hour', ''=>'minute', '秒'=>'second' );

  foreach ( $unitArr as $cn => $u )
  {
    if( $u == 'second' )break;
    if ( $$u > 0 )
    {
      if( $u == 'minute' )//计算分钟数
        $$u = round( $$u / 60, 2 ) ;

      $elapse += $$u ;
    }
  }
  $elapse = format_number( $elapse , 2  );

  return $elapse;
}

function time_now()
{
  return date( "Y-m-d H:i:s" );
}

function time_today()
{
  return date( "Y-m-d" );
}

function time_yestoday()
{
  $pf_time = strtotime("-1 days");
  return date( "Y-m-d", $pf_time );
}

/**
 * 获取指定日期的上一个月
 * 
 * @param string $day date
 * @return string format as date( 'Y-m' )
 */
function time_lastmonth( $day = FALSE )
{
  //不能用-1month实现，比如获取2016-03-31的上一月就会有bug(得到2016-03)
  //改用先获取指定日期所在月第1天，然后再减去1天，即得到上月
  if( $day == FALSE )
  {
    $day_month_first_day = date( 'Y-m-01' );
  }
  else
  {
    $day_month_first_day = date( 'Y-m-01', strtotime( $day ) );
  }
  return date( 'Y-m', strtotime( '-1 day', strtotime( $day_month_first_day ) ) );
}

/**
 * 获取指定日期的下一个月
 * 
 * @param string $day date
 * @return string format as date( 'Y-m' )
 */
function time_nextmonth( $day = FALSE )
{
  //不能用+1month实现，比如获取2016-01-31的下一个月就会有bug(得到2016-03)
  //改用先获取指定日期所在月最后1天，然后再加1天，即得到下月
  if( $day == FALSE )
  {
    $day_month_last_day = date( 'Y-m-t' );
  }
  else
  {
    $day_month_last_day = date( 'Y-m-t', strtotime( $day ) );
  }
  return date( 'Y-m', strtotime( '+1 day', strtotime( $day_month_last_day ) ) );
}

/**
 * 获取指定日期的月的最后一天
 * @param type $day
 * @return type
 */
function time_monthlastday( $day = FALSE )
{
  $last_month_first_day = time_nextmonth( $day )."-01";
  return date( "Y-m-d", strtotime( $last_month_first_day ) - 1 );
}

function time_month_firstday()
{
  return date( "Y-m-01" );
}

function time_weekday( $date )
{
  $date = gettime( $date );
  $date_time_array = getdate( $date );
  $weekday = $date_time_array['weekday'];
  switch ( $weekday ) 
  {
    case "Monday":
      $weekday_chinese = "周一";
      break;
    case "Tuesday":
      $weekday_chinese = "周二";
      break;
    case "Wednesday":
      $weekday_chinese = "周三";
      break;
    case "Thursday":
      $weekday_chinese = "周四";
      break;
    case "Friday":
      $weekday_chinese = "周五";
      break;
    case "Saturday":
      $weekday_chinese = "周六";
      break;
     default :
      $weekday_chinese = "周日";
      break;
  }
  return $weekday_chinese;
}

function time_add( $old_time, $interval )
{
  $t = new DateTime( $old_time );
  $t->add( new DateInterval( "PT".strtoupper( $interval ) ) );
  return $t->format( "Y-m-d H:i:s" );
}

function time_sub ( $old_time ,$interval )
{
  $t = new DateTime( $old_time );
  $t->sub( new DateInterval( "PT".strtoupper( $interval ) ) );
  return $t->format( "Y-m-d H:i:s" ); 
}
/**
* 获取上月同期时间
* @param type $cur_time 时间
* @param type $flag 标志位,FALSE表示查询上月的开始时间，TRUE表示查询上月的结束时间
*/
function time_lastmonthday ( $cur_time, $flag )
{
  $last_month_today = "";
  //得到当前月的第一天
  $firstday = substr( $cur_time , 0, 7 )."-01";
  //得到前一个月的最后一天
  $last_month_last_day = date("Y-m-d 23:59:59", strtotime( $firstday ."-1 day" ) );
  //得到下一个月的第一天
  $fur_month_first_day = date("Y-m-d H:i:s", strtotime( $firstday ."+1 month" ) );
  //得到当前月的最后一天
  $cur_month_last_day = date("Y-m-d 23:59:59", strtotime( $fur_month_first_day ."-1 day" ) );
  // 得到前一个月的当前时间
  $last_month_time = date("Y-m-d H:i:s", strtotime( $cur_time ."-1 month" ) );
  if( $last_month_time > $last_month_last_day || ( $cur_month_last_day == $cur_time && $last_month_time < $last_month_last_day ) )
  {
    $last_month_today = $last_month_last_day;
  }
  else
  {
    if( $flag == true )
      $last_month_today = date( "Y-m-d 23:59:59", strtotime( $last_month_time ) );
    else
      $last_month_today = date( "Y-m-d 00:00:00", strtotime( $last_month_time ) );
  }
  return $last_month_today;
}

/*
 * 返回任意月份后的同一天
 * 3月31日加6月对应9月30日；2月28日加6月对应8月28日；8月29日、30日、31日加6月都对应2月28日（如果有29日则对应29日）
 * @param date $old_date  基准日期 格式：2016-01-01
 * @param int $number 要增加的月份数
 * @param bool $flag  空表示查询只返回日期，TRUE表示查询这一天的开始时间，FALSE表示查询这一天的结束时间
 * @return date $result_date
 */
function time_anymonthday( $old_date, $number, $flag = '' )
{
  $result_date = '';
  $date = new DateTime();
  
  //得到当前月的第一天
  $current_month_firstday = substr( $old_date , 0, 7 ) . '-01';
  //得出加$number月后最后一天
  $current_month_firstday_array = explode( '-', $current_month_firstday );
  $date->setDate( $current_month_firstday_array[ 0 ], $current_month_firstday_array[ 1 ], $current_month_firstday_array[ 2 ] );
  $date->modify( "+{$number} month +1 month -1 day" );
  $plus_month_last_day = $date->format( 'Y-m-d' );
  //得出加$number月的当前时间
  $old_date_array = explode( '-', $old_date );
  $date->setDate( $old_date_array[ 0 ], $old_date_array[ 1 ], $old_date_array[ 2 ] );
  $date->modify( "+{$number} month" );
  $plus_month_same_day = $date->format( 'Y-m-d' );
  
  if( $plus_month_same_day < $plus_month_last_day )
  {
    $result_date = $plus_month_same_day;
  }
  else
  {
    $result_date = $plus_month_last_day;
  }
  
  if( $flag !== '' )
  {
    if( $flag == true )
    {
      $result_date .= ' 00:00:00';
    }
    else
    {
      $result_date .= ' 23:59:59';
    }
  }
  
  return $result_date;
}

function day_add( $old_day, $interval )
{
  $t = new DateTime( $old_day );
  $t->add( new DateInterval( "P".strtoupper( $interval ) ) );
  return $t->format( "Y-m-d" );
}

function is_datetime( $str )
{
  try
  {
    $ret = new DateTime( $str );
    if( $ret !== FALSE )
      return TRUE;
    else
      return FALSE;
  }
  catch(Exception $e)
  {
    return FALSE;
  }
}
