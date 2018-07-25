<?php
	echo $header;
	if(isset($_GET['add']))
	{
		echo $notices.'<form action="" method="post" enctype="multipart/form-data" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="blog"><i class="icon-arrow-left"></i></a>Add new Post</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Post title</label>
							<input name="title" type="text"  class="form-control" required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">Post content</label>
							<textarea class="form-control" name="content" id="content" rows="10" cols="80" required></textarea>
						  </div>
							<div class="form-group">
								<label class="control-label">Post image</label>
								<input type="file" class="form-control" name="image" accept="image/*"/>
							</div>
						  <input name="add" type="submit" value="Add Post" class="btn btn-primary" />
					</fieldset>
				</form></div></div>';
	}
	elseif(isset($_GET['edit']))
	{
		echo $notices.'<form action=""  enctype="multipart/form-data" method="post" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="blog"><i class="icon-arrow-left"></i></a>Edit Post</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Post title</label>
							<input name="title" value="'.$post->title.'" type="text"  class="form-control" required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">Post content</label>
							<textarea name="content" class="form-control" id="content" rows="10" cols="80" required>'.$post->content.'</textarea>
						  </div><div class="row">';
						  if (!empty($post->images)){
							echo '<p>Uploading new images will overwrtite current images .</p>
							<img class="col-md-4" src="'.url('/assets/blog/'.$post->images).'" />
							<div class="clearfix"></div><br/>';
						  }
						  echo '</div><div class="form-group">
								<label class="control-label">Post image</label>
								<input type="file" class="form-control" name="image" accept="image/*"/>
						  </div>
						  <input name="edit" type="submit" value="Edit Post" class="btn btn-primary" />
					</fieldset>
				</form>';
	} else {
	?>
	<div class="head">
	<h3>Blog<a href="blog?add" class="add">Add post</a></h3>
	<p>Create blog posts & boost customers engagement </p>
	</div>
	<?php
		echo $notices;
		foreach ($posts as $post){
			echo'<div class="bloc">
				<h5>
					<a href="../blog/'.path($post->title,$post->id).'">'.$post->title.'</a>
					<div class="tools">
						<a href="blog?delete='.$post->id.'"><i class="icon-trash"></i></a>
						<a href="blog?edit='.$post->id.'"><i class="icon-pencil"></i></a>
					</div>
				</h5>
				<p>'.mb_substr(strip_tags($post->content),0,130).'...</p>
			</div>';
		}
	}
	echo $footer;
?>