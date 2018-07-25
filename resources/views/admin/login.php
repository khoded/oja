<!DOCTYPE html>
<html>
	<head>
		<meta charset='utf-8'/>
		<title>Login | <?=$cfg->name?></title>
		<meta name="viewport" content="initial-scale = 1.0,maximum-scale = 1.0" />
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
		<meta content='<?=$cfg->desc?>' name='description'/>
		<meta content='<?=$cfg->key?>' name='keywords'/>
		<!--ASSETS-->
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
		 <link href="https://fonts.googleapis.com/css?family=Montserrat:400" rel="stylesheet"> 
		<link rel="stylesheet" href="<?=$tp;?>/admin/style.css">
		<script src="<?=$tp;?>/assets/jquery.min.js"></script>
		<script src="<?=$tp;?>/assets/bootstrap.min.js"></script>
		<script src="<?=$tp;?>/assets/Chart.min.js"></script>
	</head>
	<body>
		<div class="content-warpper">
			<div class="content">
			<div class="clear"></div>
			<form method="post" action="" class="mini">
				<fieldset>
					<?=(isset($error) ? $error : '')?>

					<img src="../assets/sellerkit.png">
					<?=csrf_field()?>

					<input placeholder="Email" name="email" type="text">
					<input placeholder="Password" name="pass" type="password">
					<input class="submit" name="login" value="Login" type="submit">
				</fieldset>
			</form>
			<div style="clear:both;padding:10px"></div>
			</div>
		</div>
	</body>
</html>