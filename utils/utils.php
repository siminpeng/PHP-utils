<?php
defined ( '_EXEC_' ) or die ( '非法访问,拒绝请求!' );


function arrayIsSet( $array_info, $name )
{
  if( !isset( $array_info[ $name ] ) )
    return false;
  return ( strlen( $array_info[ $name ]) > 0 );
}

function guid()
{
  if ( function_exists( 'com_create_guid' ) )
  {
    $ret = com_create_guid();
    return preg_replace( "/{|-|}/", '', strtoupper( $ret ) );
  }
  else
  {
    mt_srand( (double) microtime() * 10000 ); //optional for php 4.2.0 and up.
    $charid = strtoupper( md5( uniqid( rand(), true ) ) );
    return $charid;
  }
}

function generateID( $random_length = 8, $day_length = 6, $day = NULL )
{
  //取日期
  $genDay = strtotime("now");

  if( is_numeric( $day ) )
    $genDay = $day;
  else if( is_string( $day ) )
    $genDay = strtotime( $day );

  if( $day_length == 8 )
    $dayPrefix = date( "Ymd", $genDay );
  else
    $dayPrefix = date( "ymd", $genDay );

  //取随机数
  $id_random = mt_rand( 1, (pow(10, $random_length) -1) );
  //随机数前面补0
  $surfix = str_pad($id_random, $random_length, "0", STR_PAD_LEFT);

  //返回 日期+随机数
  return $dayPrefix.$surfix;
}

function generateNumberID( $random_length = 8 )
{
  //取随机数
  $number_random = mt_rand( 1, ( pow( 10, $random_length ) - 1 ) );
  //随机数前面补0
  return str_pad( $number_random, $random_length, "0", STR_PAD_LEFT );
}

function startsWith( $src_str, $prefix )
{
	if( strlen( $src_str ) < strlen( $prefix ) )
	  return FALSE;

	return (strncmp( $src_str, $prefix, strlen( $prefix ) ) == 0);
}

function endsWith( $src_str, $surfix )
{
	$surfix_len = strlen( $surfix );
  if( strlen( $src_str ) < $surfix_len )
    return FALSE;

  return (substr_compare( $src_str, $surfix, (-1 * $surfix_len), $surfix_len ) == 0);
}

function removePrefix( $src_str, $prefix )
{
	if( startsWith( $src_str, $prefix ) )
	{
    if ( $src_str === $prefix )
    {
      return "";
    }
    else
    {
      return substr( $src_str, strlen( $prefix ) );
    }
  }
	return $src_str;
}

function removeSurfix( $src_str, $surfix )
{
  if( endsWith( $src_str, $surfix ) )
  {
    return substr( $src_str, 0, strlen($src_str) - strlen( $surfix ) );
  }
  return $src_str;
}

function removeDir($dir, $DeleteMe = TRUE)
{
  if(!$dh = @opendir($dir)) return FALSE;
  while (($obj = readdir($dh)) !== FALSE)
  {
    if($obj=='.' || $obj=='..') continue;
    if (!@unlink($dir.'/'.$obj)) removeDir($dir.'/'.$obj, TRUE);
  }
  if ($DeleteMe)
  {
    closedir($dh);
    return rmdir($dir);
  }
}

function arrayEquals( $array1, $array2 )
{
  if( count( $array1 ) !== count( $array2 ) )
    return FALSE;

  foreach( $array1 as $key => $value )
  {
    if( !array_key_exists( $key, $array2 ) )
      return FALSE;
    $value2 = $array2[ $key ];
    if( $value !== $value2 )
    {
      return FALSE;
    }
  }
  return TRUE;
}


/*************************客户端相关*******************************************/

/**
	* 功能说明：	取得当前用户的ip 字串类型如'192.168.1.1'
	* 参数：
	* 返回值：      $userip当前用户的ip值
	*/
function gf_getip()
{
  if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) )
    $ip = $_SERVER['HTTP_X_REAL_IP'];
  else if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
    $ip = getenv("HTTP_CLIENT_IP");
  else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
    $ip = getenv("HTTP_X_FORWARDED_FOR");
  else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
    $ip = getenv("REMOTE_ADDR");
  else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
    $ip = $_SERVER['REMOTE_ADDR'];
  else
    $ip = "unknown";
  return( $ip );
}

/**
	* 功能说明：	把相应的字串型ip转化为长整形ip
	* 参数：            $ip 字串型ip
	* 返回值：       长整形ip
	*/
function gf_ip2long( $ip )
{
	if( $ip == "unknown" )
	  return 0;
	return ((ip2long($ip) & 0x7FFFFFFF) + 0x80000000);
}

/**
	* 功能说明：	检查ip的合法性
	* 参数：          字串型ip
	* 返回值：     true 合法  false 非法
	*/
function gf_checkip($ip)
{
  if(gf_ip2long($ip) < 0)
    return false;

  return true;
}

/***************************************************时间相关***************************/
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
/***********************目录相关*****************************************************
/**
* 生成目录树
*/

function mkdirs($path , $mode = 0755 ){
    if(!is_dir($path)){
        mkdirs(dirname($path),$mode);
        mkdir($path,$mode);
    }
    return true;
}


function addFolderToZip($dir, $zipArchive, $zipdir = ''){
  if (is_dir($dir)) {
    if ($dh = opendir($dir)) {

      //Add the directory
      //$zipArchive->addEmptyDir($dir);

      // Loop through all the files
      while (($file = readdir($dh)) !== false) {
        //If it's a folder, run the function again!
        if(!is_file($dir . $file)){
          // Skip parent and root directories
          if( ($file !== ".") && ($file !== "..")){
              addFolderToZip($dir . $file . "/", $zipArchive, $zipdir . $file . "/");
          }

        }else{
          // Add the files
          $zipArchive->addFile($dir . $file, $zipdir . $file);

        }
      }
    }
  }
}

function listFolder( $folder, $listHiddenFiles = false )
{
  if ( ! is_dir( $folder ) )
    throw new Exception("$folder is not a folder.");

  $dir = dir($folder);


  $entries = array();
  while (false !== ($entry = $dir->read()))
  {
    if ( !$listHiddenFiles && $entry{0} == "." )
      continue;

    $entries[] = $entry;
  }

  return $entries;
}

function deleteFolder($directory, $empty = false) 
{ 
    if(substr($directory,-1) == "/")
        $directory = substr($directory,0,-1); 

    if(!file_exists($directory) || !is_dir($directory))
        return false; 
    elseif(!is_readable($directory))
        return false; 
    else 
    { 
      $directoryHandle = opendir($directory); 

      while ($contents = readdir($directoryHandle)) 
      { 
        if($contents != '.' && $contents != '..') 
        { 
            $path = $directory . "/" . $contents;
            if(is_dir($path))
                deleteFolder($path); 
            else
                unlink($path); 
          } 
        } 
        
        closedir($directoryHandle); 

        if($empty == false)
        { 
          if(!rmdir($directory)) 
            return false; 
        }        
        return true; 
    } 
}

/**
 * 列出指定目录下面的所有文件
 * 
 * @param string $path 目录
 * @param boolean $filter_folder 是否过滤掉文件夹
 * @param string_enum $order_attr 按什么排序，可选范围：mtime 修改时间、ctime 创建时间、atime 访问时间、size 文件大小，默认按mtime 修改时间
 * @param boolean $order_dir_asc 排序方向，默认FALSE 降序，TRUE 升序
 * @return array file names
 * @throws Exception
 */
function listDirectoryFiles( $path, $filter_folder = TRUE, $order_attr = 'mtime', $order_dir_asc = FALSE )
{
  $files = array();
  
  while( endsWith( $path, DIRECTORY_SEPARATOR ) )
  {
    $path = removeSurfix( $path, DIRECTORY_SEPARATOR );
  }
  if( ! is_dir( $path ) )
  {
    throw new Exception( $path.' is not folder!' );
  }
  //现在支持按 mtime 修改时间、ctime 创建时间、atime 访问时间、size 文件大小排序
  $order_attrs = array( 'mtime', 'ctime', 'atime', 'size' );
  if( ! in_array( $order_attr, $order_attrs ) )
  {
    throw new Exception( 'argument '.$order_attr.' is not support sort!' );
  }
  if( ! is_bool( $order_dir_asc ) )
  {
    throw new Exception( 'argument '.$order_dir_asc.' is unallowed!' );
  }
  
  $tmp_files = array();
  $mixed_files = scandir( $path );
  foreach( $mixed_files as $mixed_file )
  {
    if( ( $mixed_file[ 0 ] == '.' ) )
    {
      continue;
    }
    
    $full_path_file_name = $path.DIRECTORY_SEPARATOR.$mixed_file;
    $is_dir = is_dir( $full_path_file_name );
    if( $filter_folder && $is_dir )
    {
      continue;
    }
    
    //修改时间
    $mtime = filemtime( $full_path_file_name );
    //创建时间
    $ctime = filectime( $full_path_file_name );
    //访问时间
    $atime = fileatime( $full_path_file_name );
    //文件大小
    $size_bytes = filesize( $full_path_file_name );
    
    $tmp_std = new stdClass();
    $tmp_std->is_dir = $is_dir;
    $tmp_std->file_name = $mixed_file;
    $tmp_std->full_path_file_name = $full_path_file_name;
    $tmp_std->created_time = $ctime;
    $tmp_std->created_time_format = ( $ctime === FALSE ) ? '未知' : date( 'Y-m-d H:i:s', $ctime );
    $tmp_std->modified_time = $mtime;
    $tmp_std->modified_time_format = ( $mtime === FALSE ) ? '未知' : date( 'Y-m-d H:i:s', $mtime );
    $tmp_std->access_time = $atime;
    $tmp_std->access_time_format = ( $atime === FALSE ) ? '未知' : date( 'Y-m-d H:i:s', $atime );
    $tmp_std->size_bytes = $size_bytes;
    
    switch ( $order_attr )
    {
      case 'mtime':
        $sort_by = ( $mtime === FALSE ) ? 0 : $mtime;
        break;
      case 'ctime':
        $sort_by = ( $ctime === FALSE ) ? 0 : $ctime;
        break;
      case 'atime':
        $sort_by = ( $atime === FALSE ) ? 0 : $atime;
        break;
      case 'size':
        $sort_by = ( $size_bytes === FALSE ) ? 0 : $size_bytes;
        break;
    }
    $tmp_std->sort_by = $sort_by;
    $tmp_files[] = $tmp_std ;
    unset( $sort_by );
  }
  
  if( $order_dir_asc )
  {
    usort( $tmp_files, '_sortListDirectoryFilesAsc' );
  }
  else
  {
    usort( $tmp_files, '_sortListDirectoryFilesDesc' );
  }
  $files = array_values( $tmp_files );
  return $files;
}
function _sortListDirectoryFilesAsc( $std1, $std2 )
{
  return $std1->sort_by > $std2->sort_by;
}
function _sortListDirectoryFilesDesc( $std1, $std2 )
{
  return $std1->sort_by < $std2->sort_by;
}

/**
 * 下载文件（部分浏览器不支持文件名中有空格）
 * 
 * @param string $full_path_file_name 文件完整路径名及文件名（如果文件名是从页面上传过来，要确保已经URLDecode）
 * @param HTTPResponse $response HTTPResponse
 * <br>在FrontController中使用的示例代码：
 * do_testfiledownload()
 * {
 *   $path = 'xxx';
 *   $file_name = urldecode( $this->request->getParameter( 'file_name' ) );//file_name在浏览器里经encodeURI()处理过
 *   $full_path_file_name = endsWith( $path, DIRECTORY_SEPARATOR ) ? $path.$file_name : $path.DIRECTORY_SEPARATOR.$file_name;
 *   downloadFile( $full_path_file_name, $this->response );
 * }
 */
function downloadFile( $full_path_file_name, &$response )
{
  if( ! file_exists( $full_path_file_name ) )
  {
    $response->setContent( '<script type="text/javascript">alert("文件未找到！");</script>' );
    exit();
  }
  
  $pathinfo = pathinfo( $full_path_file_name );
  $file_name = $pathinfo[ 'basename' ];
  $file_size = filesize( $full_path_file_name );
  
  $file = fopen( $full_path_file_name, "r" );
  Header( "Content-type: application/octet-stream" );
  Header( "Accept-Ranges: bytes" );
  Header( "Accept-Length: ".$file_size );
  if( preg_match( "/MSIE/", $_SERVER[ "HTTP_USER_AGENT" ] ) )
  {
    $file_name = iconv( 'UTF-8', 'GB2312', $file_name );
  }
  //TODO 对于文件名中有空格的情况还没完全兼容
  Header( "Content-Disposition: attachment; filename=" . $file_name );
  $response->outputHeader();
  echo fread( $file, $file_size );
  fclose($file);
  exit();
}


/**
 * 计算时间差
 * @param string $start 格式 2011-03-12 12:32:03
 * @param <type> $end 格式 2011-03-12 12:32:03
 * @param <type> $unit 精确到
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


function is_mobile( $mobile ) 
{
  if ( strlen( $mobile ) != 11 || !is_numeric( $mobile ) ) {
    return false;
  }
  return preg_match( "/^1[3,4,5,7,8]\d{9}$/", $mobile );
}

function filesize_format($size) 
{
  $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
  if ($size == 0) { 
    return('n/a'); 
  } else {
    return (round($size/pow(1024, ($i = floor(log($size, 1024)))), $i > 1 ? 2 : 0) . $sizes[$i]); 
  }
}

//是否序列化
function is_serialized( $data ) 
{
    // if it isn't a string, it isn't serialized
    if ( !is_string( $data ) )
        return false;
    $data = trim( $data );
    if ( 'N;' == $data )
        return true;
    if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
        return false;
    switch ( $badions[1] ) {
        case 'a' :
        case 'O' :
        case 's' :
            if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
                return true;
            break;
        case 'b' :
        case 'i' :
        case 'd' :
            if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
                return true;
            break;
    }
    return false;
}

/**
 * 获取字符串长度,支持汉字
 */
function utf8_strlen( $str )
{
  $count = 0;
  for( $i = 0; $i < strlen($str); $i++ )
  {
    $value = ord($str[$i]);
    if($value > 127)
    {
      if($value >= 192 && $value <= 223)
	$i++;
      elseif($value >= 224 && $value <= 239)
	$i = $i + 2;
      elseif($value >= 240 && $value <= 247)
	$i = $i + 3;
      else
	$i = strlen( $value );
    }
    $count++;
  }
  return $count;
}

/**
 * 验证输入的邮件地址是否合法
 *
 * @access  public
 * @param   string      $email      需要验证的邮件地址
 *
 * @return bool
 */
function is_email( $user_email )
{
  $chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,6}\$/i";
  if (strpos($user_email, '@') !== false && strpos($user_email, '.') !== false)
  {
    if (preg_match($chars, $user_email))
    {
      return true;
    }
    else
    {
      return false;
    }
  }
  else
  {
    return false;
  }
}

/**
 * 验证输入的QQ号是否合法
 * 
 * @param string $qq QQ号
 * @return boolean
 */
function is_qq( $qq )
{
  if( preg_match( '/^[1-9][0-9]{4,9}$/', $qq ) )
  {
    return TRUE;
  }
  else
  {
    return FALSE;
  }
}

/**
 * 验证输入的邮政编码是否合法
 * 
 * @param number string $zipcode
 * @return boolean
 */
function is_zipcode( $zipcode )
{
  if( preg_match( '/^\d{6}$/', $zipcode ) )
  {
    return TRUE;
  }
  else
  {
    return FALSE;
  }
}

/**
 * 验证输入的固定电话是否合法
 * 
 * @param string $telephone
 * @return boolean
 */
function is_telephone( $telephone )
{
  //3-4位区号，7-8位直播号码，1－4位分机号
  if( preg_match( '/^(0[0-9]{2,3}-)?([2-9][0-9]{6,7})(-\d{1,4})?$/', $telephone ) )
  {
    return TRUE;
  }
  else
  {
    return FALSE;
  }
}

/*
 * 验证输入的银行卡号是否合法
 * @param string $bank_accno
 * @return boolean
 */
function is_bankaccno( $bank_accno )
{
  //16到19位数字
  if( preg_match( '/^(\d{12,19})$/', $bank_accno ) )
  {
    return true;
  }
  else
  {
    return false;
  }
}

/**
 * 通过这个方法可以判断出，来访是否手机 
 * return bool
 */
function isMobileAgent()
{
  $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
  $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
  $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
  $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
  $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
  $regex_match.=")/i";
  return isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE']) or preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
}

function escapeSQLColumn( $sql )
{
  return "`{$sql}`";
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

function xml_encode( $name, $data )
{
  $str = "<?xml version='1.0' encoding='utf-8'?>\n";
  $str .= "<$name></$name>";
  $document = simplexml_load_string( $str );
  _xml_add_object( $document, $data );
  return $document->asXML();
}

function _xml_add_array( &$node, $name, $adata )
{
  foreach( $adata as $key => $data )
  {
    $child_node = $node->addChild( $name );
    _xml_add_object( $child_node, $data );
  }
}

function _xml_add_object( &$node, &$object )
{
  $vars = get_object_vars( $object );
  foreach( $vars as $name => $value )
  {
    if( is_object( $value ) )
    {
      $child_node = $node->addChild( $name );
      _xml_add_object( $child_node, $value );
    }
    else if( is_array( $value ) )
    {
      $array_node_name = $name;
      if( endsWith( $name, "s" ) )
      {
        $array_node_name = removeSurfix ( $name, "s" );
        $child_node = $node->addChild( $name );
      }
      else
      {
        $child_node = $node->addChild( $name."s" );
      }
      _xml_add_array( $child_node, $array_node_name, $value );
    }
    else
    {
      $node->addChild( $name, $value );
    }
  }
}

/**
 * 将原始图片缩放到指定尺寸保存
 * 
 * @param string $src_file 原始文件
 * @param string $target_file 目标文件
 * @param int $max_width 目标最大宽度
 * @param int $max_height 目标最大高度
 * @param boolean $canvas_flag 是否填充空白
 * @return \stdClass|boolean 失败，返回FALSE，否则返回stdClass包含 width height
 */
function save_and_resize_image( $src_file, $target_file, $max_width, $max_height, $canvas_flag = FALSE )
{
  try
  {

    if( ( $max_width == 0 ) && ( $max_height == 0 ) )
    {
      $ret = new stdClass();
      $imagick = new Imagick( $src_file );
      $ret->width = $imagick->getimagewidth();
      $ret->height = $imagick->getimageheight();
      $imagick->writeimage( $target_file );
      return $ret;
    }
    else if( ( $max_width == 0 ) || ( $max_height == 0 ) )
    {
      $ret = new stdClass();
      $imagick = new Imagick( $src_file );
      $imagick->thumbnailImage( $max_width, $max_height, false );
      $ret->width = $imagick->getimagewidth();
      $ret->height = $imagick->getimageheight();
      $imagick->writeimage( $target_file );
      return $ret;
    }
    else
    {
      $ret = new stdClass();
      $imagick = new Imagick( $src_file );
      $imagick->thumbnailImage( $max_width, $max_height, true );

      /* Create a canvas with the desired color */
      $canvas = new Imagick();
      $canvas->newImage( $max_width, $max_height, 'white', 'png' );

      /* Get the image geometry */
      $geometry = $imagick->getImageGeometry();

      /* The overlay x and y coordinates */
      $x = ( $max_width - $geometry['width'] ) / 2;
      $y = ( $max_height - $geometry['height'] ) / 2;

      /* Composite on the canvas  */
      $canvas->compositeImage( $imagick, imagick::COMPOSITE_OVER, $x, $y );

      $canvas->writeimage( $target_file );

      $ret->width = $max_width;
      $ret->height = $max_height;

      return $ret;
    }
  }
  catch(Exception $e)
  {
    var_dump( $e->getTraceAsString() );
    return FALSE;
  }
}




/**
 * 图片原始URL转为缩略图URL
 * 
 * @param string $url 原始图片URL
 * @param integer $width 缩略图宽度
 * @param integer $height 缩略图高度
 * @return string 缩略图URL
 */
function generateThumbUrl( $url, $width, $height )
{
  if ( empty( $url ) )
  {
    return "";
  }
  
  $info = pathinfo( $url );
  
  return $info[ "dirname" ]."/".$info[ "filename" ]."_{$width}x{$height}.".$info[ "extension" ];
}

/**
 * RC4 加密、解密
 * @param string $pwd 密码
 * @param string $data 原始数据
 * @return string 加密/解密之后的数据
 */
function rc4 ($pwd, $data)
{
  $key[] = "";
  $box[] = "";

  $pwd_length = strlen( $pwd );
  $data_length = strlen( $data );

  for ( $i = 0; $i < 256; $i++ )
  {
    $key[ $i ] = ord( $pwd[ $i % $pwd_length ] ); //ord返回字符串 string 第一个字符的 ASCII 码值。
    $box[ $i ] = $i;
  }

  for ( $j = $i = 0; $i < 256; $i++ )
  {
    $j = ($j + $box[ $i ] + $key[ $i ]) % 256;
    $tmp = $box[ $i ];
    $box[ $i ] = $box[ $j ];
    $box[ $j ] = $tmp;
  }

  $cipher = "";
  
  for ( $a = $j = $i = 0; $i < $data_length; $i++ )
  {
    $a = ($a + 1) % 256;
    $j = ($j + $box[ $a ]) % 256;

    $tmp = $box[ $a ];
    $box[ $a ] = $box[ $j ];
    $box[ $j ] = $tmp;

    $k = $box[ (($box[ $a ] + $box[ $j ]) % 256) ];
    $cipher .= chr( ord( $data[ $i ] ) ^ $k );
  }

  return $cipher;
}

/**
 * 生成随机数
 * @para int $length 要生成的长度
 * @para int $numeric 为空则返回字母与数字混合的随机数,不为空则纯数字
 *
 * @return string 返回生成的字串
 */
function random( $length, $numeric = 0 )
{
  PHP_VERSION < '4.2.0' ? mt_srand( (double) microtime() * 1000000 ) : mt_srand();
  $seed = base_convert( md5( print_r( $_SERVER, 1 ) . microtime() ), 16, $numeric ? 10 : 35  );
  $seed = $numeric ? ( str_replace( '0', '', $seed ) . '012340567890') : ( $seed . 'zZ' . strtoupper( $seed ) );
  $hash = '';
  $max = strlen( $seed ) - 1;
  for ( $i = 0; $i < $length; $i++ )
  {
    $hash .= $seed[ mt_rand( 0, $max ) ];
  }
  return $hash;
}



/**
 * 根据生日计算年龄的函数  生日格式必须为 1989-01-01  这种格式
 * @param type $birthday
 * @return type
 */
function getAgeByBirthday( $birthday )
{
  list( $by, $bm, $bd ) = explode( '-', $birthday );
  $cm = date('n');
  $cd = date('j');
  $age = date('Y')- $by - 1;
  if( $cm > $bm || ( $cm == $bm && $cd >= $bd ) )
  {
    $age++;
  }
  if( $age < 0 )
  {
    $age = 0;
  }
  return $age;
}



/**
 * 判断是否是微信浏览器
 * @return boolean
 */
function is_weixin()
{
  if( strpos( $_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false ) 
  {
    return true;
  }	
  return false;
}

/**
 * 判断是否是支付宝服务窗的函数
 */
function is_alipay()
{
  if( strpos( $_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false ) 
  {
    return true;
  }	
  return false;
}


/**
 * 判断某个字符串是否是时间类型的字符串
 * @param type $date_str
 * @param type $date_format
 */
function isDate( $date_str, $date_format = "Y-m-d H:i:s" )
{
  $date_time = strtotime( $date_str );
  if( !$date_time || date( $date_format, $date_time ) != $date_str )
  {
    return FALSE;
  }
  return TRUE;
}

function isIdNo( $id_no )
{
//  $idno_ret = "/(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/";
//  if( preg_match( $idno_ret, $id_no ) )
//  {
//    return TRUE;
//  }
//  return FALSE;
  if( strlen( $id_no ) == 18 ) //如果是18位的身份证，那么直接验证是否正确
  {
    return idcard_checksum18( $id_no );
  }
  else if( ( strlen( $id_no ) == 15 ) ) //如果是15位的身份证，那么先转换成18位的，然后在验证18位的是否正确
  {
    $id_no = idcard_15to18( $id_no ); //将15位的转换成18位的
    return idcard_checksum18( $id_no );
  }
  else
  {
    return false;
  }
}

/**
 * 计算身份证校验码，根据国家标准GB 11643-1999
 * @param type $idcard_base
 * @return boolean|string
 */
function idcard_verify_number( $idcard_base )
{
  //如果传送进来的不是17位，那么直接返回FALSE
  if( strlen( $idcard_base ) != 17 )
  {
    return false;
  }
  //加权因子
  $factor = array( 7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2 );
  //校验码对应值
  $verify_number_list = array( '1','0','X','9','8','7','6','5','4','3','2' );
  $checksum = 0;
  for( $i = 0; $i < strlen( $idcard_base ); $i++ )
  {
    $checksum += substr( $idcard_base, $i, 1 ) * $factor[ $i ];
  }
  $mod = $checksum % 11;
  $verify_number = $verify_number_list[ $mod ];
  return $verify_number;
}

/**
 * 将15位身份证升级到18位
 * @param type $idcard
 * @return boolean|string 
 */
function idcard_15to18( $idcard )
{
  if( strlen( $idcard ) !=15 )
  {
    return false;
  }
  else
  {
    // 如果身份证顺序码是996 997 998 999，这些是为百岁以上老人的特殊编码
    if( array_search( substr( $idcard, 12, 3 ), array( '996','997','998','999' ) ) !== false)
    {
      $idcard = substr( $idcard, 0, 6 ) . '18' . substr( $idcard, 6, 9 );
    }
    else
    {
      $idcard = substr( $idcard, 0, 6 ) . '19' . substr( $idcard, 6, 9 );
    }
  }
  $idcard = $idcard . idcard_verify_number( $idcard );
  return $idcard;
}

/**
 * 18位身份证校验码有效性检查
 * @param String $idcard 身份证号码
 */
function idcard_checksum18( $idcard )
{
  if( strlen( $idcard ) != 18 )
  {
    return false;
  }
  //判断是否全部是数字和Xx组成
  if( !preg_match( "/^[0-9]{17}(X|x)|[0-9]{18}$/", $idcard ) )
  {
    return false;
  }
  $idcard_base = substr( $idcard, 0, 17 );
  if( idcard_verify_number( $idcard_base ) != strtoupper( substr( $idcard, 17, 1 ) ) )
  {
    return false;
  }
  else
  {
    return true;
  }
}

/**
 * 返回隐藏生日位数的身份证号码
 * 
 * @param numeric_string $idcard 身份证号码
 * @param string $cover_with 用什么类型的字符替换，默认是*号
 * @return string
 */
function idCardCover( $idcard, $cover_with = '*' )
{
  $id_card = trim( $idcard );
  if( !is_numeric( $id_card ) )
  {
    return $id_card;
  }
  if( strlen( $id_card ) == 18 )
  {
    return substr( $id_card, 0, 10 ).$cover_with.$cover_with.$cover_with.$cover_with.substr( $id_card, -4 );
  }
  else if( strlen( $id_card ) == 15 )
  {
    return substr( $id_card, 0, 8 ).$cover_with.$cover_with.$cover_with.$cover_with.substr( $id_card, -3 );
  }
  else
  {
    $_cover_with = '';
    if( strlen( $id_card ) > 15 )
    {
      //非18、15位，隐藏后面10位
      for( $i = 0; $i < 10; $i++ )
      {
        $_cover_with .= $cover_with;
      }
      return substr( $id_card, 0, strlen( $id_card ) - 10 ).$_cover_with;
    }
    return $id_card;
  }
}

/**
 * 生成随机字符串
 * @param $length 要生成的随机字符串的长度
 */
function gen_nonce_str( $length )
{
  $base_str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
  $return_str = "";
  for( $i = 0; $i < $length; $i++ )
  {
    $r_number = rand( 0, 35 );
    $random_str = substr( $base_str, $r_number, 1 );
    $return_str .= $random_str;
  }

  return $return_str;
}


function my_curl( $url, $data = FALSE, $header = false )
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  if( $data )
  {
    // post数据
    curl_setopt($ch, CURLOPT_POST, 1);
    // post的变量
    curl_setopt($ch, CURLOPT_POSTFIELDS,  $data );
  }
  
  //设置请求头格式
  if( $header !== false )
  {
    curl_setopt( $ch, CURLOPT_HTTPHEADER, $header );
  }
  
  $ssl = substr( $url, 0, 8 ) == "https://" ? TRUE : FALSE;
  if( $ssl )
  {
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 1 );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
  }
  
  $output = curl_exec($ch);
  curl_close( $ch );
  return $output;
}


/**
 * 根据身份证来获取用户的信息
 * return 性别 gender
 *        生日 birthday
 */
function getUserInfoByIdno($idno)
{
  if( !isIdNo( $idno ) )
  {
    return false;
  }
  if( strlen( $idno ) == 15 )  //如果是15位的身份证
  {
    //获取出生日期  是7位到12位之间
    $birthday_str = "19" . substr( $idno, 6, 6 );
    //获取性别判断位
    $gender_number = substr( $idno, -1 );
  }
  else if( strlen( $idno ) == 18 )  //如果是18位的身份证
  {
    //获取出生日期  是7位到14位之间
    $birthday_str = substr( $idno, 6, 8 );
    //获取性别判断位
    $gender_number = substr( $idno, -2, 1 );
  }
  $gender = 1; // 女
  if( $gender_number % 2 == 1 )
  {
    $gender = 0; // 男
  }
  return array(
      'birthday' => date('Y-m-d', strtotime( $birthday_str ) ),
      'gender' => $gender,
  );
}

class MySftp
{
  private $conn = NULL;
  private $sftp = NULL;
  
  public function __construct( $host, $port )
  {
    //连接服务器
    $this->conn = ssh2_connect( $host, $port );
    if( !$this->conn )
    {
      throw new Exception( 'Cannot connect to server' );
    }
    
    //验证服务器指纹（略过）
  }
  
  public function __destruct()
  {
    $this->disconnect();
  }
  
  /**
   * 使用用户名+密码连接sftp服务器并初始化sftp使用环境
   * 
   * @param string $user_name 用户名
   * @param string $password 密码
   * @throws Exception
   */
  public function authByPassword( $user_name, $password )
  {
    //授权
    $auth = ssh2_auth_password( $this->conn, $user_name, $password );
    if( !$auth )
    {
      throw new Exception( 'Authorize failed' );
    }
    //初始化sftp子系统
    $this->sftp = @ssh2_sftp( $this->conn );
    if( !$this->sftp )
    {
      throw new Exception( 'Initialize SFTP subsystem failed' );
    }
  }
  
  /**
   * 下载远程文件
   * 
   * @param string $remote_file_name 远程文件全名（即包括绝对路径）
   * @param string $local_file_name 本地文件全名（即包括绝对路径）
   * @return boolean TRUE 下载成功 FALSE 失败
   */
  public function download( $remote_file_name, $local_file_name )
  {
    try
    {
      $sftp = $this->sftp;
      $ssh2_file_name = "ssh2.sftp://$sftp".$remote_file_name;
      if( ! file_exists( $ssh2_file_name ) )
      {
        return FALSE;
      }
      $file_size = filesize( $ssh2_file_name );
      $stream = @fopen( $ssh2_file_name, 'r' );

      //方法一
      $contents = stream_get_contents( $stream );

      /**
      //方法二
      $contents = '';
      $read = 0;
      while ( ( $read < $file_size ) && ( $buf = fread( $stream, $file_size - $read ) ) )
      {
        $read += strlen( $buf );
        $contents .= $buf;
      }

      //方法三
      while ( !feof( $stream ) )
      {
        $contents .= fread( $stream, 8192 );
      }
      **/
      

      file_put_contents( $local_file_name, $contents );
      @fclose( $stream );
      
      //核对本地文件大小
      $local_size = filesize( $local_file_name );
      if( $file_size == $local_size )
      {
        return TRUE;
      }
      else
      {
        //如果由于网络原因或其他原因，只下载了部分，则将这部分文件要删掉重下
        unlink( $local_file_name );
        return FALSE;
      }
    }
    catch( Exception $ex )
    {
      return FALSE;
    }
  }
  
  /**
   * 上传本地文件
   * 
   * @param string $local_file_name 本地文件全名（即包括绝对路径）
   * @param string $remote_file_name 远程文件全名（即包括绝对路径）
   * @param boolean $remote_mkdir 是否有权限在远程服务器上创建目录，默认FALSE无权限
   * @return stdClass 属性flag为TRUE 上传成功 FALSE 失败
   */
  function upload( $local_file_name, $remote_file_name, $remote_mkdir = FALSE )
  {
    $ret = new stdClass();
    $ret->flag = FALSE;
    $ret->msg = '';
    
    //检查本地文件是否存在
    if( ! file_exists( $local_file_name ) )
    {
      $ret->msg = 'local file '.$local_file_name.' not exists';
      return $ret;
    }
    
    $sftp = $this->sftp;
    //检查远程文件目录是否存在
    $remote_pathinfo = pathinfo( $remote_file_name );
    $remote_file_dir = $remote_pathinfo[ 'dirname' ];
    $ssh2_remote_file_dir = "ssh2.sftp://$sftp".$remote_file_dir;
    if( ! file_exists( $ssh2_remote_file_dir ) )
    {
      if( $remote_mkdir )
      {
        //不存在创建
        if( ! @ssh2_sftp_mkdir( $sftp, $remote_file_dir ) )
        {
          $ret->msg = 'remote dir '.$remote_file_dir.' create fail';
          return $ret;
        }
      }
      else
      {
        $ret->msg = 'remote dir '.$remote_file_dir.' not exists';
        return $ret;
      }
    }
    
    //上传
    $data_to_send = @file_get_contents( $local_file_name );
    if( $data_to_send === FALSE )
    {
      $ret->msg = 'local file '.$local_file_name.' could not open';
      return $ret;
    }
    $stream = @fopen( "ssh2.sftp://$sftp".$remote_file_name, 'w' );
    if( $stream === FALSE )
    {
      $ret->msg = 'remote file '.$remote_file_name.' could not open';
      return $ret;
    }
    if( @fwrite( $stream, $data_to_send ) === FALSE )
    {
      $ret->msg = 'could not send data from local file '.$local_file_name;
      return $ret;
    }
    @fclose( $stream );
    
    //TODO核对大小
    
    $ret->flag = TRUE;
    return $ret;
  }

  /**
   * 关闭连接
   */
  public function disconnect()
  {
    try
    {
      $data = $this->exec( 'echo "EXITING" && exit;' );
    }
    catch( Exception $ex )
    {
      
    }
    if( $this->conn )
    {
      unset( $this->conn );
    }
    if( $this->sftp )
    {
      unset( $this->sftp );
    }
    //echo $data;
  }
  
  /**
   * 返回ssh2_sftp连接resource
   * 
   * @return resource
   */
  public function getConnect()
  {
    return $this->conn;
  }
  
  /**
   * 返回sftp子系统资源句柄
   */
  public function getSftp()
  {
    return $this->sftp;
  }
  

  /**
   * 执行SSH命令并返回数据
   * 
   * @param string $cmd SSH命令
   * @return string
   * @throws Exception
   */
  public function exec( $cmd )
  {
    if( !( $stream = ssh2_exec( $this->conn, $cmd ) ) )
    { 
      throw new Exception( 'SSH command failed' ); 
    } 
    stream_set_blocking( $stream, true ); 
    $data = ""; 
    while( $buf = fread( $stream, 4096 ) )
    { 
      $data .= $buf; 
    } 
    fclose( $stream ); 
    return $data;
  }
  
  /**
   * 扫描远程sftp服务器的目录并返回所需的文件列表，支持只返回文件或只返回目录或都返回，不支持子目录下的文件扫描。
   * 
   * @param string $dir_arg 目录，必须是绝对路径
   * @param enum_string $return_mode 返回对象类型，可选值：FILE 只返回文件、DIR 只返回目录、ALL 都返回
   * @param array $file_exts 只返回哪些扩展名的文件
   * @return array
   */
  public function scanRemoteDir( $dir_arg, $return_mode = 'FILE', $file_exts = array() )
  {
    //初始化返回数据
    $return_files = array();
    $return_files[ 'file_names' ] = array();
    $return_files[ 'file_full_names' ] = array();
    
    $remote_dir_separator = '/';
    $dir = $dir_arg;
    while ( endsWith( $dir, $remote_dir_separator ) )
    {
      $dir = removeSurfix( $dir, $remote_dir_separator );
    }
    
    $sftp = $this->sftp;
    $ssh2_dir = "ssh2.sftp://$sftp".$dir;
    
    $file_names = array();
    $file_full_names = array();
    $handle = @opendir( $ssh2_dir );
    //如果目录不存在则直接返回空数组，不抛异常
    if( !$handle )
    {
      return $return_files;
    }
    
    while( FALSE !== ( $file_name = readdir( $handle ) ) )
    {
      if( substr( "$file_name", 0, 1 ) != "." )
      {
        $ssh2_file = $ssh2_dir.$remote_dir_separator.$file_name;
        $file_full_name = $dir.$remote_dir_separator.$file_name;
        if( is_dir( $ssh2_file ) )
        {
          if( ( $return_mode != 'FILE' ) && ( count( $file_exts ) == 0 )  )
          {
            $file_full_names[] = $file_full_name;
            $file_names[] = $file_name;
          }
        }
        else
        {
          if( $return_mode != 'DIR' )
          {
            if( count( $file_exts ) > 0 )
            {
              $pathinfo = pathinfo( $file_full_name );
              if( in_array( $pathinfo[ 'extension' ], $file_exts ) || in_array( strtolower( $pathinfo[ 'extension' ] ), $file_exts )
                      || in_array( strtoupper( $pathinfo[ 'extension' ] ), $file_exts ) )
              {
                $file_full_names[] = $file_full_name;
                $file_names[] = $file_name;
              }
            }
            else
            {
              $file_full_names[] = $file_full_name;
              $file_names[] = $file_name;
            }
          }
        }
      }
    }
    closedir( $handle );
    
    sort( $file_names );
    sort( $file_full_names );
    
    $return_files[ 'file_names' ] = $file_names;
    $return_files[ 'file_full_names' ] = $file_full_names;
    return $return_files;
  }
  
  /**
   * 获取远程文件大小
   * 
   * @param string $file_name 文件全名
   * @return int bytes
   */
  public function getFileSize( $file_name )
  {
    $sftp = $this->sftp;
    $ssh2_file_name = "ssh2.sftp://$sftp".$file_name;
    return filesize( $ssh2_file_name );
  }
}

/**
 * 解压缩zip文件
 * 
 * @param string $zip_file zip压缩文件全名（即包括绝对路径）
 * @param string $destination_file_dir 解压到哪个目标目录，如果为NULL，表示解压到zip文件的当前文件夹
 * @param string $destination_file_ext 解压出来的文件的扩展名
 * @return boolean|string
 * @throws Exception
 */
function unpackZipFile( $zip_file, $destination_file_dir = NULL, $destination_file_ext = '' )
{
  if( ! file_exists( $zip_file ) )
  {
    throw new Exception( 'zip file: '.$zip_file.' not exists!' );
  }
  //获取原压缩文件信息
  $zip_file_info = pathinfo( $zip_file );
  if( empty( $destination_file_dir )  )
  {
    $destination_file_dir = $zip_file_info[ 'dirname' ];
  }
  if( ! empty( $destination_file_ext ) )
  {
    $destination_file_ext = ".".$destination_file_ext;
  }
  
  $zip = new ZipArchive();
  if( $zip->open( $zip_file ) === TRUE )
  {
    $ret = $zip->extractTo( $destination_file_dir );
    $zip->close();
    
    $file_name = $zip_file_info[ 'filename' ];
    $file_full_name = $zip_file_info[ 'dirname' ].DIRECTORY_SEPARATOR.$file_name.$destination_file_ext;
    
    if( $ret == FALSE )
    {
      @unlink( $file_full_name );
      return FALSE;
    }

    //检查下解压后的文件是否存在，存在才返回解压后的文件的文件名
    if( file_exists( $file_full_name ) )
    {
      return $file_full_name;
    }
  }
  return FALSE;
}

/**
 * 解压含有多个文件(不包括文件夹及子文件夹)的zip文件
 * 
 * @param string $zip_file zip文件
 * @param string $destination_dir 解压到
 * @param boolean $to_folder 解压时是否将所有文件解压到文件夹
 * @return mixed 如果解压成功会返回所有解压出来的文件名(数组)，失败返回FALSE
 * @throws Exception 会有异常抛出，需要做捕获处理
 */
function unpackMultiFileZip( $zip_file, $destination_dir = NULL, $to_folder = FALSE )
{
  if( ! file_exists( $zip_file ) )
  {
    throw new Exception( 'zip file: '.$zip_file.' not exists' );
  }
  
  $zip_pathinfo = pathinfo( $zip_file );
  //如果没传解压目录就用压缩文件所在目录
  if( empty( $destination_dir ) )
  {
    $destination_dir = $zip_pathinfo[ 'dirname' ];
  }
  if( $to_folder )
  {
    $destination_dir .= DIRECTORY_SEPARATOR.$zip_pathinfo[ 'filename' ];
  }
  
  $zip = new ZipArchive();
  if( $zip->open( $zip_file ) === TRUE )
  {
    $ret = $zip->extractTo( $destination_dir );
    $zip->close();
    
    //获取zip压缩包里文件名称列表作为返回的解压后的文件名列表
    return getFilesNameFromZip( $zip_file, $destination_dir );
  }
  return FALSE;
}

/**
 * 返回只包含文件的zip文件中的所有文件的名字
 * 
 * @param string $zip_file zip文件全路径文件名
 * @param string $pre_dir 返回的文件名称可以带路径前缀
 * @return mixed 发送异常返回FALSE 否则返回array
 */
function getFilesNameFromZip( $zip_file, $pre_dir = FALSE )
{
  $zip_handle = @ zip_open( $zip_file );
  if( ! is_resource( $zip_handle ) )
  {
    return FALSE;
  }
  
  if( !empty( $pre_dir ) )
  {
    while( endsWith( $pre_dir, DIRECTORY_SEPARATOR ) )
    {
      $pre_dir = removeSurfix( $pre_dir, DIRECTORY_SEPARATOR );
    }
  }
  $files_name = array();
  while( is_resource( $zip_entry = zip_read( $zip_handle ) ) )
  {
    $files_name[] = empty( $pre_dir ) ? zip_entry_name( $zip_entry ) : $pre_dir.DIRECTORY_SEPARATOR.zip_entry_name( $zip_entry );
  }
  @zip_close( $zip_handle );
  return $files_name;
}

/**
 * 添加一段字符串到新建的文件并压缩到zip文件（注意：zip压缩包里只有1个文件，如果要压缩多个字符串用函数packMultiStringToNewZip()）
 * 注意生成的zip是重新生成的，不会使用已存在的zip文件。
 * 
 * @param string $zip_file 目标zip文件（包括全路径+文件名）
 * @param string $content 待添加到zip文件的内容
 * @param string $content_file_ext 可选参数，添加到zip文件里的文件的扩展名，不传则无扩展名，前面可以带点.也可以不带
 * @param string $content_file_name 可选参数，添加到zip文件里的文件的名字，不传则与zip文件名相同
 * @return mixed 成功返回生成的zip文件名（包括全路径）、失败返回FALSE、出现错误则抛异常
 * @throws Exception
 */
function packStringToNewZip( $zip_file, $content, $content_file_ext = '', $content_file_name = '' )
{
  //检查目标zip文件所在的目录是否存在，不存在则要创建
  $pathinfo = pathinfo( $zip_file );
  $dirname = $pathinfo[ 'dirname' ];
  if( !is_dir( $dirname ) && !mkdir( $dirname, 0775, TRUE ) )
  {
    throw new Exception( 'dir '.$dirname.' create failed!' );
  }
  
  //删掉已存在的zip文件
  if( file_exists( $zip_file ) )
  {
    @ unlink( $zip_file );
  }
  
  //生成压缩文件
  $zip = new ZipArchive();
  if( $zip->open( $zip_file, ZipArchive::CREATE ) !== TRUE )
  {
    throw new Exception( 'cannot open '.$zip_file.' !' );
  }
  $entry_file_name = empty( $content_file_name ) ? $pathinfo[ 'filename' ] : $content_file_name;
  if(  !empty( $content_file_ext ) )
  {
    $entry_file_name = startsWith( $content_file_ext, '.' ) ? $entry_file_name.$content_file_ext : $entry_file_name.'.'.$content_file_ext;
  }
  $zip->addFromString( $entry_file_name, $content );

  //close前先取出所有属性
  $status = $zip->status;
  //$status_sys = $zip->statusSys;
  $num_files = $zip->numFiles;
  $filename = $zip->filename;
  //$comment = $zip->comment;

  $zip->close();

  if( ( $num_files > 0 ) && ( $status == 0 ) )
  {
    return $filename;
  }
  return FALSE;
}

/**
 * 添加多个字符串(生成文件后)到压缩文件
 * 函数packStringToNewZip()是添加单个字符串
 * 
 * @param string $zip_file 目标zip压缩文件
 * @param array $multi_string_array 字符串数组，格式 文件名 => 内容
 * @return mixed 成功则返回生成的zip文件的全路径文件名，失败返回FALSE 
 * @throws Exception 如果有异常会抛出
 */
function packMultiStringToNewZip( $zip_file, $multi_string_array )
{
  //检查目标zip文件所在的目录是否存在，不存在则要创建
  $pathinfo = pathinfo( $zip_file );
  $dirname = $pathinfo[ 'dirname' ];
  if( !is_dir( $dirname ) && !mkdir( $dirname, 0775, TRUE ) )
  {
    throw new Exception( 'dir '.$dirname.' create failed' );
  }
  
  //删掉已存在的zip文件
  if( file_exists( $zip_file ) )
  {
    @ unlink( $zip_file );
  }
  
  //生成压缩文件
  $zip = new ZipArchive();
  if( $zip->open( $zip_file, ZipArchive::CREATE ) !== TRUE )
  {
    throw new Exception( 'cannot open '.$zip_file );
  }
  foreach( $multi_string_array as $file_name => $content )
  {
    $add_ret = $zip->addFromString( $file_name, $content );
    if( $add_ret == FALSE )
    {
      throw new Exception( 'zip cannot add file '.$file_name );
    }
  }
  
  //close前先取出所有属性
  $status = $zip->status;
  //$status_sys = $zip->statusSys;
  $num_files = $zip->numFiles;
  $filename = $zip->filename;
  //$comment = $zip->comment;

  $zip->close();
  
  if( ( $num_files == count( $multi_string_array ) ) && ( $status == 0 ) )
  {
    return $filename;
  }
  //删除数据不完整的的zip
  if( file_exists( $filename ) )
  {
    @ unlink( $filename );
  }
  return FALSE;
}

/**
 * 添加一个文件到新建的zip文件
 * 注意生成的zip是重新生成的，不会使用已存在的zip文件。
 * 
 * @param string $source_file 待添加到zip的源文件（包括全路径）
 * @param string $target_zip_file 待生成的目标zip文件名（包括全路径），默认为空字符串表示在源文件所在目录生成同名zip文件
 * @param string $entry_file_name 是否用新的文件名打包到zip文件，默认为空字符串表示与源文件名相同
 * @return mixed 成功会返回生成的zip文件（包含全路径）、失败返回FALSE
 * @throws Exception
 */
function packFileToNewZip( $source_file, $target_zip_file = '', $entry_file_name = '' )
{
  //检查源文件是否存在
  if( ! file_exists( $source_file ) )
  {
    throw new Exception( 'file '.$source_file.' not exists!' );
  }
  
  $source_info = pathinfo( $source_file );
  if( empty( $target_zip_file ) )
  {
    $target_zip_file = $source_info[ 'dirname' ].DIRECTORY_SEPARATOR.$source_info[ 'filename' ].'.zip';
  }
  //检查目标zip文件所在的目录是否存在，不存在则要创建
  $targer_info = pathinfo( $target_zip_file );
  $dirname = $targer_info[ 'dirname' ];
  if( !is_dir( $dirname ) && !mkdir( $dirname, 0775, TRUE ) )
  {
    throw new Exception( 'dir '.$dirname.' create failed!' );
  }
  
  //删掉已存在的zip文件
  if( file_exists( $target_zip_file ) )
  {
    @ unlink( $target_zip_file );
  }
  
  //生成压缩文件
  $zip = new ZipArchive();
  if( $zip->open( $target_zip_file, ZipArchive::CREATE ) !== TRUE )
  {
    throw new Exception( 'cannot open '.$target_zip_file.' !' );
  }
  $_entry_file_name = empty( $entry_file_name ) ? $source_info[ 'basename' ] 
          : $entry_file_name.( empty( $source_info[ 'extension' ] ) ? '' : '.'.$source_info[ 'extension' ] );
  $zip->addFile( $source_file, $_entry_file_name );
  
  //close前先取出所有属性
  $status = $zip->status;
  //$status_sys = $zip->statusSys;
  $num_files = $zip->numFiles;
  $filename = $zip->filename;
  //$comment = $zip->comment;

  $zip->close();

  if( ( $num_files > 0 ) && ( $status == 0 ) )
  {
    return $filename;
  }
  return FALSE;
}



/**
 * 获取html中的标签内容
 * 
 * @param string $html html格式文本
 * @param string $tag_star_regex 标签前缀正则表达式，比如<div class="content"的正则表达式："/<div[^<>]+class*\=*[\"']?content[\"']?/"
 * @param string $tag_name	标签名称，比如："div"
 * @return boolean|string
 */
function getHtmlTagContent( $html, $tag_star_regex, $tag_name )
{
  $html_star = array();
  $pre_matches = array();
  $suf_matches = array();
  preg_match( $tag_star_regex, $html, $html_star, PREG_OFFSET_CAPTURE );
  if( !isset( $html_star[0][1] ) )
  {
    return false;
  }
  $html = substr( $html, $html_star[0][1] );  //从起始位置开始截取内容
  preg_match_all( "/<" . $tag_name . "/i", $html, $pre_matches, PREG_OFFSET_CAPTURE );    //获取所有tag前缀
  preg_match_all( "/<\/" . $tag_name . ">/i", $html, $suf_matches, PREG_OFFSET_CAPTURE ); //获取所有tag后缀
  
  //根据标签的前缀与后缀一对一的关系，
  //同一个下标数组中的前缀和后缀的标签位置进行对比，
  //当前缀的位置比上一个后缀的位置大的时候，则是一个新的标签
  //即上一个小标的后缀位置即该函数所要获取的内容
  if( count( $pre_matches[0] ) >= count( $suf_matches[0] ) )
  {
    $loop_matches = $suf_matches[0];
  }
  else
  {
    $loop_matches = $pre_matches[0];
  }
  foreach( $loop_matches as $index => $tag_pos )
  {
    if ( $index > 0 && $pre_matches[0][$index][1] > $suf_matches[0][$index - 1][1] )
    {
      $end_pos = $suf_matches[0][$index - 1][1];
      $end_pos += strlen( $tag_name ) + 3;
      $html = substr( $html, 0, $end_pos );  //截取内容
      return $html;
    }
  }
  if( count( $pre_matches[0] ) > 0  && count( $suf_matches[0] ) > 0 && count( $pre_matches[0] ) == count( $suf_matches[0] ) )
  {
    $t_pos = end( $suf_matches[0] );
    $end_pos = $t_pos[1];
    $end_pos += strlen( $tag_name ) + 3;
    $html = substr( $html, 0, $end_pos );  //截取内容
    return $html;
  }
  else
  {
    return false;
  }
}

/**
 * 获取时间，精确小数点后4位
 * @return type
 */
function get_micro_time()
{
  list( $us_time, $s_time ) = explode( " ", microtime() );
  return bcadd( $s_time, $us_time, 4 );
}

/**
* 短连接生成器
* @param type $url
* @return string
*/
function generateShortUrlId( $url )
{
 $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
 $key = "012345789ABEF)T)$@)@#GHIJKSTUVWdefghijklmyz";
 $urlhash = md5( $key . $url );
 $len = strlen( $urlhash );

 #将加密后的串分成4段，每段4字节，对每段进行计算，一共可以生成四组短连接
 for( $i = 0; $i < 4; $i++ )
 {
   $urlhash_piece = substr( $urlhash, $i * $len / 4, $len / 4 );
   #将分段的位与0x3fffffff做位与，0x3fffffff表示二进制数的30个1，即30位以后的加密串都归零
   $hex = hexdec( $urlhash_piece ) & 0x3fffffff; #此处需要用到hexdec()将16进制字符串转为10进制数值型，否则运算会不正常
   $short_url = "";
   #生成6位短连接
   for( $j = 0; $j < 6; $j++ )
   {
     #将得到的值与0x0000003d,3d为61，即charset的坐标最大值
     $short_url .= $charset[ $hex & 0x0000003d ];
     #循环完以后将hex右移5位
     $hex = $hex >> 5;
   }

   $short_url_list[] = $short_url;
 }

 return $short_url_list;
}

?>