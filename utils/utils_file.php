<?php
/*
 * 2017-10-25
 * 文件和目录相关
 */

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

/**
* 生成目录树
*/

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


?>