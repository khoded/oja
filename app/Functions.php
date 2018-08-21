<?php
/*
* 	Send mail with custom templates
* 	$template : E-mail template.
* 	$array : Variables for email template.
* 	$subject : E-mail Subject.
* 	$to : E-mail receiver.
*/
function mailing($template,$array,$subject,$to) {
	$cfg = DB::select('SELECT * FROM config WHERE id = 1')[0];
	$array['url'] = url('');
	$array['name'] = $cfg->name;
	$array['address'] = nl2br($cfg->address);
	$array['phone'] = $cfg->phone;
	// Get the template from the database
	$message = DB::select("SELECT template FROM templates WHERE code = '".$template."'")[0]->template;
    foreach ($array as $ind => $val) {
        $message = str_replace("{{$ind}}",$val,$message);
    }
    $message = preg_replace('/\{\{(.*?)\}\}/is','',$message);
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	$headers .= 'From: '.$cfg->name.' <'.$cfg->email.'>'."\r\n";
	mail($to,$subject,$message,$headers);
    return true;
}
/*
* 	Generate url
* 	$str : The title.
* 	$id : Item ID.
*/
function path($str,$id = false) {
	$path = preg_replace("/[^a-zA-Z0-9\_|+ -]/", '', $str);
	$path = strtolower(trim($path, '-'));
	$path = preg_replace("/[\_|+ -]+/", '-', $path);
	if($id != false){
		$path = $id.'-'.$path;
	}
	return $path;
}
/*
* 	Get image by order
* 	$string : The string.
* 	$order : Image order.
*/
function image_order($string, $order = 0){
	return explode(',',$string)[$order];
}
/*
* 	Smart string cut
* 	$text : The string.
* 	$length : Length of the output.
* 	$end : String to be appended.
*/
function string_cut($text, $length = 100,$end = ''){
	mb_strlen($text);
	if (mb_strlen($text) > $length){
		$text = mb_substr($text, 0, $length);
		return $text.$end;
	} else {
		return $text;
	}
}
/*
*   Get user operation system
*/
function getOS() {
    global $_SERVER;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
    $os_platform    =   "Unknown OS Platform";
    $os_array       =   array(
                            '/windows nt 10/i'     =>  'Windows 10',
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );
    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $os_platform    =   $value;
        }
    }
    return $os_platform;
}
/*
*   Get user browser
*/
function getBrowser() {
	global $_SERVER;
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$browser		=   "Unknown Browser";
	$browser_array  =   array(
							'/msie/i'	   =>  'Internet Explorer',
							'/firefox/i'	=>  'Firefox',
							'/safari/i'	 =>  'Safari',
							'/chrome/i'	 =>  'Chrome',
							'/edge/i'	   =>  'Edge',
							'/opera/i'	  =>  'Opera',
							'/netscape/i'   =>  'Netscape',
							'/maxthon/i'	=>  'Maxthon',
							'/konqueror/i'  =>  'Konqueror',
							'/mobile/i'	 =>  'Handheld Browser'
						);
	foreach ($browser_array as $regex => $value) {
		if (preg_match($regex, $user_agent)) {
			$browser	=   $value;
		}
	}
	return $browser;
}
/*
*   Get user country
*/
function getCountry() {
	if (!isset($_COOKIE['country'])) {
		//get country from api
		$ch = curl_init('http://api.ipstack.com/41.58.250.241?access_key=1b478826a244ca44621b0773e1a39fbd&format=1');
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		$json = '';
		if (($json = curl_exec($ch)) !== false) {
			// return country code
			$country = json_decode($json,true)['country_code'];
			// Save for one month
			setcookie("country",$country,time()+2592000);
		}
		else
		{
			// return false if api failed
			$country = false;
		}
		curl_close($ch);
	}
	else
	{
		$country = $_COOKIE['country'];
	}
	return $country;
}
/*
*   Get referrer
*/
function getReferrer() {
	global $_SERVER;
	$user_referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER']: '';
	$referrer = implode(array_slice(explode('/', preg_replace('/https?:\/\/(www\.)?/', '', $user_referrer)), 0, 1));
	return $referrer;
}
/*
* 	Time difference
* 	$old : The compared time.
* 	$level : Time level.
*/
function timegap($old,$level = 0) {
	$time = time();
	$dif = $time-$old;
	$names = array('second','minute','hour','day','week','month','year','decade');
	$length = array(1,60,3600,86400,604800,2630880,31570560,315705600);
	for($v = sizeof($length)-1; ($v >= 0)&&(($no = $dif/$length[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $time-($dif%$length[$v]);
	$no = floor($no); if($no <> 1) $names[$v] .='s'; $gap=sprintf("%d %s ",$no,$names[$v]);
	if(($level > 0)&&($v >= 1)&&(($time-$_tm) > 0)) $gap .= ' and '.timegap($_tm,--$level);
	return $gap;
}
/*
* 	Percentage change
* 	$old : The old figure.
* 	$new : The new figure.
*/
function p($old, $new) {
	if (($old != 0) && ($new != 0)) {
		$percentChange = (1 - $old / $new) * 100;
	}
	else {
		$percentChange = 0;
	}
	return number_format($percentChange);
}
/*
* 	Currency
* 	$dollar_price : price.
*/
function c($dollar_price) {
	if (!isset($_COOKIE['currency'])) {
		// Use default currency
		$currency = DB::select('SELECT code FROM currency ORDER BY `default` DESC LIMIT 1')[0]->code;
	} else {
		// Use user currency
		$currency = $_COOKIE['currency'];
	}
	$check = DB::select("SELECT COUNT(*) as count FROM currency WHERE code = '".escape($currency)."'")[0];
	if ($check->count > 0) {
		$rate = DB::select("SELECT rate FROM currency WHERE code = '".escape($currency)."' LIMIT 1")[0]->rate;
		$price = (!empty($dollar_price) ? $dollar_price*$rate : '');
		return $price.$currency;
	} else {
		$price = (!empty($dollar_price) ? $dollar_price : '');
		return $price.'$';
	}
}
/*
* 	Returns country name .
* 	$iso : country iso code.
*/
function country($iso) {
	$country = DB::select("SELECT nicename FROM country WHERE iso = '$iso' LIMIT 1")[0]->nicename;
	return $country;
}
/*
* 	Order status
* 	$i : Status id.
*/
function status($i) {
	switch ($i){
	case 1:
	$s = "Pending";
	break;
	case 2:
	$s = "Shipped";
	break;
	case 3:
	$s = "Delivered";
	break;
	case 4:
	$s = "Canceled";
	break;
	}
	return $s;
}
/*
* 	PDO Escape
* 	$word : The word to be escaped.
*/
function escape($word){
	return substr(DB::connection()->getPdo()->quote($word),1,-1);
}
/*
* 	Translation
* 	$word : The word to translate.
*/
function translate($word){
	$cfg = DB::select('SELECT * FROM config WHERE id = 1')[0];
	// Set frontend language
	if (!isset($_COOKIE['lang'])) {
		// Use default language
		$lang = $cfg->lang;
	} else {
		// Use user language
		$lang = $_COOKIE['lang'];
	}
	if ($cfg->translations == 0)
	{
		// Desactivate translation
		return $word;
	}
	else
	{
		$word = escape($word);
		// Fetching for translation
		$wordc =  DB::select("SELECT COUNT(*) as count FROM translate WHERE word = '".$word."' AND lang = '".$lang."'")[0];
		if ($wordc->count > 0)
		{
			// Return translation
			$translation = DB::select("SELECT translation FROM translate WHERE word = '".$word."' AND lang = '".$lang."'")[0];
			return $translation->translation;
		}
		else
		{
			// Add translation to database and return word
			DB::insert("INSERT INTO translate (lang,word,translation) VALUE ('".$lang."','".$word."','".$word."')");
			return $word;
		}
	}
}
/*
* 	Customer
* 	$info : The information to be retrieved.
*/
function customer($info){
	if (session('customer') == '') {
		return '';
	}
	$customer = DB::select("SELECT * FROM customers WHERE sid = '".session('customer')."'")[0];
	if (isset($customer->$info)) {
		return $customer->$info;
	} else {
		return '';
	}
}
