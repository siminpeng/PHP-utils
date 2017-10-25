<?php

/* 
 * 服务器的ip 相关.
 */

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

/*
 * curl方法
 */
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


