<?php
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
						<p class="readmore" ><a href="#" class="readmore" >ثبت نام قطعی</a></p>
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
