<?php echo $header?>
<div class="container">
	<h1 class="title"><?=translate('Blog') ?></h1>
	<div class="row m50">
		<?php
			foreach($posts as $post){
				echo '<div class="col-md-4">
					<a data-title="'.translate($post->title).'" class="smooth" href="blog/'.path($post->title,$post->id).'">
					<div class="post" id="'.$post->id.'">
					<div class="post-image" style="background-image:url(\''.url('/assets/blog/'.$post->images).'\')"></div>
						<h4>'.translate($post->title).'</h4>
						<div class="i">
							<div class="pull-left"><i class="icon-clock"></i> '.translate('Posted ').timegap($post->time).translate(' ago').'</div>
							<div class="pull-right"><i class="icon-eye"></i> '.$post->visits.' '.translate('Views').'</div>
							<div class="clearfix"></div>
						</div>
					</div>
					</a>
				</div>';
			}
		?>
	</div>
</div>
<?php echo $footer?>