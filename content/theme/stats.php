<section class="section section-stats">
	<div class="container-xl">
		<h1>Statistics</h1>
	</div>

	<div class="container-xl">
		<div class="stats-box-wrapper">
			<div class="stats-box first">
				
			</div>

			<div class="stats-box second">
				
			</div>

			<div class="stats-box">
				<h4>Summary</h4>
				<ul class="stats table">
					<li>
						<span>Visitors Now</span>
						<span><?=$users_now?></span>
					</li>
					<li>
						<span>Visitors Today</span>
						<span><?=$visitors_today?></span>
					</li>
					<li>
						<span>Hits Today</span>
						<span><?=$hits_today?></span>
					</li>
					<li>
						<span>Mobile Devices</span>
						<span><?=$mobile_users?>%</span>
					</li>
					<li>
						<span>iOS Devices</span>
						<span><?=$ios_devices?>%</span>
					</li>			
					<li>
						<span>Average Visitors /day</span>
						<span><?=$avg_visitors_day?></span>
					</li>
					<li>
						<span>Average Visitors /month</span>
						<span><?=$avg_visitors_month?></span>
					</li>
					<li>
						<span>Average Visitors /year</span>
						<span><?=$avg_visitors_year?></span>
					</li>
					<li>
						<span>Growth Rate</span>
						<span><?=$growth?>%</span>
					</li>				
					<li>
						<span>Total Posts</span>
						<span><?=$total_posts?></span>
					</li>	
					<li>
						<span>Total Pages</span>
						<span><?=$total_pages?></span>
					</li>					

				</ul>
			</div><!-- stats-box -->			

			<div class="stats-box">
				<h4>Hits</h4>
				<ul class="stats table">
					<?php foreach ($daily_hits as $item):?>
						<li>
							<span><?=$item['thedate']?></span>
							<span><?=$item['cnt']?></span>
						</li>
					<?php endforeach;?>
				</ul>
			</div><!-- stats-box -->

			<div class="stats-box">
				<h4>Visits</h4>
				<ul class="stats table">
					<?php foreach ($daily_visitors as $item):?>
						<li>
							<span><?=$item['thedate']?></span>
							<span><?=$item['cnt']?></span>
						</li>
					<?php endforeach;?>
				</ul>
			</div><!-- stats-box -->

			<div class="stats-box">
				<h4>Hourly</h4>
				<ul class="stats table">
					<?php foreach ($hourly_visitors as $item):?>
						<li>
							<span><?=$item['thehour']?>:00</span>
							<span><?=$item['cnt']?></span>
							<span><?=$item['perc']?>%</span>							
						</li>
					<?php endforeach;?>
				</ul>
			</div><!-- stats-box -->

			<div class="stats-box">
				<h4>Popular <span class="color-richer">Posts</span></h4>
				<ul class="stats table">
					<?php foreach ($popular_posts as $item):?>
						<li>
							<span><?=$item['post_id']?></span>
							<span><?=$item['cnt']?></span>					
						</li>
					<?php endforeach;?>
				</ul>
			</div><!-- stats-box -->

			<div class="stats-box">
				<h4>Popular <span class="color-richer">Pages</span></h4>
				<ul class="stats table">
					<?php foreach ($popular_pages as $item):?>
						<li>
							<span><?=$item['page_slug']?></span>
							<span><?=$item['cnt']?></span>					
						</li>
					<?php endforeach;?>
				</ul>
			</div><!-- stats-box -->

			<div class="stats-box">
				<h4>Countries</h4>
				<ul class="stats table">
					<?php foreach ($countries as $item):?>
						<li>
							<span class="flag"><img src="<?=site_images().'flags/'.strtolower($item['country_code']).'.svg'?>"></span> 
							<span><?=$item['country']?></span>
							<span><?=$item['cnt']?></span>
							<span><?=$item['perc']?>%</span>					
						</li>
					<?php endforeach;?>
				</ul>
			</div><!-- stats-box -->											

		</div><!-- stats-box-wrapper -->

		
	</div><!-- container-xl -->

</section>



