<?php
	$response[] = '<button data-toggle="collapse" data-target="#bank-payment" class="payment">
			<img src="'.url('/assets/bank.svg').'">
			<h5>'.translate("Bank transfer").'</h5>
		</button>
		<script src="'.url('/themes/default/assets/bank.js').'"></script>
		<div id="bank-payment" class="collapse ">
			<p><b>Account Name :</b> '.$options['AccountName'].'</p>
			<p><b>Account Number :</b> '.$options['AccountNumber'].'</p>
			<p><b>Bank Name :</b> '.$options['BankName'].'</p>
			<p><b>Routing Number :</b> '.$options['RoutingNumber'].'</p>
			<p><b>IBAN :</b> '.$options['IBAN'].'</p>
			<p><b>SWIFT :</b> '.$options['SWIFT'].'</p>
			<br><br>
			<button id="bank" data-order="'.$order.'" class="cart-btn bg">Confirm</button>
		</div>';
?>