<?php
	$out = '';
	if(isset($_REQUEST['RefId']) && isset($_REQUEST['ResCode']) && isset($_REQUEST['SaleOrderId']) && isset($_REQUEST['SaleReferenceId']) && isset($_REQUEST['CardHolderInfo']))
	{
		$RefId  = $_REQUEST['RefId'];
		$ResCode = $_REQUEST['ResCode'];
		$SaleOrderId = $_REQUEST['SaleOrderId'];
		$SaleReferenceId = $_REQUEST['SaleReferenceId'];
		$CardHolderInfo = $_REQUEST['CardHolderInfo'];
		$bank_out = array('RefId'=>$RefId,'ResCode'=>$ResCode,'SaleOrderId'=>$SaleOrderId,'SaleReferenceId'=>$SaleReferenceId,'CardHolderInfo'=>$CardHolderInfo);
		$pay = pay_class::verify($SaleOrderId,$SaleReferenceId);
		if(($pay == '0' || (int)$pay == 43) && (!is_array($pay)))
		{
			$my = new mysql_class;
			$my->ex_sqlx("update #__kargah_reserve set pardakht = '$SaleReferenceId' where id =".((int)$SaleOrderId-1000000));
			$rev = pay_class::settle($SaleOrderId,$SaleReferenceId);
			
			$out = '<div class="alert alert-success" >
					پرداخت با موفقیت انجام گرفت
				</div>
                                <div class="alert alert-info" >
                                        کد پیگیری بانک: 
                                        '.$SaleReferenceId.'
                                </div>
				<div>
					<input type="button" value="بازگشت" onclick="window.location=\'index.php/component/kargah/\';" />
				</div>				
';
		}
		else
			//$out = 'salam 2';
			$out = ' پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، مبلغ از حساب شما کم نشده است<br/><input class="button1 blue searchBu" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />';

	}
	else
		$out = 'در تراکنش مالی مشکلی پیش آمده است پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، مبلغ از حساب شما کم نشده است
			<br/>
			<input class="button1 blue searchBu" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />';
	echo $out; 
?>