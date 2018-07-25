<?php 
	echo $header;
	echo $notices;
?>
<form action="" method="post" class="form-horizontal single">
	<?=csrf_field()?>
	<h5>Profile</h5>
	<fieldset>
		<div class="form-group">
			<label class="control-label">Your name</label>
			<input name="name" type="text"  value="<?=$user->u_name;?>" class="form-control" />
		</div>
		<div class="form-group">
			<label class="control-label">Your Email</label>
			<input name="email" type="text"  value="<?=$user->u_email;?>" class="form-control" />
		</div>
		<div class="form-group">
			<label class="control-label">Password</label>
			<input name="pass" type="password"  class="form-control" />
		</div>
		<input name="update" type="submit" value="Update" class="btn btn-primary" />
	</fieldset>
</form>
<?php echo $footer;?>