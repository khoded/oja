<?php
	include dirname(dirname(dirname(__FILE__)))."/system/config.php";
	include dirname(dirname(dirname(__FILE__)))."/system/functions.php";
	$method = mysqli_fetch_assoc(mysqli_query($conn, "SELECT options FROM payments WHERE active = 1 AND code = 'paypal' ORDER BY id ASC "))['options'];
	$paypal_email = json_decode(stripslashes($method), true)['email'];
	if(isset($_GET['order']) || isset($_POST['custom'])){
		$order_id = isset($_GET['order']) ? $_GET['order'] : $_POST['custom'];
		$order_json = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE id = ".$order_id));
		$coupon = $order_json['coupon'];
		$order = json_decode($order_json['products'],true);
		$ids = "";
		foreach($order as $o){
			$ids = $ids . $o['id'] . ",";
			$q[$o['id']] = $o['quantity'];
		}
		$ids = rtrim($ids, ',');
		$product = mysqli_query($conn, "SELECT * FROM products WHERE id IN ({$ids})  ORDER BY id DESC ");
		$items = '';
		$i = 1;
		while($row = mysqli_fetch_assoc($product)){
			$items .= "item_name_".$i."=".urlencode(e($row['title']))."&";
			$items .= "amount_".$i."=".urlencode($row['price'])."&";
			$items .= "quantity_".$i."=".urlencode($q[$row['id']])."&";
			$i++;
		}
		$couponquery = '';
		if (isset($coupon)) {
			// Check if coupon is valid
			if (mysqli_num_rows(mysqli_query($conn, "SELECT code FROM coupons WHERE code = '".$coupon."'")) > 0){
				$coupon_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM coupons WHERE code = '".$coupon."'"));
				if ($coupon_data['type'] == '%'){
					$couponquery = "discount_rate_cart=".urlencode($coupon_data['discount'])."&";
				} else {
					$couponquery = "discount_amount_cart=".urlencode($coupon_data['discount'])."&";
				}
			}
		}
		// PayPal settings
		$return_url = $url.'/success';
		$cancel_url = $url.'/failed';
		$notify_url = $url.'/paypal';
		// Check if paypal request or response
		if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"])){
			$querystring = '';
			
			// Firstly Append paypal account to querystring
			$querystring .= "?cmd=_cart&";
			$querystring .= "upload=1&";
			$querystring .= "business=".urlencode($paypal_email)."&";
			// $querystring .= "shopping_url =".urlencode($url)."&";
			
			//The item name and amount can be brought in dynamically by querying the $_POST['item_number'] variable.
			$querystring .= $items;
			$querystring .= $couponquery;
			$querystring .= "currency_code=USD&";
			// Append paypal return addresses
			$querystring .= "return=".urlencode(stripslashes($return_url))."&";
			$querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
			$querystring .= "notify_url=".urlencode($notify_url);
			echo $querystring .= "&custom=".$_GET['order'];			
			// Redirect to paypal IPN
			header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$querystring);
			exit();
		} else {
			// Response from Paypal
			// read the post from PayPal system and add 'cmd'
			$req = 'cmd=_notify-validate';
			foreach ($_POST as $key => $value) {
				$value = urlencode(stripslashes($value));
				$value = preg_replace('/(.*[^%^0^D])(%0A)(.*)/i','${1}%0D%0A${3}',$value);// IPN fix
				$req .= "&$key=$value";
			}
			
			// assign posted variables to local variables
			$data['method'] 		= 'paypal';
			$data['payment_status'] 	= $_POST['payment_status'];
			$data['payment_amount'] 	= $_POST['mc_gross'];
			$data['payment_currency']	= $_POST['mc_currency'];
			$data['txn_id']			= $_POST['txn_id'];
			$data['receiver_email'] 	= $_POST['receiver_email'];
			$data['payer_email'] 		= $_POST['payer_email'];
			$data['order'] 			= $_POST['custom'];
			echo $payment = json_encode($data, true);
			
			// post back to PayPal system to validate
			$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Host: www.sandbox.paypal.com\r\n";  // www.paypal.com for a live site
			$header .= "Content-Length: " . strlen($req) . "\r\n";
			$header .= "Connection: close\r\n\r\n";
			
			$fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);
			
			if (!$fp) {
				// HTTP ERROR
				error_log('HTTP ERROR');
			} else {
				fputs($fp, $header . $req);
				while (!feof($fp)) {
					$res = fgets($fp);
					// Payment verification
					if (strcmp($res, "VERIFIED") >= 0) {
						// another validation layer 
						if ($data['payment_status'] == 'Completed' && $data['receiver_email'] == $paypal_email) {
							// The payment is successful
							mysqli_query($conn, "UPDATE orders SET payment = '$payment' WHERE id = ".$data['order']);
							// for debugging
							error_log('Successful payment');
						} else {
							// Payment unsuccessful - for debugging
							error_log("The payment isn't completed yet !".$data['payment_status'].$data['receiver_email']);
						}
					} else if (strcmp ($res, "INVALID") == 0) {
						// Payment invalid - for debugging
						error_log("The payment is invalid !");
					}
				}
				
				fclose ($fp);
			}
		}
		} else {
		header('location:'.$url);
	}
?>
