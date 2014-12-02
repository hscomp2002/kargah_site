<?php
        if(isset($_REQUEST['kargah_id']))
        {
            $kargah_id =(int) $_REQUEST['kargah_id'];
            if($kargah_id > 0)
            {
                $kargah = new kargah_class($kargah_id);
                $tarikh = date("Y-m-d H:i:s");
                $pardakht_id = pardakht_class::add($parvande_id,$tarikh,$kargah->ghimat);
                $pardakht_id+=1000000;
                $pardakht = new pardakht_class($pardakht_id);
                $pay_code = pay_class::pay($pardakht_id,$kargah->ghimat);
                $tmpo = explode(',',$pay_code);
                if(count($tmpo)==2 && $tmpo[0]==0 && $conf->ps !== 'TRUE')
                        $out = $tmpo[1];
                else
                        $out =-1;
            }
            else
                $out = 'no_kargah';
            die($out);
        }    
	$msg='';
	$id = (int)$_REQUEST['id'];
	$kid = -1;
	$kargah = new kargah_class($id);
	$det = kargah_view_class::single_view($kargah);
	if(isset($_REQUEST['fname']))
	{
		$date = new DateTime(date("Y-m-d H:i:s"));
		$date->setTimezone(new DateTimeZone('Asia/Tehran'));
		$dt = $date->format('Y-m-d H:i:s').'<br/>';
		$my = new mysql_class;
		$ln = $my->ex_sqlx("insert into #__kargah_reserve (tarikh,fname,lname,mob,tell,address,kargah_id) values ('$dt','".$_REQUEST['fname']."','".$_REQUEST['lname']."','".$_REQUEST['mob']."','".$_REQUEST['tell']."','".$_REQUEST['address']."',$id)",FALSE);
		$kid = $my->insert_id($ln);
		$my->close($ln);
		$msg='<div class="alert alert-success" >ثبت با موفقیت انجام شد کد رهگیری شما <span id="rahgiri">'.$kid.'</span> می باشد</div>';
	}
?>
<script>
	var kargah_reserve_id = <?php echo $kid; ?>;
        var base_url ='<?php echo COM_PATH; ?>';
        
        function start_kharid(inp)
	{
            jQuery("#khoon").html("درحال ارسال اطلاعات به بانک ...");
            jQuery.get(base_url+"?comman=register&kargah_id="+inp+"&",function(result){
                console.log(result);
                jQuery("#khoon").html("");
                if(jQuery.trim(result)=='-1')
                    alert('خطا در ارتباط با بانک');
                else if (jQuery.trim(result)=='no_kargah')
                    alert('هیچ  کارگاهی انتخاب نشده است');
                else
                {
                        //alert('کد رهگیری مربوط به بانک را یادداشت نمایید سپس به بانک هدایت می‌شوید' +result.split(',')[0] );
                        postRefId(result.split(',')[1]);
                }
            });
	}
        function postRefId (refIdValue)
        {
                var form = document.createElement("form");
                form.setAttribute("method", "POST");
                form.setAttribute("action", "<?php echo $conf->mellat_payPage; ?>");         
                form.setAttribute("target", "_self");
                var hiddenField = document.createElement("input");              
                hiddenField.setAttribute("name", "RefId");
                hiddenField.setAttribute("value", refIdValue);
                form.appendChild(hiddenField);
                document.body.appendChild(form);         
                form.submit();
                document.body.removeChild(form);
        }
        
</script>
<div>
	<?php
		echo $msg.$det;
                if(!isset($_REQUEST['fname'])){
	?>
        
	<form method="post" id="frm1">
		<div class="CSSTableGenerator" >
			<table>
				<tr>
					<td colspan="2" >
                                            <h3>
                                                ثبت نام
                                            </h3>
					</td>
				</tr>
				<tr>
					<td>
						نام :
					</td>
					<td>
						<input name="fname" />
					</td>
				</tr>
				<tr>
					<td>
						نام خانوادگی :
					</td>
					<td>
						<input name="lname" />
					</td>
				</tr>
				<tr>
					<td>
						تلفن همراه:
					</td>
					<td>
						<input name="mob" />
					</td>
				</tr>
				<tr>
					<td>
						تلفن ثابت:
					</td>
					<td>
						<input name="tell" />
					</td>
				</tr>
				<tr>
					<td>
						ایمیل:
					</td>
					<td>
						<input name="address" />
					</td>
				</tr>
				<tr>
					<td>
						<input type="hidden" name="id" value="<?php echo $id; ?>" />
						<input type="hidden" name="bank_send" id="bank_send" value="false" />
						<p class="readmore" ><a class="readmore" onclick="start_kharid(<?php echo $id; ?>);" >ثبت نام قطعی</a></p>
                                                <span id="khoon" ></span>
					</td>
					<td>
						<p class="readmore" ><a href="#" class="readmore" >ثبت نام موقت</a></p>
                                                <button>test</button>
					</td>
					</tr>
			</table>
		</div>
	</form>
                <?php } ?>
</div>
