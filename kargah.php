<?php
/**
 * @version 1
 * @package    joomla
 * @subpackage Project
 * @author	   	
 *  @copyright  	Copyright (C) 2014, . All rights reserved.
 *  @license 
 */

//--No direct access
defined('_JEXEC') or die('Resrtricted Access');
define('COM_PATH','components/com_kargah/');
include COM_PATH.'class/conf.php';
include COM_PATH.'class/mysql_class.php';
include COM_PATH.'class/htmlGenerator.php';
include COM_PATH.'class/xgrid.php';
include COM_PATH.'class/inc.php';
include COM_PATH.'class/jdf.php';
include COM_PATH.'class/audit_class.php';
include COM_PATH.'class/kargah_class.php';
include COM_PATH.'class/kargah_view_class.php';
include COM_PATH.'class/pay_class.php';
$document = JFactory::getDocument();
$document->addScript(COM_PATH.'js/jquery.min.js');
$document->addScript(COM_PATH.'js/grid.js');
$document->addScript(COM_PATH.'js/jquery.ui.widget.js');
$document->addScript(COM_PATH.'js/jquery.fileupload.js');
$document->addScript(COM_PATH.'js/jquery.iframe-transport.js');
$document->addScript(COM_PATH.'js/cal/jalali.js');
$document->addScript(COM_PATH.'js/cal/calendar.js');
$document->addScript(COM_PATH.'js/cal/calendar-setup.js');
$document->addScript(COM_PATH.'js/cal/lang/calendar-fa.js');

//$document->addStyleSheet(COM_PATH.'css/bootstrap.min.css');
$document->addStyleSheet(COM_PATH.'css/xgrid.css');
$document->addStyleSheet(COM_PATH.'css/com_kargah.css');
$document->addStyleSheet(COM_PATH.'css/jquery.fileupload.css');
$document->addStyleSheet(COM_PATH.'js/cal/skins/aqua/theme.css');
$kargahs = kargah_class::loadActives();
//var_dump($kargahs);
$comman = (isset($_REQUEST['comman']) && trim($_REQUEST['comman'])!='')?$_REQUEST['comman']:'main';
if($comman == 'main' && isset($_REQUEST['RefId']))
	$comman = 'purchase';
if($comman == 'main')
{
	$out = kargah_view_class::main_view($kargahs);
	echo $out;
}
else if($comman == 'combo')
{
        $out='';
        $pishnahad=TRUE;
        if(isset($_POST['flname']))
        {
            $flname = trim($_REQUEST['flname']);
            $toz= strip_tags(trim($_REQUEST['toz']));
            if($flname!='' && $toz!='')
            {
                $pishnahad=FALSE;
                $my = new mysql_class;
                $tarikh= date("Y-m-d H:i:s");
                $my->ex_sqlx("insert into #__kargah_data (toz,en,tarikh,pishnehad) values ('$toz',-2,'$tarikh','$flname')");
                $out='<div class="alert alert-success" >پیشنهاد شما با موفقیت ثبت گردید</div>';
            }   
            else
                $out='<div class="alert alert-danger" >هر دو مورد نام ونام خانوادگی و توضیحات را وارد فرمایید</div>';
        }    
        $out .='<div class="alert alert-info" >جهت ثبت نام یا پیش ثبت نام از لیست زیر کارگاه مورد نظر را انتخاب فرمایید</div>';
	$out .= '<div><form method="post">';
        $out .= '<span style="font-size:16px;" >کارگاه:</span>';
	$out .= '<select class="input-lg" name="id" >';
	$out .= '<option value="-1"></option>';
	foreach($kargahs as $kargah)
		$out .= '<option value="'.$kargah->id.'" >'.$kargah->name.'</option>';
	$out .= '</select>';
	$out .= '<input type="hidden" name="comman" value="register" />';
	$out .= '<button class="btn btn-lg " >انتخاب</button>';
	$out .='</form></div>';
        if($pishnahad)
        {    
            $out.='<div class="new_kargah" ><form method="POST" >';
            $out.='<div class="alert alert-info" >پیشنهاد کارگاه جدید</div>';
            $out.='<div>نام و نام خانوادگی: <input name="flname" type="text"></div>';
            $out.='<div>در مورد کارگاه پیشنهادی خود توضیح دهید: </div>';
            $out .='<div><textarea name="toz" cols="100" rows="20"></textarea></div>';
            $out .='<div><button class="btn blue" >ثبت پیشنهاد</button></textarea></div>';
            $out .= '<input type="hidden" name="comman" value="combo" />';
            $out.='</form></div>';
        }
	echo $out;
}
else
{
	require($comman.'.php');
	
}
//echo '<a href="index.php?option=com_kargah&"  class="btn btn-warning" ><i class="icon-white icon-arrow-left"></i>بازگشت به صفحه اصلی</a>';
