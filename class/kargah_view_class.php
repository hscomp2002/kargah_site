<?php
    class kargah_view_class
    {
		//
		public function main_view($inp)
		{
			
			$url = JURI::base().'index.php/component/kargah/?';
			$out = '<ul  class="gallery" >';
			foreach($inp as $kargah_obj)
			{
				$out.=
					'<li class="thumb" >
						<div class="thumb_all" >
							<div class="thumb_img"  >
								<a href="'.($url.'comman=register&id='.$kargah_obj->id).'" > 
                                                                    <img src="'.$kargah_obj->thumbnail.'">
								</a>
							</div>
							<div class="thumb_title"><a style="color:white;visited:white;" href="'.($url.'comman=register&id='.$kargah_obj->id).'" >'.$kargah_obj->name.'</a></div>
						</div>
					</li>';
			}
			$out.='</ul>';
			return($out);
		}
		public function single_view($inp)
		{
			//$out = trim($inp->pic)===''?'':'<div><img alt="'.$inp->name.'" src="'.$inp->pic.'" /></div>';
			$out .='<div><h1>'.$inp->name.'</h1></div>';
			$out .='<div><p>'.$inp->toz.'</p></div>';
			return($out);
		}
	}
?>
