<?php
	echo $header;
	if(isset($_GET['add']))
	{
		echo $notices.'<form action="" method="post" enctype="multipart/form-data" class="form-horizontal single">
				'.csrf_field().'
				<h5><a href="products"><i class="icon-arrow-left"></i></a>Add new product</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Product name</label>
							<input name="title" type="text"  class="form-control" required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">Product category</label>
							<select name="category" class="form-control">';
							foreach ($categories as $category){
								echo '<option value="'.$category->id.'">'.$category->name.'</option>';
								$childs = DB::select("SELECT * FROM category WHERE parent = ".$category->id." ORDER BY id DESC");
								foreach ($childs as $child){
									echo '<option value="'.$child->id.'">- '.$child->name.'</option>';
								}
							}
							echo '</select>
						  </div>
						  <div class="form-group">
							<label class="control-label">Product description</label>
							<textarea name="text" type="text" class="form-control" required></textarea>
						  </div>
						  <div class="form-group">
							<label class="control-label">Product price</label>
							<input name="price" type="text" class="form-control" required />
						  </div>
						  <div class="form-group">
							<label class="control-label">Available quantity</label>
							<input name="q" type="text" class="form-control" required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">Product images</label>
							<input type="file" class="form-control" name="images[]" multiple="multiple" accept="image/*"  required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">Download ( digital product )</label>
							<input type="file" class="form-control" name="download"/>
						  </div>
						  <div class="form-group">
							  <label class="control-label">Customer options</label>
							  <input type="hidden" id="option_count"></input>
							  <div id="options"></div>
							  <div id="add_option" class="button pull-right">Add field</div>
                          </div>
						  <input name="add" type="submit" value="Add product" class="btn btn-primary" />
					</fieldset>
				</form>';
	} elseif(isset($_GET['edit'])){
		echo $notices.'<form action="" method="post" enctype="multipart/form-data" class="form-horizontal single">
			'.csrf_field().'
				<h5><a href="products"><i class="icon-arrow-left"></i></a>Update product</h5>
					<fieldset>
						  <div class="form-group">
							<label class="control-label">Product name</label>
							<input name="title" type="text" value="'.$product->title.'" class="form-control"  required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">Product description</label>
							<textarea name="text" type="text" class="form-control" required>'.$product->text.'</textarea>
						  </div>
						  <div class="form-group">
							<label class="control-label">Product category</label>
							<select name="category" class="form-control">';
							foreach ($categories as $category){
								echo '<option value="'.$category->id.'" '.($category->id == $product->category ? 'selected' : '').'>'.$category->name.'</option>';
								$childs = DB::select("SELECT * FROM category WHERE parent = ".$category->id." ORDER BY id DESC");
								foreach ($childs as $child){
									echo '<option value="'.$child->id.'" '.($child->id == $product->category ? 'selected' : '').'>- '.$child->name.'</option>';
								}
							}
							echo '</select>
						  </div>
						  <div class="form-group">
							<label class="control-label">Product price</label>
							<input name="price" type="text" value="'.$product->price.'" class="form-control"  required/>
						  </div>
						  <div class="form-group">
							<label class="control-label">Available quantity</label>
							<input name="quantity" type="text" value="'.$product->quantity.'" class="form-control"  required/>
						  </div><div class="row">';
						  if (!empty($product->images)){
							  echo '<p>Uploading new images will overwrtite current images .</p>';
							  $images = explode(',',$product->images);
							  foreach($images as $image){
									echo '<img class="col-md-2" src="'.url('/assets/products/'.$image).'" />';
							  }
							  echo '<div class="clearfix"></div>';
						  }
						  echo '</div><div class="form-group">
							<label class="control-label">Product images</label>
							<input type="file" class="form-control" name="images[]" multiple="multiple" accept="image/*"/>
						  </div>';
						  if (!empty($product->download)){
							  echo '<p>Uploading new file will overwrite current file .</p>';
						  }
						  echo '
						  <div class="form-group">
							<label class="control-label">Download</label>
							<input type="file" class="form-control" name="download"/>
						  </div>
						  <div class="form-group">
							  <label class="control-label">Customer options</label>
							  <input type="hidden" id="option_count" value="'.count(json_decode($product->options,true)).'"></input>
							  <div id="options">';
								$options = json_decode($product->options,true);
                                if(!empty($options)){
                                    foreach($options as $i=>$row){
								?>
								<div class="form-group" data-no="<?php echo $row['no']; ?>">
									<div class="col-sm-6">
										<input name="option_title[]" class="form-control" placeholder="Title" type="text" value="<?php echo $row['title']; ?>">
									</div>
									<div class="col-sm-5">
										<select class="form-control option_type" name="option_type[]">
											<option value="text" <?php if($row['type'] == 'text'){ echo 'selected'; } ?>>Text input</option>
											<option value="select" <?php if($row['type'] == 'select'){ echo 'selected'; } ?>>Dropdown - single select</option>
											<option value="multi_select" <?php if($row['type'] == 'multi_select'){ echo 'selected'; } ?>>Checkbox - multi select</option>
											<option value="radio" <?php if($row['type'] == 'radio'){ echo 'selected'; } ?>>Radio</option>
										</select>
										<div class="options">
											<?php
                                            if($row['type'] == 'text'){
                                        ?>
                                            <input type="hidden" name="option_set<?php echo $row['no']; ?>[]" value="none" >
                                        <?php
                                            } else {
                                        ?>
											<?php foreach ($row['option'] as $key => $row1) { ?>
											<div style="margin: 10px -15px;">
												<div class="col-sm-10">
													<input value="<?php echo $row1; ?>" type="text" name="option_set<?php echo $row['no']; ?>[]" class="form-control required"  placeholder="Option">
												</div>
												<div class="col-sm-1">
												  <span class="remove-option mini-button"><i class="icon-close"></i></span>
												</div>
												<div class="clearfix"></div>
											</div>
											<?php } ?>
											<div class="pull-right button add_option">Add option</div>
										<?php } ?>
										</div>
									</div>
									<input name="option_no[]" value="<?php echo $row['no']; ?>" type="hidden">
									<div class="col-sm-1"> <span class="remove mini-button"><i class="icon-close"></i></span> </div>
								</div>
								<?php
										}
									}
								?>
								</div>
								<div id="add_option" class="button pull-right">Add field</div>
							</div>
						  <input name="edit" type="submit" value="Update product" class="btn btn-primary" />
					</fieldset>
				</form>
<?php } else { ?>
	<div class="head">
	<h3>Products<a href="products?add" class="add">Add product</a></h3>
	<p>Manage your website stock & products </p>
	</div>
<?php
	echo $notices;
	foreach ($products as $product){
	echo '<div class=" col-md-3">
		<div class="product">
			<div class="pi">
				<img src="../assets/products/'.image_order($product->images).'">
			</div>
			<h5>'.$product->title.'</h5>
			<b>'.c($product->price).'</b>
			<div class="tools">
				<a href="products?delete='.$product->id.'"><i class="icon-trash"></i></a>
				<a href="products?edit='.$product->id.'"><i class="icon-pencil"></i></a>
			</div>
		</div>
	</div>';}
	}
?>	
<style>
	.button {
		background: gainsboro;
		padding: 5px 20px;
		cursor: pointer;
		border-radius: 30px;
	}
	.mini-button {
		cursor: pointer;
		border-radius: 30px;
		background: transparent;
		padding: 5px 0px;
		display: block;
	}
</style>
<script>
    function option_count(type){
        var count = $('#option_count').val();
        if(type == 'add'){
            count++;
        }
        if(type == 'reduce'){
            count--;
        }
        $('#option_count').val(count);
    }
    
    $("#add_option").click(function(){
        option_count('add');
        var co = $('#option_count').val();
        $("#options").append(''
            +'<div class="form-group" data-no="'+co+'">'
            +'    <div class="col-sm-6">'
            +'        <input type="text" name="option_title[]" class="form-control"  placeholder="Title">'
            +'    </div>'
            +'    <div class="col-sm-5">'
            +'        <select class="form-control option_type" name="option_type[]" >'
            +'            <option value="text">Text input</option>'
            +'            <option value="select">Dropdown - single select</option>'
            +'            <option value="multi_select">Checkbox - multi select</option>'
            +'            <option value="radio">Radio</option>'
            +'        </select>'
            +'        <div class="options">'
            +'          <input type="hidden" name="option_set'+co+'[]" value="none" >'
            +'        </div>'
            +'    </div>'
            +'    <input type="hidden" name="option_no[]" value="'+co+'" >'
            +'    <div class="col-sm-1">'
            +'        <span class="remove mini-button"><i class="icon-close"></i></span>'
            +'    </div>'
            +'</div>'
        );
    });
    
    $("#options").on('change','.option_type',function(){
        var co = $(this).closest('.form-group').data('no');
        if($(this).val() !== 'text'){
            $(this).closest('div').find(".options").html('<div class="pull-right button add_option">Add option</div>');
        } else {
            $(this).closest('div').find(".options").html('<input type="hidden" name="option_set'+co+'[]" value="none" >');
        }
    });
    
    $("#options").on('click','.add_option',function(){
        var co = $(this).closest('.form-group').data('no');
        $(this).closest('.options').prepend(''
            +'    <div style="margin: 10px -15px;">'
            +'        <div class="col-sm-10">'
            +'          <input type="text" name="option_set'+co+'[]" class="form-control required"  placeholder="Option">'
            +'        </div>'
            +'        <div class="col-sm-1">'
            +'          <span class="remove-option mini-button"><i class="icon-close"></i></span>'
            +'        </div>'
            +'        <div class="clearfix"></div>'
            +'    </div>'
        );
    });
    
    $('body').on('click', '.remove', function(){
        $(this).parent().parent().remove();
    });

    $('body').on('click', '.remove-option', function(){
        var co = $(this).closest('.form-group').data('no');
        $(this).parent().parent().remove();
        if($(this).parent().parent().parent().html() == ''){
            $(this).parent().parent().parent().html(''
                +'   <input type="hidden" name="option_set'+co+'[]" value="none" >'
            );
        }
    });
</script>