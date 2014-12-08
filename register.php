<?php
	$msg='';
	$conf = new conf;
	$id = (int)$_REQUEST['id'];
	$kid = -1;
	$kargah = new kargah_class($id);
	$det = kargah_view_class::single_view($kargah);
	if(isset($_REQUEST['fname']))
	{
            $ou = '-1';
            $date = new DateTime(date("Y-m-d H:i:s"));
            $date->setTimezone(new DateTimeZone('Asia/Tehran'));
            $dt = $date->format('Y-m-d H:i:s').'<br/>';
            $my = new mysql_class;
            $ln = $my->ex_sqlx("insert into #__kargah_reserve (tarikh,fname,lname,mob,tell,address,kargah_id) values ('$dt','".strip_tags($_REQUEST['fname'])."','".strip_tags($_REQUEST['lname'])."','".strip_tags($_REQUEST['mob'])."','".strip_tags($_REQUEST['tell'])."','".strip_tags($_REQUEST['address'])."',$id)",FALSE);
            $kid = $my->insert_id($ln);
            $my->close($ln);
            if($_REQUEST['bank']=='false')
            {    
                $ou = $kid;
                JUtility::sendMail("it@arencenter.ir","آرن", 'hscomp2002@gmail.com','ثبت نام کارگاه','<html><body>پیش ثبت نام انجام شد</body></html>');
            }    
            else
            {
                $kargah_id =(int) $_REQUEST['id'];
                $kargah = new kargah_class($kargah_id);
                $kid+=1000000;
                if($kargah->ghimat>=1000 || TRUE)
                {
                    $pay_code = pay_class::pay($kid,$kargah->ghimat);
                    $tmpo = explode(',',$pay_code);
                    if(count($tmpo)==2 && $tmpo[0]==0 && $conf->ps !== 'TRUE')
                            $ou = $tmpo[1];
                    else
                            $ou =-1;
                }    
            }
            die("$ou");
	}
?>
<script>
	var kargah_reserve_id = <?php echo $kid; ?>;
        var base_url ='<?php echo COM_PATH; ?>';
        function start_kharid(inp,bank)
	{
            if(val_form())
            {    
                jQuery("#khoon").html("درحال ارسال اطلاعات ...");
                jQuery("#tr_btn").hide();
                var requ = jQuery("#frm1").serialize();
                jQuery.get(base_url+"?comman=register&"+requ+"&bank="+(bank?'true':'false'),function(result){
                    result = jQuery.trim(result);
                    if(result!=='-1')
                    {
                        jQuery(".CSSTableGenerator table").hide('slow');
                        if(!bank)
                        {    
                            jQuery("#khoon").addClass("alert alert-success");
                            jQuery("#khoon").html("ثبت نام موقت شما با موفقیت انجام گرفت کد پیگیری شما  "+result+" می باشد");
                        }
                        else
                        {
                            postRefId(result);
                        }    
                    }
                    else
                    {
                        alert("خطا در  ارتباط با بانک لطفا بعدا تلاش فرمایید");
                        jQuery("#khoon").html(""); 
                        jQuery("#tr_btn").show();
                    }    
                });
            }
	}
        function postRefId (refIdValue)
        {
                var form = document.createElement("form");
                form.setAttribute("method", "POST");
		form.setAttribute("id", "mellat_frm");
                form.setAttribute("action","<?php echo $conf->mellat_payPage; ?>");         
                form.setAttribute("target", "_self");
                var hiddenField = document.createElement("input");              
                hiddenField.setAttribute("name", "RefId");
                hiddenField.setAttribute("value", refIdValue);
                form.appendChild(hiddenField);
                document.body.appendChild(form);         
                jQuery("#mellat_frm").submit();
                document.body.removeChild(form);
        }
        function val_form()
        {
            if(jQuery("#fname").val().length < 3)
            {
                alert("نام را کامل وارد کنید");
                return false;
            }
            if(jQuery("#lname").val().length < 3)
            {
                alert("نام خانوادگی را کامل وارد کنید");
                return false;
            }
            if(jQuery("#mob").val().length < 9 && !isInt(jQuery("#mob").val()))
            {
                alert("تلفن همراه  را کامل وارد کنید");
                return false;
            }
            if(jQuery("#address").val().length < 3 && !validateEmail(jQuery("#address").val()))
            {
                
                alert("ایمیل  را کامل وارد کنید");
                return false;
            }
            return true;
        }
        function isInt(n)
        {
            return Number(n)===n && n%1===0;
        }
        function validateEmail(email) { 
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        } 
</script>
<div>
	<?php
		echo $msg.$det;
	?>
        
	<form method="post" id="frm1">
                <div class="alert alert-info" >
                    شهریه:
                    <?php echo monize($kargah->ghimat); ?> 
                    ریال
                </div>
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
						نام :*
					</td>
					<td>
						<input id="fname"  name="fname" />
					</td>
				</tr>
				<tr>
					<td>
						نام خانوادگی :*
					</td>
					<td>
						<input name="lname"  id="lname" />
					</td>
				</tr>
				<tr>
					<td>
						تلفن همراه:*
					</td>
					<td>
						<input name="mob" id="mob" />
					</td>
				</tr>
				<tr>
					<td>
						تلفن ثابت:
					</td>
					<td>
						<input name="tell" id="tell" />
					</td>
				</tr>
				<tr>
					<td>
						ایمیل:*
					</td>
					<td>
						<input name="address" id="address" />
					</td>
				</tr>
				<tr id="tr_btn" >
					<td>
						<!-- <p class="readmore" ><a style="cursor:pointer" class="readmore" onclick="start_kharid(<?php echo $id; ?>,false);" >ثبت نام موقت</a></p> -->
					</td>
					<td>
						<input type="hidden" name="id" value="<?php echo $id; ?>" />
						<input type="hidden" name="bank_send" id="bank_send" value="false" />
						<p class="readmore" ><a style="cursor:pointer" class="readmore" onclick="start_kharid(<?php echo $id; ?>,true);" >ثبت نام</a></p>
					</td>
				</tr>
			</table>
                        <div id="khoon" ></div>
		</div>
	</form>
</div>
