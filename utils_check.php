<?php

/* 
 * 2017-10-25
 * php 常用的检测字符串格式的方法
 * 检测是否是 邮件地址，电话号码、QQ号，邮编、身份证号等
 * 其他字符串方法：根据身份证号码获取出生日期
 */

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

/**
 * 是否是电话号码
 * @param type $mobile
 * @return boolean
 */
function is_mobile( $mobile ) 
{
  if ( strlen( $mobile ) != 11 || !is_numeric( $mobile ) ) {
    return false;
  }
  return preg_match( "/^1[3,4,5,7,8]\d{9}$/", $mobile );
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

/**
 * 检查是否是身份证号码
 * @param type $id_no
 * @return boolean
 */
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




