<?php
	echo $header;
	if(isset($_GET['reply']))
	{
		echo $notices.'<form action="" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="support"><i class="icon-arrow-left"></i></a>Reply to : '.$ticket->title.'</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">E-mail title</label>
							<input name="title" type="text" value="RE : '.$ticket->title.'" class="form-control" required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">E-mail reply</label>
							<textarea class="form-control" name="reply" id="reply" rows="10" cols="80" required></textarea>
						  </div>
						  <input name="send" type="submit" value="Send E-mail" class="btn btn-primary" />
					</fieldset>
				</form>';
	} else {
	?>
	<div class="head">
	<h3>Support</h3>
	<p>Manage customers tickets </p>
	</div>
	<?php
		echo $notices;
		foreach ($tickets as $ticket){
			echo'
			<div class="bloc">
				<h5>
					'.$ticket->title.'
					<div class="tools">
						<a href="support?reply='.$ticket->id.'"><i class="icon-action-undo "></i></a>
					</div>
				</h5>
				<p>'.nl2br(htmlspecialchars($ticket->message)).'</p>
				<b>'.$ticket->name.' - '.$ticket->email.'</b>
			</div>';
		}
	}
	echo $footer;
	?>