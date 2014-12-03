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
		var_dump($bank_put);
		$pay = pay_class::verify($SaleOrderId,$SaleReferenceId);
		if(($pay == '0' || (int)$pay == 43) && (!is_array($pay)))
		{
			$my = new mysql_class;
			$kid = $SaleOrderId-1000000;
			$my->ex_sqlx("update #__kargah_reserve set pardakht = '$SaleReferenceId' where id = $kid");
/*
			$pardakht = new pardakht_class($SaleOrderId);
			$pardakht->bank_out = serialize($bank_out);
			$pardakht->update();
			$parvande = new parvande_class($pardakht->parvande_id);
                        $toz = ' افزایش اعتبار بابت شارژ اینترنتی از درگاه، شماره پیگیری بانکی '.$SaleReferenceId;
                        $parvande->addEtebar($pardakht->mablagh,user_parvande_class::loadUserIdByParvandeId($pardakht->parvande_id),$toz);
*/
			$rev = pay_class::settle($SaleOrderId,$SaleReferenceId);
			$out = '<div class="msg" >
					پرداخت با موفقیت انجام گرفت
				</div>
                                <div>
                                        کد پیگیری بانک: 
                                        '.$SaleReferenceId.'
                                </div>
				<div>
					<input class="button1 blue searchBu" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />
				</div>				
';
		}
		else
			$out = ' پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، مبلغ از حساب شما کم نشده است
					<br/>
					<input class="button1 blue searchBu" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />';
	}
	else
		$out = 'در تراکنش مالی مشکلی پیش آمده است پرداخت انجام نشد مجدد سعی نمایید درصورت پرداخت وجه ، مبلغ از حساب شما کم نشده است
			<br/>
			<input class="button1 blue searchBu" type="button" value="بازگشت" onclick="window.location=\'index.php\';" />';
	echo $out; 
?>
