<?php
	/*
	Template Name: Full Width Page (Alt)
	*/
?>

<?php get_header('alt'); ?>

<?php
	$options = get_option('sf_dante_options');
	
	$default_sidebar_config = $options['default_sidebar_config'];
	$default_left_sidebar = strtolower($options['default_left_sidebar']);
	$default_right_sidebar = strtolower($options['default_right_sidebar']);
	
	$pb_active = sf_get_post_meta($post->ID, '_spb_js_status', true);
	
	$sidebar_config = sf_get_post_meta($post->ID, 'sf_sidebar_config', true);
	$left_sidebar = strtolower(sf_get_post_meta($post->ID, 'sf_left_sidebar', true));
	$right_sidebar = strtolower(sf_get_post_meta($post->ID, 'sf_right_sidebar', true));
	
	if ($sidebar_config == "") {
		$sidebar_config = $default_sidebar_config;
	}
	if ($left_sidebar == "") {
		$left_sidebar = $default_left_sidebar;
	}
	if ($right_sidebar == "") {
		$right_sidebar = $default_right_sidebar;
	}
	
	sf_set_sidebar_global($sidebar_config);
	
	$page_wrap_class = '';
	if ($sidebar_config == "left-sidebar") {
	$page_wrap_class = 'has-left-sidebar has-one-sidebar row';
	} else if ($sidebar_config == "right-sidebar") {
	$page_wrap_class = 'has-right-sidebar has-one-sidebar row';
	} else if ($sidebar_config == "both-sidebars") {
	$page_wrap_class = 'has-both-sidebars row';
	} else {
	$page_wrap_class = 'has-no-sidebar';
	}
	
	$remove_bottom_spacing = sf_get_post_meta($post->ID, 'sf_no_bottom_spacing', true);
	$remove_top_spacing = sf_get_post_meta($post->ID, 'sf_no_top_spacing', true);
	
	if ($remove_bottom_spacing) {
	$page_wrap_class .= ' no-bottom-spacing';
	}
	if ($remove_top_spacing) {
	$page_wrap_class .= ' no-top-spacing';
	}
		
?>

<div class="inner-page-wrap <?php echo $page_wrap_class; ?> clearfix">
		
	<?php if (have_posts()) : the_post(); ?>

	<!-- OPEN page -->
	<?php if (($sidebar_config == "left-sidebar") || ($sidebar_config == "right-sidebar")) { ?>
	<div <?php post_class('clearfix col-sm-8'); ?> id="<?php the_ID(); ?>">
	<?php } else if ($sidebar_config == "both-sidebars") { ?>
	<div <?php post_class('clearfix col-sm-9'); ?> id="<?php the_ID(); ?>">
	<?php } else { ?>
	<div <?php post_class('clearfix'); ?> id="<?php the_ID(); ?>">
	<?php } ?>
	
		<?php if ($sidebar_config == "both-sidebars") { ?>
		<div class="row">	
			<div class="page-content col-sm-8">
				<?php the_content(); ?>
			</div>
				
			<aside class="sidebar left-sidebar col-sm-4">
				<?php dynamic_sidebar($left_sidebar); ?>
			</aside>
		</div>
		<?php } else { ?>
		
		<div class="page-content clearfix">

			<?php the_content(); ?>
			
		</div>
		
		<?php } ?>	
	
	<!-- CLOSE page -->
	</div>

	<?php endif; ?>
	
	<?php if ($sidebar_config == "left-sidebar") { ?>
		
		<aside class="sidebar left-sidebar col-sm-4">
			<?php dynamic_sidebar($left_sidebar); ?>
		</aside>

	<?php } else if ($sidebar_config == "right-sidebar") { ?>
		
		<aside class="sidebar right-sidebar col-sm-4">
			<?php dynamic_sidebar($right_sidebar); ?>
		</aside>
		
	<?php } else if ($sidebar_config == "both-sidebars") { ?>
		
		<aside class="sidebar right-sidebar col-sm-3">
			<?php dynamic_sidebar($right_sidebar); ?>
		</aside>
	
	<?php } ?>

</div>

<!--// WordPress Hook //-->
<?php get_footer('alt'); ?>