<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Api extends Controller
{
	public $cfg;
    public $cart;
    public $cart_options;

    public function __construct()
    {
        $this->cfg = DB::select('SELECT * FROM config WHERE id = 1')[0];
        $this->cart = isset($_COOKIE['cart']) ? json_decode(stripslashes($_COOKIE['cart']),true) : array();
        $this->cart_options = isset($_COOKIE['cart_options']) ? json_decode($_COOKIE['cart_options'],true) : array();
    }
    public function products()
	{
		// Apply the product filters
		$where = array();
		if (!empty($_GET['min']) && !empty($_GET['max']))
		{
			$where['price'] = "price BETWEEN '".escape($_GET['min'])."' AND '".escape($_GET['max'])."'";
		}
		if (!empty($_GET['search']))
		{
			$where['search'] = "(title LIKE '%".escape($_GET['search'])."%' OR text LIKE '%".escape($_GET['search'])."%')";
		}
		if (!empty($_GET['cat']))
		{
			$category = DB::select("SELECT * FROM category WHERE id = '".escape($_GET['cat'])."'")[0];
			$categories[] = $category->id;
			if ($category->parent == 0){
				$childs = DB::select("SELECT * FROM category WHERE parent = ".$category->id." ORDER BY id DESC");
				foreach ($childs as $child){
					$categories[] = $child->id;
				}
			}
			$where['cat'] = "category IN (".implode(',',$categories).")";
		}
		$where = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';
		$products = DB::select("SELECT * FROM products $where ORDER BY id DESC ");
		$response = array();
		$response['products'] = array();
		// fetch products and return them in json format
		foreach ($products as $row){
			$data['id'] = $row->id;
			$data['images'] = url('/assets/products/'.image_order($row->images));
			$data['title'] = $row->title;
			$data['text'] = mb_substr(translate($row->text),0,200);
			$data['path'] = 'product/'.path($row->title,$row->id);
			$data['price'] = c($row->price);
			array_push($response["products"], $data);
		}
		return json_encode($response);
	}
    public function posts()
	{
		// Apply the posts filter
		$where = array();
		if (!empty($_GET['search']))
		{
			$where['search'] = "(title LIKE '%".escape($_GET['search'])."%' OR content LIKE '%".escape($_GET['search'])."%')";
		}
		$where = ($where) ? 'WHERE ' . implode(' AND ', $where) : '';
		$posts = DB::select("SELECT * FROM blog $where ORDER BY id DESC ");
		$response = array();
		$response['posts'] = array();
		// fetch posts and return them in json format
		foreach ($posts as $row){
			$data['id'] = $row->id;
			$data['image'] = url('/assets/products/'.$row->images);
			$data['title'] = $row->title;
			$data['content'] = mb_substr(translate($row->content),0,200);
			$data['path'] = 'blog/'.path($row->title,$row->id);
			$data['time'] = timegap($row->time).translate(' ago');
			$data['timestamp'] = $row->time;
			array_push($response["posts"], $data);
		}
		return json_encode($response);
	}
	public function add()
	{
		if (!isset($_GET['id'])) { exit; }
		$id = $_GET['id'];
		$q = isset($_GET['q']) ? (int)$_GET['q'] : "1";
		$cart_items = array();
		$cart_items[$id] = $q;
		$options = json_decode(DB::select("SELECT options FROM products WHERE id = ".$id)[0]->options,true);
		$option_list = array();
            if(!empty($options)){
                foreach ($options as $option) {
                    $name = $option['name'];
                    $title = $option['title'];
                    $option_list[$name] = array('title'=>$title,'value'=>(isset($_GET[$name]) ? ($option['type'] == 'multi_select' ? implode(' , ',$_GET[$name]) : $_GET[$name]) : ''));
                }
            }
			$cart_options[$id] = json_encode($option_list);
		if (DB::select("SELECT quantity FROM products WHERE id = ".$id)[0]->quantity < $q){
			// Throw stock unavailable error
			return 'unavailable';
		}
		// Check if the item is in the array, if it is, update quantity
		if(array_key_exists($id, $this->cart)){
			foreach($this->cart as $key => $value){
				$cart_items[$key] = $value;
			}
			unset($cart_items[$id]);
			if ($q > 0){
				$cart_items[$id] = $q;
			}
			$json = json_encode($cart_items, true);
			// Update cart cookie
			setcookie('cart', $json,time()+31536000,'/');
			foreach($this->cart_options as $key => $value){
				$cart_options[$key] = $value;
			}
			unset($cart_options[$id]);
			$cart_options[$id] = json_encode($option_list);
			$json = json_encode($cart_options, true);
			// Update cart options cookie
			setcookie('cart_options', $json,time()+31536000,'/');
			return 'updated';
		} else {
			if(count($this->cart)>0){
				foreach($this->cart as $key=>$value){
					// add old item to array, it will prevent duplicate keys
					$cart_items[$key]=$value;
				}
			}
			$json = json_encode($cart_items, true);
			// Update cart cookies
			setcookie('cart', $json,time()+31536000,'/');
			if(count($this->cart_options)>0){
				foreach($this->cart_options as $key=>$value){
					// add old item to array, it will prevent duplicate keys
					$cart_options[$key]=$value;
				}
			}
			$json = json_encode($cart_options, true);
			// Update cart cookies
			setcookie('cart_options', $json,time()+31536000,'/');
			return 'success';
		}
	}
	public function cart()
	{
		$cart_items = $this->cart;
		// Append items count to the response
		$response["count"] = count($cart_items);
		// Cart header
		$response['header'] = translate('Cart').'<button class="pull-right" onclick="$(\'#cart\').toggleClass(\'cart-open\');$(\'#cart\').toggle(\'300\');"><i class="icon-close"></i></button>';
		if($response["count"] > 0){
			// get the product ids
			$ids = "";
			foreach($cart_items as $id=>$name){
				$ids = $ids . $id . ",";
			}
			// remove the last comma
			$ids = rtrim($ids, ',');
			// Get products from database
			$cart_products = DB::select("SELECT * FROM products WHERE id IN ({$ids}) ORDER BY id DESC ");
			$total_price = 0;
			$total_p = 0;
			$response["products"] = array();
			foreach ($cart_products as $row){
				$q = $this->cart[$row->id];
				$options = json_decode($this->cart_options[$row->id],true);
				$option_array = array();
				foreach ($options as $option) {
                    $option_array[] = '<i>'.$option['title'].'</i> : '.$option['value'];
                }
				$data['id'] = $row->id;
				$data['images'] = image_order($row->images);
				$data['title'] = $row->title;
				$data['price'] = c($row->price);
				$data['quantity'] = $q;
				$data['options'] = implode('<br/>',$option_array);
				$data['total'] = c($row->price * $q);
				// Add product to array :
				array_push($response["products"], $data);
				$total_p+= $q;
			}
		}
		if (isset($_COOKIE["coupon"])){
			// Check if coupon applied
			if (DB::select("SELECT COUNT(*) as count FROM coupons WHERE code = '".$_COOKIE['coupon']."'")[0]->count > 0){
				$coupon = DB::select("SELECT * FROM coupons WHERE code = '".$_COOKIE["coupon"]."'")[0];
				$response["coupon"] = '<button data-toggle="collapse" class="btn-coupon" data-target="#coupon">'.translate('Coupon').' : <b>'.$_COOKIE["coupon"].' ('.$coupon->discount.$coupon->type.')</b></button>
				<div id="coupon" class="collapse">
				<input placeholder="'.translate('Coupon code').'" value="'.$_COOKIE["coupon"].'" class="coupon" id="code"/>
				<button id="apply" class="btn-apply">'.translate('Apply').'</button>
				</div>';
			}
		} else {
			$response["coupon"] = '
			<button data-toggle="collapse" class="btn-coupon" data-target="#coupon">'.translate('Have a coupon code ?').'</button>
			<div id="coupon" class="collapse">
			<input placeholder="'.translate('Coupon code').'" class="coupon" id="code"/>
			<button id="apply" class="btn-apply">'.translate('Apply').'</button>
			</div>';
		}
		return json_encode($response);
	}
	public function remove()
	{
		// Remove product from cart
		$cart_items = array();
		foreach($this->cart as $key=>$value){
			$cart_items[$key] = $value;
		}
		unset($cart_items[$_GET['id']]);
		$json = json_encode($cart_items, true);
		setcookie('cart', $json,time()+31536000,'/');
		return 'removed';
	}
	public function checkout()
	{
		$response = array();
		// Checkout header
		$response['header'] = '<button class="pull-left load-cart"><i class="icon-arrow-left"></i></button>'.translate('Checkout').'<button class="pull-right" onclick="$(\'#cart\').toggleClass(\'cart-open\');$(\'#cart\').toggle(\'300\');"><i class="icon-close"></i></button>';
		// Get custom fields from database
		$fields = DB::select("SELECT * FROM fields ORDER BY id ASC ");
		$response[] = '<form id="customer"><div id="errors"></div>';
		foreach ($fields as $field){
			if ($field->code == 'country'){
				// Return country selector if code of field is country
				$options = '';
				$countries = DB::select("SELECT * FROM country ORDER BY nicename ASC");
				foreach ($countries as $country){
					$options .= '<option value="'.$country->iso.'" data-phone="'.$country->phonecode.'">'.$country->nicename.'</option>';
				}
				$response[] = '<div class="form-group">
					<label class="control-label">'.translate($field->name).'</label>
					<select id="country" name="'.$field->code.'" class="form-control" type="text">'.$options.'</select>
				</div>';
			} else {
				$response[] = '<div class="form-group">
					<label class="control-label">'.translate($field->name).'</label>
					<input name="'.$field->code.'" value="'.customer($field->code).'" class="form-control" type="'.($field->code == 'mobile' ? 'number' : 'text').'">
				</div>';
			}
		}
		$response[] = '</form>';
		return json_encode($response);
	}
	public function payment()
	{
		$response = array();
		$error = '';
		$success = '';
		// Payment header
		$response['header'] = translate('Payment').'<button class="pull-right" onclick="$(\'#cart\').toggleClass(\'cart-open\');$(\'#cart\').toggle(\'300\');"><i class="icon-close"></i></button>';
		$response['message'] = '';
		$email_fields = '';
		$email_products = '';
		$fields = DB::select("SELECT * FROM fields ORDER BY id ASC ");
		foreach ($fields as $field){
			// Retrieve POSTed data , or throw error if they aren't filled
			if(!empty($_POST[$field->code])){
				$data[$field->code] = escape($_POST[$field->code]);
				$email_fields .= $data[$field->code].'<br />';
			} else {
				$response['error'] = 'true';
				$response['message'] .= translate($field->name).' '.translate('field is required').'<br/>';
			}
		}
		if (isset($response['error'])){
			return json_encode($response);
		}
		$cart_items = $this->cart;
		if(count($cart_items) > 0){
			$ids = "";
			foreach($cart_items as $id=>$name){
				$ids = $ids . $id . ",";
			}
			$ids = rtrim($ids, ',');
			$cart_products = DB::select("SELECT * FROM products WHERE id IN ({$ids})  ORDER BY id DESC ");
			$total = 0;
			$products = array();
			foreach ($cart_products as $row){
				$q = $this->cart[$row->id];
				$product['id'] = $row->id;
				$product['quantity'] = $q;
				$product['options'] = $this->cart_options[$row->id];
				array_push($products,$product);
				$total += $row->price * $q;
				$email_products .= '<div>'.$row->title.' x '.$q.'<b style="float:right">'.$row->price * $q.'$</b></div><hr>';
				// Update product quantity
				DB::update("UPDATE products SET quantity = quantity - $q WHERE id = '".$product['id']."'");
			}
		} else {
			return 'Your cart is empty';
		}
		$sub_total = $total;
		$coupon_response = '';
		$data['coupon'] = '';
		if (isset($_COOKIE['coupon'])) {
			// Check if coupon is valid
			if (DB::select("SELECT COUNT(*) as count FROM coupons WHERE code = '".$_COOKIE['coupon']."'")[0]->count > 0){
				$coupon_data = DB::select("SELECT * FROM coupons WHERE code = '".$_COOKIE["coupon"]."'")[0];
				if ($coupon_data->type == '%'){
					$total = $total - ($total * $coupon_data->discount / 100);
				} else {
					$total = $total - $coupon_data->discount;
				}
				$email_products .= '<div>Coupon discount<b style="float:right">'.$coupon_data->discount.$coupon_data->type.'</b></div><hr>';
				$coupon_response = '<div>Coupon discount <b>'.$coupon_data->discount.$coupon_data->type.'</b></div>';
				$data['coupon'] = $_COOKIE['coupon'];
			}
		}
		$shipping_response = '';
		$data['shipping'] = '0';
		if (!empty($data['country'])) {
			// Add shipping cost
			if (DB::select("SELECT COUNT(*) as count FROM shipping WHERE country = '".$data['country']."'")[0]->count > 0){
				$shipping_data = DB::select("SELECT * FROM shipping WHERE country = '".$data['country']."'")[0];
				$total = $total + $shipping_data->cost;
				$email_products .= '<div>Shipping<b style="float:right">'.c($shipping_data->cost).'</b></div><hr>';
				$shipping_response = '<div>Shipping <b>'.c($shipping_data->cost).'</b></div>';
				$data['shipping'] = $shipping_data->cost;
			}
		}
		$data['products'] = json_encode($products,true);
		$data['customer'] = (session('customer') == '' ? '0' : customer('id'));
		$data['summ'] = $total;
		$data['time'] = time();
		$data['date'] = date('Y-m-d');
		$data['stat'] = 1;
		$data['payment'] = '{"payment_status":"unpaid"}';
		$columns = implode(", ",array_keys($data));
		$values  = implode("', '", $data);
		// Update orders per country
		DB::update("UPDATE country SET orders = orders+1 WHERE iso = '".$data['country']."'");
		// Save order in database
		$order = DB::table('orders')->insertGetId($data);
		// Send an email to customer
		mailing('order',array('buyer_name'=>$data['name'],'buyer_email'=>$data['email'],'buyer_fields'=>$email_fields,'name'=>$this->cfg->name,'address'=>$this->cfg->address,'phone'=>$this->cfg->phone,'products'=>$email_products,'total'=>$total),'Order Confirmation #'.$order,$data['email']);
		// Get payment methods
		$methods = DB::select("SELECT * FROM payments WHERE active = 1 ORDER BY id ASC ");
		$response[] = '<div class="payment_total"><div>Sub total <b>'.c($sub_total).'</b></div>'.$coupon_response.$shipping_response.'<div>Total <b>'.c($total).'</b></div></div><div class="payments">';
		foreach($methods as $method){
			// Get method options and include it 
			$options = json_decode(stripslashes($method->options), true);
			include app_path()."/Plugins/".$method->code."/checkout.php";
		}
		$response[] = '</div>';
		echo json_encode($response);
	}
	public function pay()
	{
		// Process order payment
		$order = (int)$_GET['order'];
		if (!isset($_GET['method']) || !isset($_GET['order'])){
			return 'Invalid parameters';
		}
		if (!in_array($_GET['method'],array('stripe','cash','bank'))){
			return 'Invalid method';
		}
		if (DB::select("SELECT COUNT(*) as count FROM orders WHERE id = {$order}")[0]->count == 0){
			return 'Invalid order';
		}
		$error = '';
		$success = '';
		$response = array();
		$unpaid = false;
		$will_pay = false;
		$response['message'] = '';
		$method = $_GET['method'];
		$cart_items = json_decode(DB::select("SELECT products FROM orders WHERE id = {$order}  ORDER BY id DESC ")[0]->products,true);
		if(count($cart_items) > 0){
			$ids = "";
			foreach($cart_items as $cart_item){
				$ids = $ids . $cart_item['id'] . ",";
				$quantity[$cart_item['id']] = $cart_item['quantity'];
			}
			$ids = rtrim($ids, ',');
			$cart_products = DB::select("SELECT * FROM products WHERE id IN ({$ids})  ORDER BY id DESC ");
			$total=0;
			$products = array();
			foreach ($cart_products as $row){
				$total += $row->price * $quantity[$row->id];
			}
		}
		$sub_total = $total;
		if (isset($_COOKIE['coupon'])) {
			// Check if coupon is valid
			if (DB::select("SELECT COUNT(*) as count FROM coupons WHERE code = '".$_COOKIE['coupon']."'")[0]->count > 0){
				$coupon_data = DB::select("SELECT * FROM coupons WHERE code = '".$_COOKIE["coupon"]."'")[0];
				if ($coupon_data->type == '%'){
					$total = $total - ($total * $coupon_data->discount / 100);
				} else {
					$total = $total - $coupon_data->discount;
				}
			}
		}
		$order_shipping = DB::select("SELECT shipping FROM orders WHERE id = ".$order)[0]->shipping;
		$total = $total + $order_shipping;
		$payment_method = DB::select("SELECT * FROM payments WHERE code='".$method."'")[0];
		if ($payment_method->active == 1){
			include app_path()."/Plugins/".$payment_method->code."/payments.php";
		} else {
			return 'Method inactive';
		}
		if ($unpaid == true) {
			// If the order isn't paid show payment methods again
			$methods = DB::select("SELECT * FROM payments WHERE active = 1 ORDER BY id ASC ");
			$response['header'] = translate('Payment').'<button class="pull-right" onclick="$(\'#cart\').toggleClass(\'cart-open\');$(\'#cart\').toggle(\'300\');"><i class="icon-close"></i></button>';
			$response[] = '<div class="payments">';
			foreach($methods as $method){
				// Get method options and include it 
				$options = json_decode(stripslashes($method->options), true);
				include app_path()."/Plugins/".$method->code."/checkout.php";
			}
		} else {
			// If the order has been paid , Show success message
			setcookie('cart', '',time()+31536000,'/');
			if ($will_pay == false) {
				// Send download link for digital products if paid successfully
				$this->send_downloads($order);
			}
			$response['header'] = translate('Success').'<button class="pull-right" onclick="$(\'#cart\').toggleClass(\'cart-open\');$(\'#cart\').toggle(\'300\');"><i class="icon-close"></i></button>';
			$response[] = '<div class="payment-success"><h3>'.translate('Thank you').'</h3>'.translate('Your order has been placed successfully').'</div>';
		}
		$response[] = '</div>';
		return json_encode($response);
	}
	public function paypal(){
		// Redirect to paypal to complete payment
		$method = DB::select("SELECT options FROM payments WHERE active = 1 AND code = 'paypal' ORDER BY id ASC ")[0]->options;
		$paypal_email = json_decode(stripslashes($method), true)['email'];
		if(isset($_GET['order']) || isset($_POST['custom'])){
			$order_id = isset($_GET['order']) ? $_GET['order'] : $_POST['custom'];
			$order_json = DB::select("SELECT * FROM orders WHERE id = ".$order_id)[0];
			$coupon = $order_json->coupon;
			$shipping = $order_json->shipping;
			$order = json_decode($order_json->products,true);
			$ids = "";
			foreach($order as $o){
				$ids = $ids . $o['id'] . ",";
				$q[$o['id']] = $o['quantity'];
			}
			$ids = rtrim($ids, ',');
			// Get order products
			$product = DB::select("SELECT * FROM products WHERE id IN ({$ids}) ORDER BY id DESC ");
			$items = '';
			$i = 1;
			foreach ($product as $row){
				$items .= "item_name_".$i."=".urlencode(translate($row->title))."&";
				$items .= "amount_".$i."=".urlencode($row->price)."&";
				$items .= "quantity_".$i."=".urlencode($q[$row->id])."&";
				$i++;
			}
			$couponquery = '';
			if (!empty($coupon)) {
				// Check if coupon is valid
				if (DB::select("SELECT COUNT(*) as count FROM coupons WHERE code = '".$coupon."'")[0]->count > 0){
					$coupon_data = DB::select("SELECT * FROM coupons WHERE code = '".$coupon."'")[0];
					if ($coupon_data->type == '%'){
						$couponquery = "discount_rate_cart=".urlencode($coupon_data->discount)."&";
					} else {
						$couponquery = "discount_amount_cart=".urlencode($coupon_data['discount'])."&";
					}
				}
			}
			$shippingquery = "handling_cart=".urlencode($shipping)."&";
			// PayPal settings
			$return_url = url('/success');
			$cancel_url = url('/failed');
			$notify_url = url('/api/paypal');
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
				$querystring .= $shippingquery;
				$querystring .= "currency_code=USD&";
				// Append paypal return addresses
				$querystring .= "return=".urlencode(stripslashes($return_url))."&";
				$querystring .= "cancel_return=".urlencode(stripslashes($cancel_url))."&";
				$querystring .= "notify_url=".urlencode($notify_url);
				$querystring .= "&custom=".$_GET['order'];			
				// Redirect to paypal IPN
				header('location:https://www.paypal.com/cgi-bin/webscr'.$querystring);
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
				$header .= "Host: www.paypal.com\r\n";  // www.paypal.com for a live site
				$header .= "Content-Length: " . strlen($req) . "\r\n";
				$header .= "Connection: close\r\n\r\n";
				
				$fp = fsockopen ('ssl://www.paypal.com', 443, $errno, $errstr, 30);
				
				if (!$fp) {
					// HTTP ERROR
					logger('HTTP ERROR');
				} else {
					fputs($fp, $header . $req);
					while (!feof($fp)) {
						$res = fgets($fp);
						// Payment verification
						if (strcmp($res, "VERIFIED") >= 0) {
							// another validation layer 
							if ($data['payment_status'] == 'Completed' && $data['receiver_email'] == $paypal_email) {
								// The payment is successful
								DB::update("UPDATE orders SET payment = '$payment' WHERE id = ".$data['order']);
								// Send download link if digital product
								$this->send_downloads($order);
								// for debugging
								logger('Successful payment');
							} else {
								// Payment unsuccessful - for debugging
								logger("The payment isn't completed yet !".$data['payment_status'].$data['receiver_email']);
							}
						} else if (strcmp ($res, "INVALID") == 0) {
							// Payment invalid - for debugging
							logger("The payment is invalid !");
						}
					}
					
					fclose ($fp);
				}
			}
		} else {
			header('location:'.url(''));
		}
	}
	public function send_downloads($order){
		$order_info = DB::select("SELECT * FROM orders WHERE id = ".$order)[0];
		$order_products = json_decode($order_info->products,true);
		$ids = "";
		foreach($order_products as $product){
			$ids = $ids . $product['id'] . ",";
		}
		$ids = rtrim($ids, ',');
		// Get order products that can be downloaded
		$products = DB::select("SELECT * FROM products WHERE id IN ({$ids}) AND download != '' ORDER BY id DESC ");
		$email_downloads = '';
		foreach ($products as $row) {
			if ($row->download != ''){
				$email_downloads .= '<div>'.$row->title.'<b style="float:right"><a href="'.url('assets/downloads/'.$row->download).'">Download</a></b></div><hr>';
			}
		}
		if ($email_downloads != '') {
			mailing('download',array('downloads'=>$email_downloads),'Order Downloads #'.$order,$order_info->email);
		}
	}
	public function review(){
		if (empty($_POST['email']) || empty($_POST['name']) || empty($_GET['product']) || empty($_POST['review']) ||empty($_POST['rating'])){
			return 'All fields are required';
		} else {
			// escape review details and insert them into the database
			$email = escape(htmlspecialchars($_POST['email']));
			$name = escape(htmlspecialchars($_POST['name']));
			$rating = (int)$_POST['rating'];
			$review = escape(htmlspecialchars($_POST['review']));
			$product = (int)$_GET['product'];
			DB::insert("INSERT INTO reviews (email,name,rating,review,product,time,active) VALUE ('$email','$name','$rating','$review','$product','".time()."','0')");
			return 'success';
		}
	}
	public function coupon(){
		// Check if coupon is valid and save it in cookies
		$code = htmlspecialchars($_GET['code']);
		if (DB::select("SELECT COUNT(*) as count FROM coupons WHERE code = '$code'")[0]->count > 0){
			setcookie('coupon', $code);
			return 'success';
		} else {
			return 'invalid';
		}
	}
	public function subscribe(){
		// Email subscribe
		$email = htmlspecialchars($_GET['email']);
		if (DB::select("SELECT COUNT(*) as count FROM subscribers WHERE email = '$email'")[0]->count > 0){
			return 'Already subscribed';
		} else {
			DB::insert("INSERT INTO subscribers (email) VALUE ('$email')");
			return 'successfully subscribed';
		}
	}
	public function orders(){
		$orders = DB::select("SELECT * FROM orders ORDER BY id DESC ");
		$fields = DB::select("SELECT code FROM fields ORDER BY id ASC");
		$response = array();
		$response['orders'] = array();
		// fetch reviews and return them in json format
		foreach ($orders as $row){
			$data['id'] = $row->id;
			// return data per field
			foreach($fields as $field){
				$code = $field->code;
				if ($code == 'country') {
					$row->$code = country($row->$code);
				}
				$data[$code] = $row->$code;
			}
			$data['products'] = array();
			$products = json_decode($row->products, true);
			if(count($products)>0){
				// search for products and return product data and selected quantity
				$ids = "";
				foreach($products as $item){
					$ids = $ids . $item['id'] . ",";
					$q[$item['id']] = $item['quantity'];
				}
				$ids = rtrim($ids, ',');
				$products = DB::select("SELECT * FROM products WHERE id IN (".$ids.")  ORDER BY id DESC ");
				$total_price=0;
				foreach ($products as $item){
					$product['id'] = $item->id;
					$product['title'] = $item->title;
					$product['price'] = $item->price;
					$product['quantity'] = $q[$item->id];
					$product['total'] = $item->price*$q[$item->id];
					array_push($data['products'], $product);
				}
			}
			$data['coupon'] = $row->coupon;
			$data['total'] = $row->summ;
			array_push($response["orders"], $data);
		}
		return json_encode($response);
	}
	public function reviews(){
		$reviews = DB::select("SELECT * FROM reviews ORDER BY id DESC ");
		$response = array();
		$response['reviews'] = array();
		// fetch reviews and return them in json format
		foreach ($reviews as $row){
			$data['id'] = $row->id;
			$data['name'] = $row->name;
			$data['name'] = $row->name;
			$data['email'] = $row->email;
			$data['rating'] = $row->rating;
			$data['review'] = $row->review;
			$data['time'] = timegap($row->time);
			$data['timestamp'] = $row->time;
			array_push($response["reviews"], $data);
		}
		return json_encode($response);
	}
}
