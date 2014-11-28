<?php
	class mysql_class
	{
		public $host = 'localhost';
		public $db = '';
		public $user = '';
		public $pass = '';
		public $ex_db = FALSE;
		public $hotTable = array('cache','menu_table','access','access_det','grop');
		public $coldTable = array();
		public $enableCache = FALSE;
		public $oldSql = FALSE;
//-------------------------------------------ArenDarmaBased----------------------------------------------------
		public $poolTables = array("parvande_etebar","user_etebar","transactions","user","cards","terminals","company");
		public $poolDB = "pool";
		public $translate_field = array(
                                "parvande_id"=>"parvande_profile_id"
                                );
		public $translate_field_1 = array(
                                "parvande_profile_id"=>"parvande_id"
                                );
		public function convertQuery($sql)
		{
			$tb = $this->tableFromQuery($sql);
			$translate = array(
				"parvande_etebar"=>"user_etebar"
				);
			$translate_field = $this->translate_field;
			if(isset($translate[$tb]))
			{
				$newTb = $translate[$tb];
				$sql = str_replace($tb,$newTb,$sql);
				foreach($translate_field as $f1 => $f2)
					$sql = str_replace($f1,$f2,$sql);
			}
			return($sql);
		}
		public function arabicToPersian($inp)
                {
			$out = str_replace(
		    array('ي', 'ك'),
		    array('ی', 'ک'),
		    $inp);
                        return($out);
                }
		public function convertDB($sql,$db)
		{
			foreach($this->poolTables as $tb)
			{
				//echo "strpos($sql,$tb) = ".var_export(strpos($sql,$tb),TRUE)."<br/>\n";
				//if(strpos($sql,$tb)!==FALSE)
				$tb1 = $this->tableFromQuery($sql);
				if(trim($tb1) == $tb)	
					$db = $this->poolDB;
			}
			return($db);
		}
//-------------------------------------------------------------------------------------------------------------
		public function __construct($tables = array())
		{
//			$conf = new conf;
//                      $this->poolDB = (trim($conf->poolDB)!="")?$conf->poolDB:"darma";
			//$this->enableCache = ($conf->enableCache === FALSE)?$conf->enableCache:TRUE;
			//$this->hotTable = ((isset($tables['hotTable']))?$tables['hotTable']:((isset($conf->hotTable))?$conf->hotTable:array()));
			//$this->coldTable = ((isset($tables['coldTable']))?$tables['coldTable']:((isset($conf->coldTable))?$conf->coldable:array()));
		}
		public function getArrayFirstIndex($arr)
		{
			foreach ($arr as $key => $value)
				return $key;
		}
		public function fetch_array($q)
		{
			return(mysql_fetch_array($q));
		}
		public function fetch_field($table)
		{
                        $jdb = JFactory::getDbo();
                        $ou = $jdb->getTableColumns($table,FALSE);
			return($ou);
		}
		public function close($ln=null)
		{
			return(TRUE);
		}
		public function num_rows($q)
		{
			return(mysql_num_rows($q));
		}
/*
		public function qToArr($data)
		{
			$tmpData = array();
			$keys = array();
			if($data !== FALSE)
			{
				while($r = mysql_fetch_field($data))
					$keys[] = $r->name;
				//var_dump($keys);
				while($r = mysql_fetch_array($data))
				{
					//var_dump($r);
					$tmpData1 = array();
					foreach($keys as $key)
					{
						$key1 = isset($this->translate_field_1[$key])?$this->translate_field_1[$key]:$key;
						$tmpData1[$key1] = $r[$key];
					}
					$tmpData[] = $tmpData1;
				}
			}
			//var_dump($tmpData);
			return($tmpData);
		}
*/

		public function qToArr($data,$tb='')
		{
			//echo "table is '$tb'<br/>\n";
			$tmpData = array();
			$keys = array();
			if($data !== FALSE)
			{
				while($r = mysql_fetch_field($data))
					$keys[] = $r->name;
				//var_dump($keys);
				while($r = mysql_fetch_array($data))
				{
					//var_dump($r);
					$tmpData1 = array();
					foreach($keys as $key)
					{
						$key1 = (isset($this->translate_field_1[$key]) && $tb=='user_etebar')?$this->translate_field_1[$key]:$key;
						$tmpData1[$key1] = $r[$key];
					}
					$tmpData[] = $tmpData1;
				}
			}
			//var_dump($tmpData);
			return($tmpData);
		}
		public function directQuery($sql,&$q)
		{
			$sql = $this->convertQuery(mysql_class::arabicToPersian($sql));
			$q = NULL;
                        $jdb = JFactory::getDbo();
                        $jdb->setQuery($sql);
                        $jdb->query();
                        $q = $jdb->loadAssocList();
                        $out = "ok";
			return($out);
		}
		public function directQueryx($sql)
                {
                        $sql = $this->convertQuery(mysql_class::arabicToPersian($sql));
			$q = NULL;
                        $jdb = JFactory::getDbo();
                        $jdb->setQuery($sql);
                        $jdb->query();
                        $out = "ok";
			return($out);
                }
		public function tableFromQuery($sql)
		{
			$out = '';
			$fr = ' ';
			if(strpos(trim($sql),"from") !== FALSE)
				$fr = 'from';
			else if(strpos(trim($sql),"into") !== FALSE)
                                $fr = 'into';
			$tmp = explode($fr,strtolower(trim($sql)));
			if(count($tmp) > 1)
			{
				$tmp1 = trim($tmp[1]);
				$tmp2 = explode(' ',$tmp1);
				$out = $tmp2[0];
			}
			return(str_replace('`','',$out));
		}
		public function insert_id($ln=null)
		{
                        $jdb = JFactory::getDbo();
                        return($jdb->insertid());
		}
		public function ex_sql($sql,&$q)
		{
			$table = $this->tableFromQuery($sql);
			$out = $this->directQuery($sql,$q);
			$out = 'ok';
			return($out);
		}
		public function ex_sqlx($sql,$close=TRUE)
		{
			$sql = $this->convertQuery(mysql_class::arabicToPersian($sql));
                        $jdb = JFactory::getDbo();
                        $jdb->setQuery($sql);
                        $jdb->query();
                        $out = "ok";
			return($out);
		}
		function loadField($table)
		{
			$out = array();
			$my = new mysql_class;
			$my->oldSql = TRUE;
			$my->ex_sql("select * from `$table` where 1=0",$q);
			while($r = mysql_fetch_field($q))
				$out[] = $r->name;
			return $out;
		}
		function accessToGroup($page_name,$group_id)
		{
			$my = new mysql_class;
			$my->ex_sqlx("insert into `access` (`page_name`,`group_id`,`is_group`) values ('$page_name','$group_id','1')");
		}
		function deleteAccessToGroup($page_name,$group_id=-1)
		{
			$my = new mysql_class;
			if((int)$group_id>0)
				$my->ex_sqlx("delete from `access`  where `page_name`='$page_name' and `group_id`='$group_id'");
			else
				$my->ex_sqlx("delete from `access`  where `page_name`='$page_name' ");
		}
	}
?>
