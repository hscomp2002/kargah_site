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

$document->addStyleSheet(COM_PATH.'css/bootstrap.min.css');
$document->addStyleSheet(COM_PATH.'css/xgrid.css');
$document->addStyleSheet(COM_PATH.'css/com_kargah.css');
$document->addStyleSheet(COM_PATH.'css/jquery.fileupload.css');
$document->addStyleSheet(COM_PATH.'js/cal/skins/aqua/theme.css');

