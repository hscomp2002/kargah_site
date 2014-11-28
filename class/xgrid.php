<?php
class xgrid
{
	public $row = array();
	public $column = array();
	public $tables = array();
	public $name = array();
	public $pageAllRows = array(); 
	public $pageRows = array();
	public $pageCount = array();
	public $pageNumber =array(); 
	public $out = array();
	public $done = FALSE;
	public $row_css =array();
	public $cell_css= array();
	public $css = array();
	public $tableProperty = array();
	public $clist = array();
	public $cfunction =array();
	public $arg = '';
	//-----------------------
	public $canAdd =array();//FALSE;
	public $canEdit = array();//FALSE;
	public $canDelete =array();// FALSE;
	public $contentDiv =array();// 'main_div';
	public $targetFile =array();
	public $cssClass =array();// 'ajaxgrid';
	public $start = array();//TRUE;
	public $addColCount = array();//3;
	public $addFunction = array();//null
	public $editFunction = array();//null
	public $deleteFunction = array();//null
	public $whereClause = array();//''
	public $buttonTitles = array();//array('delete'=>'حذف','add'=>'ثبت','next'=>'بعد','pre'=>'قبل'); 
	public $eRequest = array();//array();
	public $echoQuery = FALSE;
	public $alert = FALSE;
	public $afterCreateFunction = array();
	public $disableRowColor = array();
	public $scrollDown = array();
	public $xls = array();
	public $query = array();
	public $qq = array();
        public $page_addr='';
        
	public function getComment($table,$fieldKol)
	{
		$fieldTmp = explode('.',$fieldKol);
		if(count($fieldTmp) >1)
		{
			$field = $fieldTmp[1];
			$table = $fieldTmp[0];
		}
		else
			$field = $fieldTmp[0];
		$conf = new conf;
		$db = $conf->db;
		$out = $field;
		$my = new mysql_class;
		$my->ex_sql("select column_comment from information_schema.columns where table_schema='$db' and table_name='$table' and column_name='$field'",$q);
		if(isset($q[0]))
			$out = (trim($q[0]['column_comment'])!='')?trim($q[0]['column_comment']):$field;
		return($out);
	}

	public function __construct($inp= array(),$addr)
	{
		$mysql = new mysql_class;
		$css = 'ajaxgrid_mainDiv';
		$tableProperty = "class='ajaxgrid_mainTable'";
		if(count($inp)!=0 && is_array($inp))
		{

			$gtmp =array();
			foreach($inp as $gname=>$det)
			{
				$gtmp[$gname]['gname'] = $gname;
				$gtmp[$gname]['table'] = $det['table'];
				$gtmp[$gname]['contentDiv'] = $det['div'];
				$this->contentDiv[$gname] = $det['div'];
				$gtmp[$gname]['canAdd'] = FALSE;
				$this->canAdd[$gname] = FALSE;
				$gtmp[$gname]['canEdit'] = FALSE;
				$this->canEdit[$gname] = FALSE;
				$gtmp[$gname]['canDelete'] = FALSE;
				$this->canDelete[$gname] = FALSE;
				$gtmp[$gname]['targetFile'] = $addr;
				$this->targetFile[$gname] = $gtmp[$gname]['targetFile'];
				$gtmp[$gname]['cssClass'] ='ajaxgrid' ;
				$this->cssClass[$gname] = 'ajaxgrid';
				$gtmp[$gname]['start'] =TRUE ;	
				$this->start[$gname] = TRUE;
				$gtmp[$gname]['xls'] =FALSE ;
                                $this->xls[$gname] = TRUE;
				$gtmp[$gname]['addColCount'] =3 ;
				$this->addColCount[$gname] = 3;
				$gtmp[$gname]['tableProperty'] = "class='ajaxgrid_mainTable'";
				$this->tableProperty[$gname] ="class='ajaxgrid_mainTable'";
				$gtmp[$gname]['css'] = "darkDiv";
				$gtmp[$gname]['eRequest'] = array();
				$gtmp[$gname]['pageNumber'] = 1;
				$gtmp[$gname]['query'] = isset($det['query'])?$det['query']:'';
				$this->query[$gname] = $gtmp[$gname]['query'];
				$this->tables[$gname] = $det['table'];
				$this->pageRows[$gname] = 10;
				$this->whereClause[$gname] = '';
				$this->pageNumber[$gname] = 1;
				$this->eRequest[$gname] = array();
				$this->scrollDown[$gname] = FALSE;
				$this->afterCreateFunction[$gname] = '';
				$this->disableRowColor[$gname] = FALSE;
				$this->buttonTitles[$gname] = array('delete'=>'حذف','add'=>'ثبت','next'=>'بعد','pre'=>'قبل'); 
				$q = null;
				$mysql->enableCache = FALSE;
				$mysql->oldSql = TRUE;
				$qur = (isset($this->query[$gname]) && trim($this->query[$gname])!='')?$this->query[$gname]:'select * from `'.$this->tables[$gname].'` where 1=0 ';
				$mysql->directQuery($qur,$q);
				if(isset($this->query[$gname]) && trim($this->query[$gname])!='')
					$this->qq[$gname] = $q;
/*
				while($r = $mysql->fetch_field($this->tables[$gname]))
				{
					$cTmp['name']=$this->getComment($det['table'],$r->name);
					if($cTmp['name'] == 'hidden')
						$cTmp['name']='';
					$cTmp['fieldname']= $r->name;
					$cTmp['css']='';
					$cTmp['typ']= $r->type;
					$this->column[$gname][] = $cTmp;
				}
*/
                                $tt = $mysql->fetch_field($this->tables[$gname]);
                                foreach($tt as $r)
                                {
					$cTmp['name']=($r->Comment==''?$r->Field:$r->Comment);//$this->getComment($det['table'],$r->name);
					if($cTmp['name'] == 'hidden')
						$cTmp['name']='';
					$cTmp['fieldname']= $r->Field;
					$cTmp['css']='';
					$cTmp['typ']= $r->Type;
					$this->column[$gname][] = $cTmp;
				}    
				$this->pageAllRows[$gname] = $this->getTableRowCount($det['table'],$_REQUEST);
				$this->pageCount[$gname] = xgrid::getPageCount($this->tables[$gname],$this->pageRows[$gname],$_REQUEST);
				$gtmp[$gname]['pageCount']= $this->pageCount[$gname] ;
				$this->css[$gname] = $gtmp[$gname]['cssClass']; 
				$this->tableProperty[$gname] = $gtmp[$gname]['tableProperty'];
				$this->addFunction[$gname] = null;
				$this->editFunction[$gname] = null;
				$this->deleteFunction[$gname] = null;
			}
			$this->arg = json_encode($gtmp);//toJSON($gtmp);
		}
	}

	public function getTableRowCount($table,$req=array())
	{
		$mysql = new mysql_class;
		$werc = '';
                $out = FALSE;
		if(isset($req['table']))
		{
			$gname = $req['table'];
        	        $whereClause = (isset($this->whereClause[$gname]))?$this->whereClause[$gname]:'';
                	$werc = (isset($req['werc']))?$req['werc']:'';
	                $werc = str_replace('|','%',$werc);
        	        $werc = str_replace('where',' ',$werc);
			$werc = str_replace("\\'","'",$werc);
                	if($werc != '' || $whereClause != '')
	 	               $werc = ' where '.$werc.' '.(($werc != '' && $whereClause != '')?'and '.$whereClause:$whereClause);
                        if(isset($this->query[$gname]) && trim($this->query[$gname])!='')
                                $mysql->ex_sql($this->query[$gname],$q);
                        else
                                $mysql->ex_sql('select `id` from `'.$table.'` '.$werc,$q);
                        $out = count($q);
		}
		return $out;
	}
	public function getPageCount($table,$pageRows,$req=array())
	{
		$pageAllRows = xgrid::getTableRowCount($table,$req);
		$extraRows = 0;
		$out = 0;
		if($pageRows!=0)
		{
			$extraRows = $pageAllRows % $pageRows;
			$out = (($pageAllRows - $extraRows)/$pageRows);
		}
		if((int)$extraRows!=0)
			$out++;
		return ($out);
	}
	public function getOut($req,$se=null)
	{
		$mysql = new mysql_class;
		$gname = '';
		$gtmp =array();
		
		$doWrite =FALSE;
		foreach($this->tables as $gname=>$ttable)
		{
			$gtmp[$gname]['gname'] = $gname;
			$gtmp[$gname]['table'] = $ttable;
			$gtmp[$gname]['contentDiv'] = $this->contentDiv[$gname];
			$gtmp[$gname]['canAdd'] = $this->canAdd[$gname] || $doWrite;
			$gtmp[$gname]['canEdit'] = $this->canEdit[$gname] || $doWrite;
			$gtmp[$gname]['canDelete'] = $this->canDelete[$gname] || $doWrite;
			$gtmp[$gname]['targetFile'] = $this->targetFile[$gname];
			$gtmp[$gname]['cssClass'] =$this->cssClass[$gname];
			$gtmp[$gname]['start'] =$this->start[$gname];	
			$gtmp[$gname]['addColCount'] =$this->addColCount[$gname];
			$gtmp[$gname]['tableProperty'] = $this->tableProperty[$gname];
			$gtmp[$gname]['css'] =  $this->css[$gname];
			$this->pageAllRows[$gname] = $this->getTableRowCount($ttable,$req);
			$this->pageCount[$gname] = $this->getPageCount($ttable,$this->pageRows[$gname],$req);
			$gtmp[$gname]['pageCount']= $this->pageCount[$gname];
			$gtmp[$gname]['pageNumber']= $this->pageNumber[$gname];
			$gtmp[$gname]['eRequest'] = $this->eRequest[$gname];
			$gtmp[$gname]['alert'] = $this->alert ;
			$gtmp[$gname]['disableRowColor'] = $this->disableRowColor[$gname];
			$gtmp[$gname]['query'] = $this->query[$gname];
		}
		if(isset($req['command']) && isset($req['table']))
		{
			$gname = trim($req['table']);
			$table = $this->tables[$gname];
			$this->done = TRUE;
			$fn = '';
			if(isset($req['field']))
				$fn = (isset($this->column[$gname][$this->fieldToId($gname,$req['field'])]['cfunction'][1]))?$this->column[$gname][$this->fieldToId($gname,$req['field'])]['cfunction'][1]:'';
			$this->out[$gname] = 'error';
			switch ($req['command'])
			{
				case 'update':
					if(isset($req['field']) && isset($req['value']) && isset($req['id']))
						$this->out[$gname] = $this->update($table,$req['id'],$req['field'],$req['value'],$fn,$gname);
					else
						$this->out[$gname] = 'failed';
					break;
				case 'delete':
					if(isset($req['ids']) && $req['ids']!='')
						$this->out[$gname] = $this->delete($table,$req['ids'],$gname);
					else
						$this->out[$gname] = 'failed';
					break;
				case 'insert':
					$this->out[$gname] = $this->insert($gname,$table,$req);
					break;
				case 'csv':
/*
					$all = $req['all']=='1'?TRUE:FALSE;
					if(!$all)
					{

						$csvTmp = json_decode($req['data']);
						$csvFileName = '../csv/'.$req['fname'];
						$csvF = fopen($csvFileName,"w+");
						foreach($csvTmp as $line)
							fputcsv($csvF,$line);
						fclose($csvF);
						$this->out[$gname] = '../csv/'.$req['fname'];
					}
					else
*/
						$this->out[$gname] = '';
					break;
			}
			$this->pageCount[$gname] = $this->getPageCount($table,$this->pageRows[$gname],$req);
			$this->out[$gname] = ($this->out[$gname].','.$this->pageCount[$gname].','.$gname);
		}
		else if(isset($req['pageNumber']) && isset($req['table']))
		{
			$gname = trim($req['table']);
			$whereClause = $this->whereClause[$gname];
			$werc = (isset($req['werc']))?$req['werc']:'';
			$werc = str_replace('|','%',$werc);
			$werc = str_replace('where',' ',$werc);
			$werc = str_replace("\\'","'",$werc);
			if($werc != '' || $whereClause != '')
				$werc = ' where '.$werc.' '.(($werc != '' && $whereClause != '')?'and '.$whereClause:$whereClause);
			$ttable = $this->tables[$gname];
			$this->pageCount[$gname] = $this->getPageCount($ttable,$this->pageRows[$gname],$req);
			$this->done = TRUE;
			$gname= trim($req['table']);
			$table = $this->tables[$gname];
			if(isset($req['pageNumber']))
				$this->pageNumber[$gname] = (int)$req['pageNumber'];
			$sort = '';
			$sort_array = array();
			foreach($req as $rk => $rv)
			{
				$sort_tmp = explode("-",$rk);
				if(count($sort_tmp) == 2 && $sort_tmp[0] == 'sort')
					$sort_array[] = $rv;
			}
			if(count($sort_array) > 0)
				$sort = ((strpos($werc,'order')===FALSE)?" order by `":',`').implode('`,`',$sort_array)."`";
/*
			if(isset($this->query[$gname]) && trim($this->query[$gname])!='')
			{
				if($this->echoQuery)
					echo $this->query[$gname]."<br/>\n";
				$q = $this->qq[$gname];
				$this->canAdd[$gname] = FALSE;
				$this->canEdit[$gname] = FALSE;
				//$this->canDelete[$gname] = FALSE;
			}
			else
			{
*/
			if(isset($this->query[$gname]) && trim($this->query[$gname])!='')
                        {
				if($this->echoQuery)
                                        echo $this->query[$gname].' limit '.(int)(($this->pageNumber[$gname]-1)*$this->pageRows[$gname]).','.(int)$this->pageRows[$gname];
				$mysql->ex_sql($this->query[$gname].' limit '.(int)(($this->pageNumber[$gname]-1)*$this->pageRows[$gname]).','.(int)$this->pageRows[$gname],$q);
			}
			else
                        {
				if($this->echoQuery)
					echo 'select * from `'.$table.'` '.$werc.' '.$sort.' limit '.(int)(($this->pageNumber[$gname]-1)*$this->pageRows[$gname]).','.(int)$this->pageRows[$gname]."<br/>\n";
				$mysql->ex_sql('select * from `'.$table.'` '.$werc.' '.$sort.' limit '.(int)(($this->pageNumber[$gname]-1)*$this->pageRows[$gname]).','.(int)$this->pageRows[$gname],$q);
			}
			$i=0;
			$row = array();
/*
			if(isset($this->query[$gname]) && trim($this->query[$gname])!='')
			{
				while($rr=mysql_fetch_array($q))
                                {
                                        $cell = array();
                                        foreach($this->column[$gname] as $k=>$field)
                                        {
                                                $fn = isset($field['cfunction'][0])?$field['cfunction'][0]:'';
                                                //$tValue = ($fn!='')?$fn(htmlentities($rr[$field['fieldname']])):htmlentities($rr[$field['fieldname']]);
                                                $tValue = ($fn!='')?$fn($rr[$field['fieldname']]):$rr[$field['fieldname']];
                                                $cell[] = array('value'=>$tValue,'css'=>$this->loadCellCss($rr['id'],$field['fieldname']),'typ'=>$field['typ']);
                                                if(in_array($field['fieldname'],$sort_array))
                                                        $this->column[$gname][$k]['sort'] = 'done';
                                        }
                                        $row[] = array('cell'=>$cell,'css'=>$this->loadRowCss($rr['id']));
                                }
			}
			else
			{
*/
				foreach($q as $rr)
				{
					$cell = array();
					foreach($this->column[$gname] as $k=>$field)
					{
						$fn = isset($field['cfunction'][0])?$field['cfunction'][0]:'';
						//$tValue = ($fn!='')?$fn(htmlentities($rr[$field['fieldname']])):htmlentities($rr[$field['fieldname']]);
						$tValue = ($fn!='')?$fn($rr[$field['fieldname']]):$rr[$field['fieldname']];
						$cell[] = array('value'=>$tValue,'css'=>$this->loadCellCss($rr['id'],$field['fieldname']),'typ'=>$field['typ']);
						if(in_array($field['fieldname'],$sort_array))
							$this->column[$gname][$k]['sort'] = 'done';
					}
					$row[] = array('cell'=>$cell,'css'=>$this->loadRowCss($rr['id']));
				}
//			}
			$grid = array('column'=>$this->column[$gname],'rows'=>$row,'cssClass'=>$this->css[$gname],'tableProperty'=>$this->tableProperty[$gname],'css'=>'','buttonTitles'=>$this->buttonTitles[$gname],'eRequest'=>$this->eRequest[$gname],'pageCount'=>$this->pageCount[$gname],'alert'=>$this->alert,'scrollDown'=>$this->scrollDown[$gname],'xls'=>$this->xls[$gname],'rows_count'=>$this->pageAllRows[$gname]);
			$affn = $this->afterCreateFunction[$gname];
			if($affn != '')
				$fgrid = $affn($grid);
			else
				$fgrid = $grid;
			$this->out[$gname] = json_encode($fgrid);//toJSON($fgrid);
		}
		$gtmp[$gname]['pageCount']= $this->pageCount[$gname] ;
		$this->arg = json_encode($gtmp);//toJSON($gtmp);
		return ((isset($this->out[$gname]))?$this->out[$gname]:'');
	}
	public function loadCellCss($id,$fieldname)
	{
		$out = '';
		if(count($this->cell_css>0))
			foreach($this->cell_css as $row=>$value)
				if($row==$id)
					foreach($value as $field=>$css)
						if($fieldname==$field)
							$out = $css;
		return $out;
	}
	public function loadRowCss($id)
	{
		$out = '';
		if(count($this->row_css>0))
			foreach($this->row_css as $rows=>$css)
				if(in_array($id,$rows))
					$out=$css;
		return $out;
	}
	public function update($table,$id,$field,$val,$fn,$gname)
	{
		$out = 'failed';
//		$val = str_replace('*$','#',$val);
		if($this->editFunction[$gname] != null)
		{
			$ef = $this->editFunction[$gname];
			$out = ($ef($table,$id,$field,$val,$fn,$gname))?'true':'failed';
		}
		else
		{
			$val = ($fn!='')?$fn($val):$val;
			$mysql = new mysql_class;
			if($this->echoQuery)
				echo "update `$table` set `$field`='$val' where `id`= $id";
			$out = ($mysql->ex_sqlx("update `$table` set `$field`='$val' where `id`= $id")=='ok')?'true':'failed';
		}
		return($out);
	}
	public function delete($table,$id,$gname)
	{
		$out = 'failed';
		$mysql = new mysql_class;
		if($this->deleteFunction[$gname] == null)
		{
			if($this->echoQuery)
				echo "delete from `$table` where `id` in ($id) ";
			$out = ($mysql->ex_sqlx("delete from `$table` where `id` in ($id) ")=='ok')?'true':'failed';
		}
		else
		{
			$df = $this->deleteFunction[$gname];
                        $out = ($df($table,$id,$gname))?'true':'failed';
		}
		return($out);
	}
	public function insert($gname,$table,$req)
	{
		$out = -1;
		$mysql = new mysql_class;
		$fields=array();
		foreach($req as $key=>$value)
		{
			$tmp = explode('-',$key) ;
			if($tmp[0]==$gname && count($tmp)==2)
				$fields[$tmp[1]] = $value;
		}
		if(count($fields)>0)
		{
			$out = '';
			if($this->addFunction[$gname] != null)
			{
				$af = $this->addFunction[$gname];
				$out = $af($gname,$table,$fields,$this->column[$gname]);
			}
			else
			{
				$fi = "(";
				$valu="(";
				foreach ($fields as $field => $value)
				{
					$fn = (isset($this->column[$gname][$this->fieldToId($gname,$field)]['cfunction'][1]))?$this->column[$gname][$this->fieldToId($gname,$field)]['cfunction'][1]:'';
					$value = ($fn!='')?$fn($value):$value;
					$fi.="`$field`,";
					$valu .="'$value',";
				}
				$fi=substr($fi,0,-1);
				$valu=substr($valu,0,-1);
				$fi.=")";
				$valu.=")";
				$query = "insert into `$table` $fi values $valu";
				if($this->echoQuery)
					echo($query);
				$mysql->ex_sqlx($query);
//				$ln = mysql_class::ex_sqlx($query,FALSE);
				$out = (string)$mysql->insert_id();
				//$out = (string)mysql_insert_id($ln);
			}
		}
		return $out;
	}
	public function loadList($table,$val,$text,$where)
	{
		$out = array();
		$mysql = new mysql_class;
		$mysql->ex_sql("select `$val`,`$text` from `$table` where $where ",$q);
		while($r = $mysql->fetch_array($q))
			$out[$r[$val]] = $r[$text];
		return $out;
	}
	public function fieldToId($gname,$field)
	{
		foreach($this->column[$gname] as $i=>$fields)
			if($fields['fieldname']==$field)
				$out = $i;
		return $out;
	}
}
?>

