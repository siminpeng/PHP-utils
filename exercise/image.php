<?php

/* 
 * 简单的PHP图片合成的方法
 */

//使用PHP的方法将头像和名字整到背景图片上
function picturetest( $user_image_path = null, $username = null )
{
  $bg_image_path = './picture/bg-img.jpg';//背景图片  
  $user_image_path = './picture/touxiangm.png';//头像图片
  $username = "小甜甜";//用户名
  if( !$username )
  {
    throw new Exception( '用户姓名错误' );
  }
  //背景
  $bg_image = imagecreatefromstring( file_get_contents( $bg_image_path ) );  
  if( !$bg_image )
  {
    throw new Exception( '背景图片错误' );
  }
  
  imagesavealpha( $bg_image, true );
  
  //添加名字
  $size = 20;//文字大小
  $name_x = 205;
  $name_y = 280;
  $textcolor = ImageColorAllocate( $bg_image, 209, 150, 150 ); //粉色   
  $font = "./picture/simhei.ttf";
  imagettftext( $bg_image, $size, 0, $name_x, $name_y, $textcolor, $font, $username );
  
  //头像
  $bg_image = yuan_img( $bg_image, $user_image_path );
//  return $bg_image;
  //输出合成图片到文件 
//  var_dump( imagepng( $bg_image,'./picture/merge.png' ));
  imagedestroy( $bg_image );  
 }
 
 function yuan_img( $bg_image,$imgpath ) 
{
  $src_img = imagecreatefromstring( file_get_contents( $imgpath ) ); 
  if( !$src_img )
  {
    throw new Exception( '头像图片错误' );
  }
  //图片的宽度
  $wh = getimagesize( $imgpath );
  $w = $wh[ 0 ];
  $h = $wh[ 1 ];
  $w = min( $w, $h );
  
  //头像的大小
  $use_h = 110;
  //创建画布
  $img = imagecreatetruecolor( $use_h, $use_h );
  imagesavealpha( $img, true );
  //拾取一个完全透明的颜色,最后一个参数127为全透明
  $bg = imagecolorallocatealpha( $img, 255, 255, 255, 127 );
  imagefill( $img, 0, 0, $bg );
  imagecopyresampled( $img, $src_img, 0, 0, 0, 0, $use_h, $use_h, $w, $w );

  $r = $use_h / 2; //圆半径
  $y_x = $r; //圆心X坐标
  $y_y = $r; //圆心Y坐标
  for ($x = 0; $x < $use_h; $x++) 
  {
    for ($y = 0; $y < $use_h; $y++) 
    {
      $rgbColor = imagecolorat( $img, $x, $y);
      if ( ( ( ( $x - $r ) * ( $x - $r ) + ( $y - $r ) * ( $y - $r ) ) < ( $r * $r ) ) ) 
      {
        imagesetpixel( $bg_image, $x + 48, $y + 256, $rgbColor );
      }
    }
  }
  imagedestroy( $src_img );
  imagedestroy( $img );
  return $bg_image;
}

/************************************************************************************************/
/*效率比较高的方法*/
/************************************************************************************************/
/*
 * 先下载图片到本地，再执行
 * $bg_path:背景图片路径
 * $user_img_path 头像图片路径
 * $user_name :用户昵称
 * $target：生成图片保存路径，包括文件名
 * ps: 需要修改字体ttf的路径
 */
function getposter( $bg_path, $user_img_path, $user_name, $target )
{
  if( !$user_name )
  {
    throw new Exception( '用户名不能为空' );
  }
  //判断路径是否存在
  if( ! file_exists( $bg_path ) )
  {
    throw new Exception( '图片路径错误' );
  }
  if( !$target )
  {
    throw new Exception( '保存路径错误' );
  }
  //下载头像到本地
  $content = file_get_contents( $user_img_path );
  if( !$content )
  {
    throw new Exception( '下载头像失败' );
  }
  $random_id = generateID();
  $head_path = "C:/workspace/naturade/www/wwwroot/admin/files/picture/";
  $head_path = $head_path . urlencode( $user_name ) . $random_id . ".png";
  file_put_contents( $head_path, $content );
  if( !file_exists( $head_path ) )
  {
    throw new Exception( '头像图片下载错误' );
  }

  //头像
  $imagick = new Imagick( $head_path );
  $imagick->thumbnailImage( 110, 110, true );
  $imagick->stripImage();
  $imagick->roundCorners($imagick->getImageWidth() / 2, $imagick->getImageHeight() / 2);

  //名字
  $draw = new ImagickDraw();  
  $draw->setFillColor(new ImagickPixel('#d19696'));  //文字的颜色
  $draw->setFontSize( 30 );  
  $draw->setFont( 'C:/workspace/naturade/www/wwwroot/admin/files/picture/simhei.ttf' );
  $draw->annotation(205, 280, $user_name);//合成的坐标和文字
  $draw->setTextEncoding( 'UTF-8' );

  //背景图 
  $poster = new Imagick( $bg_path );
  $poster->setImageFormat( 'JPEG' );                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
  $poster->drawImage( $draw );  

  //头像 背景 合成
 //    $poster->compositeImage($Qrcode,Imagick::COMPOSITE_OVER,275,960);
  $poster->compositeImage( $imagick, Imagick::COMPOSITE_OVER, 48, 256 );//合成的位置坐标
 //    $poster->setImageCompressionQuality(60); //压缩质量

  //输出图片
  $poster->writeimage( "C:/workspace/naturade/www/wwwroot/admin/files/picture/merge.jpg" );
 //    $poster->writeimage( $target );
  //删除头像
  unlink ( $head_path ); 
  $draw->destroy();
  $imagick->destroy();
  $poster->destroy();
}