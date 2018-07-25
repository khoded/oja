<?php echo $header?>
	<div class="bheader bg">
		<h2><?=(isset($category->name) ? translate($category->name) : translate('Products'));?></h2>
	</div>
	<div class="container top-filter">
		<div class="filter">
			<form id="search">
				<div class="col-md-3">
					<div class="form-group">
						<input name="search" placeholder="<?=translate('Search keyword')?>" type="text" value="<?=isset($_GET['search'])?$_GET['search']:''?>" class="form-control" />
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group">
						<select name="cat" class="form-control">
							<option value=""><?=translate('Category')?></option>
							<?php foreach($cats as $cat){
								$selected = '';
								if (isset($category->id)) {
									$selected = ($category->id == $cat->id) ? 'selected' : '';
								}
								echo '<option value="'.$cat->id.'" '.$selected.'>'.translate($cat->name).'</option>';
								$childs = DB::select("SELECT * FROM category WHERE parent = ".$cat->id." ORDER BY id DESC");
								foreach ($childs as $child){
									echo '<option value="'.$child->id.'" '.(isset($category->id) ? ($child->id == $category->id ? 'selected' : '') : '').'>- '.$child->name.'</option>';
								}
							}
							?>
						</select>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-price">
						<div class="clearfix"></div>
						<b class="pull-left price"><?=$price['min'] ?></b>
						<b class="pull-right price"><?=$price['max']; ?></b>
						<input name="min" id="min" type="hidden">
						<input name="max" id="max" type="hidden">
						<div id="price"></div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="form-group text-center">
						<button type="submit" class="btn-search bg"><?=translate('Search')?></button>
					</div>
				</div>
				<div class="clearfix"></div>
			</form>
		</div>
	</div>
	<div class="container">
		<div id="listing" class="product-container row space-margin">
			<?php	
				foreach($products as $product){
					echo '<div class="col-md-3">
						<div class="product" id="'.$product->id.'">
							<div class="pi">
								<img src="'.url('/assets/products/'.image_order($product->images)).'"/>
							</div>
							<h5>'.translate($product->title).'</h5>
							<b>'.c($product->price).'</b>
						</div>
						<div class="bg view">
							<h5>'.translate($product->title).'</h5>
							<p>'.mb_substr(translate($product->text),0,200).'</p>
							<a href="product/'.path($product->title,$product->id).'" data-title="'.translate($product->title).'" class="smooth">
								<i class="icon-eye"></i>
								Details
							</a>
						</div>
					</div>';
				}
			?>
			<div class="clearfix"></div>
		</div>
	</div>
<script>
		var handlesSlider = document.getElementById('price');
		noUiSlider.create(handlesSlider, {
			start: [<?=$price['min']?>,<?=$price['max']?>],
			step: 1,
			connect: false,
			range: {'min':<?=$price['min']?>,'max':<?=$price['max']?>},
		});
		var BudgetElement = [document.getElementById('min'),document.getElementById('max')];
		handlesSlider.noUiSlider.on('update', function(values, handle) {
			BudgetElement[0].textContent = values[0];
			BudgetElement[1].textContent = values[1];
			$("#min").val(values[0]);
			$(".pull-left.price").html(values[0]);
			$("#max").val(values[1]);
			$(".pull-right.price").html(values[1]);
		});
</script>
<?php echo $footer?>