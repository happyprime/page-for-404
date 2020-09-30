# Page for 404

Page for 404 is a WordPress plugin that allows you to configure one of a site's pages as the source of content for its 404 page.

The setting is handled similar to the default `page_on_front` and `page_for_posts` options maintaned by WordPress. Once the plugin is installed and activated, an option is available to set the `page_for_404` setting through **Settings** -> **Reading**.

Once a page is configured as the page for 404, the plugin tries to find a matching PHP template that will handle the request in this order:

* In the site's active theme:
  * `page-for-404.php
  * `page-{the-configured-page-slug}.php`
  * `page-{the-configured-page-id}.php`
* And, if none of those is available, in this plugin:
  * `templates/404.php`

The template included in this plugin uses markup that matches that of the TwentyTwenty theme provided with WordPress.

To add support for the plugin in your theme (or child theme), make sure one of the first three template files is available and add `\PageFor404\setup_post_global();` at the top, preferrably above `get_header();`. This will cause the global `$post` variable to be populated with data from the page that has been configured through the plugin.

Note that the standard loop will not work at the moment. Instead, you can use templating functions under the assumption that you're in the loop already:

```
<main id="site-content" role="main">
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
```

All of this is subject to change as long as this note is here. ;)
