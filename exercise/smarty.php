<?php
/* 
 * smarty的使用相关
 */

/*
 * 安装
 * 1.解压后将目录中的libs目录重命名为smarty，复制到网站目录，
 * 2.在网站根目录下建立templates和templates_c两个目录 
 * 3.在templates目录下新建test.html 
 */
//包含Smarty.class.php 文件
include_once $_SERVER['DOCUMENT_ROOT'] . '/Smarty/Smarty.class.php';

$smarty = new Smarty();
$smarty -> template_dir = $_SERVER['DOCUMENT_ROOT']."/smartytest/templates"; //模板存放目录 
$smarty -> compile_dir = $_SERVER['DOCUMENT_ROOT']."/smartytest/templates_c"; //模板存放目录 
$smarty -> left_delimiter = "{{"; //左定界符 
$smarty -> right_delimiter = "}}"; //右定界符 
$smarty -> assign('test','改了一下限定符号'); 
$smarty -> display('test.html'); 



