<?php

\PageFor404\setup_post_global();

get_header();

?>
<main id="site-content" class="site-main" role="main">
	<article <?php post_class(); ?>>
		<header class="entry-header has-text-align-center">
			<div class="entry-header-inner section-inner medium">
				<h1 class="entry-title">
					<?php the_title(); ?>
				</h1>
			</div>
		</header>
		<div class="post-inner">
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</div>
	</article>
</main>
<?php

get_footer();
