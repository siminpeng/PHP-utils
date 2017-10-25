<?php

/* 
 * 2017-10-25 其他常用的工具类
 */

/******************************************数组***************************************************/
function arrayIsSet( $array_info, $name )
{
  if( !isset( $array_info[ $name ] ) )
    return false;
  return ( strlen( $array_info[ $name ]) > 0 );
}

/**
 * 数组比较
 * @param type $array1
 * @param type $array2
 * @return boolean
 */
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

/******************************************生成id***************************************************/
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

/******************************************字符串***************************************************/
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



/*************************xml*****************************/

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



/******************************************RC4 加密、解密***************************************************/
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



