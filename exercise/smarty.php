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
$smarty -> left_delimiter = "{"; //左定界符 
$smarty -> right_delimiter = "}"; //右定界符 

//变量
$smarty -> assign('test','改了一下限定符号'); 

//数组变量
$contacts = array('fax'=>'555-222-9876',
                   'email'=>'fhrj@fhdj.com',
                   'other'=>array( 'a'=>'aa','b'=>'bb'));
$smarty->assign('contacts', $contacts);

//数组下标
$contacts_back = array( '1057585926','18253590634');
$smarty->assign('contacts_back',$contacts_back);

//对象
$obj = new stdClass();
$obj->name = '张三';
$obj->age = '24';
$smarty->assign('obj',$obj);

//变量修饰器
$abc = 'abc';
$smarty->assign('abc',$abc);

//第一个首字母大写capitalize
$smarty->assign('articleTitle', 'next x-men film, x3, delayed.');

$smarty->assign('articleTitlea', 'Smokers are Productive, but Death Cuts Efficiency.');

$smarty->assign('to',10);
$smarty->assign('start',8);

$smarty -> display('test.html'); 



