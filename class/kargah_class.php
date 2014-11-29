<?php
        class kargah_class
        {
		function __construct($id=-1)
		{
			$id = (int)$id;
			if($id > 0)
			{
				$my = new mysql_class;
				$my->ex_sql("select * from #__kargah_data where `id` = $id",$q);
				if(isset($q[0]))
				{
					$r = $q[0];
					$this->id = $id;
					$this->name = $r['name'];
					$this->toz = $r['toz'];
					$this->pic = $r['pic'];
					$this->tarikh = $r['tarikh'];
					$this->en = $r['en'];
				}
			}
		}
		function loadActives()
		{
			$out = array();
			$my = new mysql_class;
			$my->ex_sql("select * from #__kargah_data where en = 1",$q);
			foreach($q as $r)
			{
				$thi = new kargah_class;
				$thi->id = $r['id'];
				$thi->name = $r['name'];
				$thi->toz = $r['toz'];
				$thi->pic = $r['pic'];
				$thi->tarikh = $r['tarikh'];
				$thi->en = $r['en'];
			        $tmp = explode('/',$thi->pic);
			        $thi->thumbnail = str_replace($tmp[count($tmp)-1], 'thumbnail/'.$tmp[count($tmp)-1], $thi->pic);
				$out[] = $thi;
			}
			return($out);
		}
	}
?>
