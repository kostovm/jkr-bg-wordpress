<?php get_header(); ?>

<div id="main-content">
	<div class="container">
		<div class="magical-404">
			<h1>404</h1>
			<p>
				The URL you have followed does not exist anymore.<br>
				But this doesn’t mean the article you are looking for isn’t here.<br>
				Try to find it by searching below.
			</p>
			<form role="search" method="get" id="searchform" class="searchform"
				action="<?php echo esc_url(home_url('/')); ?>">
				<input type="search" name="s" id="s" placeholder="Search with a flick of your wand..." />
				<button type="submit"><span>🔍</span></button>
			</form>
		</div>
	</div>

	<div class="magic-sparkles">
		<?php for ($i = 0; $i < 40; $i++): ?>
			<div class="sparkle" style="
				top: <?= rand(0, 100); ?>%;
				left: <?= rand(0, 100); ?>%;
				animation-delay: <?= rand(0, 3000) / 1000; ?>s;
				animation-duration: <?= rand(2000, 6000) / 1000; ?>s;
			"></div>
		<?php endfor; ?>
	</div>
</div>

<?php get_footer(); ?>