<?php 
	class htmlGenerator
	{
		private $js = '';
		public $readOnly = false;
		public $table = '';
		public $whereClause = '';
		public function getComment($table,$field)
		{
			$conf = new conf;
			$db = $table=='user'?$conf->poolDB:$conf->db;
			$out = $field;
			$my = new mysql_class;
			$my->ex_sql("select column_comment from information_schema.columns where table_schema='$db' and table_name='$table' and column_name='$field'",$q);
			if(isset($q[0]))
				$out = (trim($q[0]['column_comment'])!='')?trim($q[0]['column_comment']):$field;
			return($out);
		}
		public function __construct($table,$id=-1,$whereClause='')
		{
			$my = new mysql_class;
			$this->table = $table;
			$whereClause = trim($whereClause);
			$this->whereClause = $whereClause;
			$id = ($whereClause!='')?-1:(int)$id;
			$tmp=array();
                        $jdb = JFactory::getDbo();
                        $ou= $jdb->getTableColumns('#__'.$table,FALSE);
			$my->ex_sql("select * from `#__$table` where ".(($whereClause=='')?"`id` = $id":$whereClause),$qq);
			$id = isset($rr['id'])?(int)$rr['id']:$id;
			foreach($ou as $r)
			{
				$r->val = '';
				if($id>0)
					$r->val = isset($qq[0][$r->Field])?$qq[0][$r->Field]:'';
				if($r->Comment!='')
						$tmp[] = array('name'=>$r->Field,'comment'=>$r->Comment,'val'=>$r->val,'type'=>$r->Type,'clist'=>array());
			}
			$this->fields = $tmp;
			$phpUrl = basename($_SERVER['PHP_SELF']);
			$this->js = <<<HASAN
				<script>
                                    
					var data_$table={};
					var file_url = '$phpUrl';
					var id_$table=$id;
					function get_$table()
					{
						data_$table = {};
						$(".hG_$table").each(function(id,field){
							data_$table [$(field).prop('id')] = $(field).val();
						});
						data_$table ['id'] = id_$table;
						return(data_$table);
					}
                                        
					function send_$table(urll,methodd,fn)
					{
						var url = (typeof urll!='undefined')?urll:file_url;
						var method = (typeof methodd!='undefined')?methodd:'get';
						var data = get_$table();
                                                console.log(data);
						if(method == 'get')
						{
							$.get(url,data,function(result){
								if(typeof fn == 'function')
									fn(result);
							}).fail(function(){
                                                            fn('fail');       
                                                        });
						}
						else
						{
							$.post(url,data,function(result){
								if(typeof fn == 'function')
									fn(result);
							});
						}
					}
                                        /*
					function calendarSet()
					{
						$.each($(".dateValue"),function(id,field){
							Calendar.setup({
								inputField     :    field.id,
								button         :    field.id,
								ifFormat       :    "%Y/%m/%d",
								dateType           :    'jalali',
								weekNumbers    : false
							});			
						});
					}
                                        
					function setCalendar(id)
					{
						Calendar.setup({
							inputField     :    id,
							button         :    id,
							ifFormat       :    "%Y/%m/%d",
							dateType           :    'jalali',
							weekNumbers    : false
						});
					}
                                        */
					$(document).ready(function(){
						$(".intClass").bind('keypress',function(e){
							var e=window.event || e
							var keyunicode=e.charCode || e.keyCode
							return ((keyunicode>=48 && keyunicode<=57) || (keyunicode<=32) || (keyunicode<=127))? true : false
						});
						//calendarSet();
					});
				</script>
HASAN;
		}
		public function clistToCombo($inp,$sval=-1)
		{
			$out = '';
			foreach($inp as $key=>$val)
				$out .= "<option value=\"$key\" ".(($sval == $key)?'selected="selected"':'').">$val</option>";
			return($out);
		}
		public function getHtml($inp,$cl='')
		{
			$out='<table width="100%" class="'.$cl.'"><tr>';
			$i=1;
			foreach($this->fields as $n)
			{
				$class_inp='';
				if($n['type']=='datetime' || $n['type']=='timestamp' || $n['type']=='date')
				{
					if($n['val']!='0000-00-00 00:00:00' and $n['val']!='')
						$n['val']=jdate("Y/m/d",strtotime($n['val']));
					else
						$n['val']='';
					$class_inp=(($this->readOnly)?'':'dateValue');
				}
				else
				{
					$khapshe = explode('(',$n['type']);
					$class_inp = $khapshe[0].'Class';
				}
				$tag = '<input type="text" '.(($this->readOnly)?'readonly="readonly"':'');
				$tag_end = '/>';
				if($n['type']=='mediumtext')
				{
					$tag = '<textarea '.(($this->readOnly)?'readonly="readonly"':'');
	                                $tag_end = '></textarea>';
				}
				else if(count($n['clist'])>0)
				{
					$tag = '<select '.(($this->readOnly)?'disabled="disabled"':'');
                                        $tag_end = '>'.$this->clistToCombo($n['clist'],$n['val']).'</select>';
				}
                                //$head = trim(strtolower($n['comment']));
                                $pos1 = strpos(trim(strtolower($n['comment'])),"hidden");
                                //trim(strtolower($n['comment']))!='hidden'
				if( !($pos1!==FALSE))
				{
					$class_inp .= ' hG_'.$this->table;
					$out .= '<td align="right" style="padding: 2px;" >'.$n['comment'].':</td><td style="padding: 3px;" align="right" >'.$tag.' class="regdate '.$class_inp.'" value="'.$n['val'].'" id="'.$n['name'].'" placeholder="'.$n['comment'].'" '.$tag_end.'</td>';
					if($i%$inp==0)
						$out .='</tr><tr>';
					$i++;
				}
			}
			$out .= '</tr></table>';
			return($out.$this->js);
		}
	}
?>
