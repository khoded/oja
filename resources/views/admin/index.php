<?=$header?>
<div class="single">
	<div class="col-lg-3 s">
		<div class="stats">
			<div class="shead">
				<h5><i class="icon-list"></i> Orders<label><?php echo $stat['0'];?></label></h5>
			</div>
			<canvas id="orders" width="100" height="60"></canvas>
			<div class="foot">
				<label><?php echo ($porders < 0)?'&#9660 '.$porders:'&#9650 '.$porders;?>%</label>
			</div>
		</div>
	</div>
	<div class="col-lg-3 s">
		<div class="stats">
			<div class="shead">
				<h5><i class="icon-users"></i> Visitors<label><?php echo $cfg->views;?></label></h5>
			</div>
			<canvas id="visitors" width="100" height="60"></canvas>
			<div class="foot">
				<label><?php echo ($pvisits <0)?'&#9660 '.$pvisits:'&#9650 '.$pvisits;?>%</label>
			</div>
		</div>
	</div>
	<div class="col-lg-3 s">
		<div class="stats">
			<div class="shead">
				<h5><i class="icon-basket-loaded"></i>  Sales<label><?php echo $ssales;?> $</label></h5>
			</div>
			<canvas id="sales" width="100" height="60"></canvas>
			<div class="foot">
				<label><?php echo ($psales < 0)?'&#9660 '.$psales:'&#9650 '.$psales;?>%</label>
			</div>
		</div>
	</div>
	<div class="col-lg-3 s">
		<div class="stats">
			<div class="shead">
				<h5><i class="icon-share-alt"></i> Conversion rate<label><?php echo number_format($stat['0'] / $cfg->views*100,2);?>%</label></h5>
			</div>
			<canvas id="conversion" width="100" height="60"></canvas>
			<div class="foot">
				<label><?php echo ($pconversion < 0)?'&#9660 '.$pconversion:'&#9650 '.$pconversion;?>%</label>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="list">
			<div class="title">
				<i class="icon-list"></i>Orders
			</div>
			<?php
				$count = 0;
				foreach($orders as $order){
					echo'<div class="item order">
					<h6>'.$order->name.'<div class="tools">
					<a href="orders?delete='.$order->id.'"><i class="icon-trash"></i></a>
					</div></h6>
					<p>'.$order->email.'</p></div>';
					$count++;
				}
				if($count == 0){
					echo '<div class="item nothing"><i class="icon-ghost"></i><h5>No orders</h5></div>';
				}
			?>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="list">
			<div class="title">
				<i class="icon-list"></i>Reviews
			</div>
			<?php
				$count = 0;
				foreach ($reviews as $review){
					echo'<div class="item review">
					<h6>'.$review->name.'<div class="tools">';
					echo ($review->active != 1) ? '<a href="reviews?approve='.$review->id.'"><i class="icon-like "></i></a>' : '<i class="icon-check"></i>';
					echo '</div></h6>
					<p>'.$review->email.'</p></div>';
					$count++;
				}
				if($count == 0){
					echo '<div class="item nothing"><i class="icon-ghost"></i><h5>No reviews</h5></div>';
				}
			?>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="list">
			<div class="title">
				<i class="icon-support"></i>Tickets
			</div>
			<?php 
				$count = 0;
				foreach ($tickets as $ticket){
					echo'<div class="item ticket">
					<h6>'.$ticket->name.'<div class="tools">
					<a href="support?reply='.$ticket->id.'"><i class="icon-action-undo"></i></a>
					</div></h6>
					<p>'.$ticket->email.'</p></div>';
					$count++;
				}
				if($count == 0){
					echo '<div class="item nothing"><i class="icon-ghost"></i><h5>No tickets</h5></div>';
				}
			?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-lg-4">
		<div class="list">
			<div class="title">
				<i class="icon-share"></i>Referrers
			</div>
			<?php 
				foreach ($referrers as $referrer){
					echo'<div class="item referrer">
					<h6><img src="https://www.google.com/s2/favicons?domain='.$referrer->referrer.'">'.$referrer->referrer.'<div class="data"><b>'.$referrer->visits.'</b> visit</div></h6>
					</div>';
				}
			?>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="list">
			<div class="title">
				<i class="icon-screen-desktop"></i>Operation systems
			</div>
			<?php
				foreach ($oss as $os){
					echo'<div class="item os">
					<h6>'.$os->os.'<div class="data"><b>'.$os->visits.'</b> visit</div></h6>
					</div>';
				}
			?>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="list">
			<div class="title">
				<i class="icon-cursor"></i>Browsers
			</div>
			<?php
				foreach ($browsers as $browser){
					echo'<div class="item browser">
					<h6>'.$browser->browser.'<div class="data"><b>'.$browser->visits.'</b> visit</div></h6>
					</div>';
				}
			?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="col-lg-4">
		<div class="list">
			<div class="title">
				<i class="icon-envelope-letter"></i>Subscribers stats
			</div>
			<div class="item">
				<h3><?=$emails['orders']?></h3>
				<p>Order emails</p>
			</div>
			<div class="item">
				<h3><?=$emails['newsletter']?></h3>
				<p>Newsletter emails</p>
			</div>
			<div class="item">
				<h3><?=$emails['support']?></h3>
				<p>Support emails</p>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="list">
			<div class="title">
				<i class="icon-envelope-letter"></i>Newsletter subscribers
			</div>
			<?php
				foreach ($subscribers as $subscriber){
					echo'<div class="item subscriber">
					<h6>'.$subscriber->email.'</h6>
					</div>';
				}
			?>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="list">
			<div class="title">
				<i class="icon-note"></i>Quick newsletter
			</div>
			<div class="item">
				<form action="newsletter" style="padding: 0px 15px;" method="get" class="form-horizontal">
					<?=csrf_field()?>
					<div class="form-group">
						<label class="control-label">E-mail title</label>
						<input name="title" class="form-control" type="text">
					</div>
					<div class="form-group">
						<label class="control-label">E-mail content</label>
						<textarea name="content" class="form-control" type="text"></textarea>
					</div>
					<input style="margin-top: 10px;" name="send" value="Send" class="btn btn-primary" type="submit">
				</form>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="map-gradient" style="box-shadow: 0px 0px 21px 1px rgb(200, 205, 225);border-radius: 8px;">
			<div class="col-md-7" id="vmap" style="border-radius: 8px 0px 0px 8px;height: 440px;"></div>
			<div class="col-md-5" style="border-radius: 0px 8px 8px 0px;">
				<table class="table top-country table-hover">
					<thead>
						<tr>
							<th>Country</th>
							<th>Visitors</th>
							<th>Orders</th>
						</tr>
					</thead>
					<tbody>
						<?php
							foreach ($countries as $country) {
								echo '<tr><td>'.$country->nicename.'</td><td>'.$country->visitors.'</td><td>'.$country->orders.'</td></tr>';
							}
						?>
					</tbody>
				</table>
			</div>
			<div class="clearfix"></div>
			
		</div>
	</div>
</div>
</div>
<link type="text/css" href="<?php echo $tp;?>/assets/jqvmap.css" rel="stylesheet" media="screen" />
<script type="text/javascript" src="<?php echo $tp;?>/assets/jquery.vmap.js"></script> 
<script type="text/javascript" src="<?php echo $tp;?>/assets/jquery.vmap.world.js"></script> 
<script type="text/javascript">
	<!--
	$(document).ready(function() {
		$.ajax({
			url: 'map',
			dataType: 'json',
			success: function(json) {
				data = [];
				for (i in json) {
					data[i] = json[i]['visitors'];
				}
				$('#vmap').vectorMap({
					map: 'world_en',
					backgroundColor: '#FFFFFF',
					borderColor: '#FFFFFF',
					color: '#9FD5F1',
					hoverOpacity: 0.7,
					selectedColor: '#FFF',
					enableZoom: true,
					showTooltip: true,
					values: data,
					normalizeFunction: 'polynomial',
					onLabelShow: function(event, label, code) {
						if (json[code]) {
							label.html('<strong>' + label.text() + '</strong><br />' + 'Total visitors : ' + json[code]['visitors'] + '<br />' + 'Total orders : ' + json[code]['total']);
						}
					}
				});
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	});
	//-->
	var orders = document.getElementById("orders");
	var myChart = new Chart(orders, {
		type: 'line',
		data: {
			labels: [<?php echo $chart['days'];?>],
			datasets: [{
				label: 'Orders',
				data: [<?php echo $o;?>],
				fill: true,
				backgroundColor: '#2196f3',
				borderColor: '#2196f3',
				borderWidth: 2,
				borderCapStyle: 'butt',
				borderDash: [],
				borderDashOffset: 0.0,
				borderJoinStyle: 'miter',
				pointBorderColor: '#2196f3',
				pointBackgroundColor: '#fff',
				pointBorderWidth: 2,
				pointHoverRadius: 4,
				pointHoverBackgroundColor: '#2196f3',
				pointHoverBorderColor: '#fff',
				pointHoverBorderWidth: 2,
				pointRadius: [0, 4, 4, 4, 4, 4, 0],
				pointHitRadius: 10,
				spanGaps: false
			}]
		},
		options: {
			scales: {
				xAxes: [{
					display: false
				}],
				yAxes: [{
					display: false,
					ticks: {
						min: 0,
						max: <?php echo $morders;?>
					}
				}]
			},
			legend: {
				display: false
			}
		}
	});
	var visitors = document.getElementById("visitors");
	var myChart = new Chart(visitors, {
		type: 'line',
		data: {
			labels: [<?php echo $chart['days'];?>],
			datasets: [{
				label: 'Visitors',
				data: [<?php echo $i;?>],
				fill: true,
				backgroundColor: '#2ecc71',
				borderColor: '#2ecc71',
				borderWidth: 2,
				borderCapStyle: 'butt',
				borderDash: [],
				borderDashOffset: 0.0,
				borderJoinStyle: 'miter',
				pointBorderColor: '#2ecc71',
				pointBackgroundColor: '#fff',
				pointBorderWidth: 2,
				pointHoverRadius: 4,
				pointHoverBackgroundColor: '#2ecc71',
				pointHoverBorderColor: '#fff',
				pointHoverBorderWidth: 2,
				pointRadius: [0, 4, 4, 4, 4, 4, 0],
				pointHitRadius: 10,
				spanGaps: false
			}]
		},
		options: {
			scales: {
				xAxes: [{
					display: false
				}],
				yAxes: [{
					display: false,
					ticks: {
						min: 0,
						max: <?php echo $mvisits;?>
					}
				}]
			},
			legend: {
				display: false
			}
		}
	});
	var conversion = document.getElementById("conversion");
	var myChart = new Chart(conversion, {
		type: 'line',
		data: {
			labels: [<?php echo $chart['days'];?>],
			datasets: [{
				label: 'Conversion rate',
				data: [<?php echo $c;?>],
				fill: true,
				backgroundColor: 'rgb(146, 109, 222)',
				borderColor: 'rgb(146, 109, 222)',
				borderWidth: 2,
				borderCapStyle: 'butt',
				borderDash: [],
				borderDashOffset: 0.0,
				borderJoinStyle: 'miter',
				pointBorderColor: 'rgb(146, 109, 222)',
				pointBackgroundColor: '#fff',
				pointBorderWidth: 2,
				pointHoverRadius: 4,
				pointHoverBackgroundColor: 'rgb(146, 109, 222)',
				pointHoverBorderColor: '#fff',
				pointHoverBorderWidth: 2,
				pointRadius: [0, 4, 4, 4, 4, 4, 0],
				pointHitRadius: 10,
				spanGaps: false
			}]
		},
		options: {
			scales: {
				xAxes: [{
					display: false
				}],
				yAxes: [{
					display: false,
					ticks: {
						min: 0,
						max: <?php echo $mconversion;?>
					}
				}]
			},
			legend: {
				display: false
			}
		}
	});
	var sales = document.getElementById("sales");
	var myChart = new Chart(sales, {
		type: 'line',
		data: {
			labels: [<?php echo $chart['days'];?>],
			datasets: [{
				label: 'Sales',
				data: [<?php echo $s;?>],
				fill: true,
				backgroundColor: 'rgb(239, 25, 60)',
				borderColor: 'rgb(239, 25, 60)',
				borderWidth: 2,
				borderCapStyle: 'butt',
				borderDash: [],
				borderDashOffset: 0.0,
				borderJoinStyle: 'miter',
				pointBorderColor: 'rgb(239, 25, 60)',
				pointBackgroundColor: '#fff',
				pointBorderWidth: 2,
				pointHoverRadius: 4,
				pointHoverBackgroundColor: 'rgb(239, 25, 60)',
				pointHoverBorderColor: '#fff',
				pointHoverBorderWidth: 2,
				pointRadius: [0, 4, 4, 4, 4, 4, 0],
				pointHitRadius: 10,
				spanGaps: false
			}]
		},
		options: {
			scales: {
				xAxes: [{
					display: false
				}],
				yAxes: [{
					display: false,
					ticks: {
						min: 0,
						max: <?php echo $msales;?>
					}
				}]
			},
			legend: {
				display: false
			}
		}
	});
	</script>
<?=$footer?>