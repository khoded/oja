<?php
	if (isset($order)){
		$data['method'] = 'cash';
		$data['payment_status'] = 'paid';
		$payment = json_encode($data,true);
		DB::update("UPDATE orders SET payment = '$payment' WHERE id = ".$order);
		$will_pay = true;
	} else {
		echo 'direct access not allowed';
	}
?>