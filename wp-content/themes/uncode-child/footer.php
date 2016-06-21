<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package uncode
 */

global $metabox_data, $is_redirect, $menutype;

$limit_width = $limit_content_width = $footer_content = $footer_text_content = $footer_icons = $footer_full_width = '';
$alignArray = array('left','right');

$general_style = ot_get_option('_uncode_general_style');

$footer_last_style = ot_get_option( '_uncode_footer_last_style');
$footer_last_bg = ot_get_option('_uncode_footer_bg_color');
$footer_last_bg = ($footer_last_bg == '') ? ' style-'.$footer_last_style.'-bg' : ' style-'.$footer_last_bg.'-bg';

$post_type = isset( $post->post_type ) ? $post->post_type : 'post';
if (is_archive()) $post_type .= '_index';
if (is_404()) $post_type = '404';

/** Get page width info **/
$boxed = ot_get_option('_uncode_boxed');
if ($boxed !== 'on') {
	if (isset($metabox_data['_uncode_specific_footer_width'][0]) && $metabox_data['_uncode_specific_footer_width'][0] !== '') {
		if ($metabox_data['_uncode_specific_footer_width'][0] === 'full') $footer_full_width = true;
		else $footer_full_width = false;
	} else {
		$footer_generic_width = ot_get_option( '_uncode_'.$post_type.'_footer_width');
		if ($footer_generic_width !== '') {
			if ($footer_generic_width === 'full') $footer_full_width = true;
			else $footer_full_width = false;
		}
		else
		{
			$footer_full = ot_get_option( '_uncode_footer_full');
			$footer_full_width = ($footer_full !== 'on') ? false : true;
		}
	}
	if (!$footer_full_width) $limit_content_width = ' limit-width';
}

if (isset($metabox_data['_uncode_specific_footer_block'][0]) && $metabox_data['_uncode_specific_footer_block'][0] !== '') {
	$footer_block = $metabox_data['_uncode_specific_footer_block'][0];
} else {
	$footer_block = ot_get_option('_uncode_' . $post_type . '_footer_block');
	if ($footer_block === '' && $footer_block !== 'none') {
		$footer_block = ot_get_option('_uncode_footer_block');
	}
}

if (isset($footer_block) && !empty($footer_block) && $footer_block !== 'none' && defined( 'WPB_VC_VERSION' )) {
	$footer_block_content = get_post_field('post_content', $footer_block);
	if ($footer_full_width) {
		$footer_block_content = preg_replace('#\s(unlock_row)="([^"]+)"#', ' unlock_row="yes"', $footer_block_content);
		$footer_block_content = preg_replace('#\s(unlock_row_content)="([^"]+)"#', ' unlock_row_content="yes"', $footer_block_content);
		$footer_block_counter = substr_count($footer_block_content, 'unlock_row_content');
		if ($footer_block_counter === 0) $footer_block_content = str_replace('[vc_row ', '[vc_row unlock_row="yes" unlock_row_content="yes" ', $footer_block_content);
	} else {
		$footer_block_content = preg_replace('#\s(unlock_row)="([^"]+)"#', ' unlock_row="yes"', $footer_block_content);
		$footer_block_content = preg_replace('#\s(unlock_row_content)="([^"]+)"#', ' unlock_row_content="no"', $footer_block_content);
		$footer_block_counter = substr_count($footer_block_content, 'unlock_row_content');
		if ($footer_block_counter === 0) $footer_block_content = str_replace('[vc_row ', '[vc_row unlock_row="yes" unlock_row_content="no" ', $footer_block_content);
	}
	$footer_content .= $footer_block_content;
}

$footer_position = ot_get_option('_uncode_footer_position');
if ($footer_position === '') $footer_position = 'left';

$footer_copyright = ot_get_option('_uncode_footer_copyright');
if ($footer_copyright !== 'off') {
	$footer_text_content = '&copy; '.date("Y").' '.get_bloginfo('name') . ' ' . esc_html__('All rights reserved','uncode');
}

$footer_text = ot_get_option('_uncode_footer_text');
if ($footer_text !== '' && $footer_copyright === 'off') {
	$footer_text_content = do_shortcode(apply_filters('the_content', $footer_text));
}

if ($footer_text_content !== '') {
	$footer_text_content = '<div class="site-info uncell col-lg-6 pos-middle text-'.$footer_position.'">'.$footer_text_content.'</div><!-- site info -->';
}

$footer_social = ot_get_option('_uncode_footer_social');
if ($footer_social !== 'off') {
	$socials = ot_get_option( '_uncode_social_list','',false,true);
	if (isset($socials) && !empty($socials) && count($socials) > 0) {
		foreach ($socials as $social) {
			if ($social['_uncode_social'] === '') continue;
			$footer_icons .= '<div class="social-icon icon-box icon-box-top icon-inline"><a href="'.esc_url($social['_uncode_link']).'" target="_blank"><i class="'.esc_attr($social['_uncode_social']).'"></i></a></div>';
		}
	}
}

if ($footer_icons !== '') $footer_icons = '<div class="uncell col-lg-6 pos-middle text-'.($footer_position === 'center' ? $footer_position : $alignArray[!array_search($footer_position, $alignArray)]).'">' . $footer_icons . '</div>';

if (($footer_text_content !== '' || $footer_icons !== '')) {
	switch ($footer_position) {
		case 'left':
			$footer_text_content = $footer_text_content . $footer_icons;
			break;
		case 'center':
			$footer_last_bg .= ' footer-center';
			$footer_text_content = $footer_icons . $footer_text_content;
			break;
		case 'right':
			$footer_text_content = $footer_icons . $footer_text_content;
			break;
	}
	$footer_last_bg .= ' footer-last';
	if (strpos($menutype ,'vmenu') !== false) $footer_last_bg .= ' desktop-hidden';
	$footer_content .= uncode_get_row_template($footer_text_content, $limit_width, $limit_content_width, $footer_last_style, $footer_last_bg, false, false, false);
}?>
							</div><!-- sections container -->
						</div><!-- page wrapper -->
					<?php if ($is_redirect !== true) : ?>
					<footer id="colophon" class="site-footer">

<?php
//MULTILANGUE POUR LE FOOTER=======================================
$my_current_lang = apply_filters( 'wpml_current_language', NULL );
//echo $my_current_lang;
$jemabonne = "Je m’abonne à la newsletter";
$mailshimp_form = "[mc4wp_form id='3552']";
$copyright_footer = "© 2016 Valais Mundi SA | Text: Didier Ters | Photography: Olivier Maire";

switch ($my_current_lang) {
    case "fr":
        $jemabonne = "Je m’abonne à la newsletter";
        $mailshimp_form = "[mc4wp_form id='3552']";
        $copyright_footer = "© 2016 Valais Mundi SA | Text: Didier Ters | Photography: Olivier Maire";
        break;
    case "en":
        $jemabonne = "I subscribe to the newsletter";
        $mailshimp_form = "[mc4wp_form id='3554']";
        $copyright_footer = "© 2016 Valais Mundi SA | Text: Didier Ters | Photography: Olivier Maire EN";
        break;
    case "de":
        $jemabonne = "Ich abonniere den Newsletter";
        $mailshimp_form = "[mc4wp_form id='3555']";
        $copyright_footer = "© 2016 Valais Mundi SA | Text: Didier Ters | Photography: Olivier Maire DE";
        break;
    default:
}
?>


						<div data-parent="true" class="style-color-717771-bg row-container" data-section="11"><div class="row full-width row-parent" data-imgready="true"><div class="row-inner"><div class="pos-top pos-center align_center column_parent col-lg-4 single-internal-gutter"><div class="uncol style-dark"><div class="uncoltable"><div class="uncell no-block-padding"><div class="uncont"><div class="uncode-single-media  text-center"><div class="single-wrapper" style="max-width: 16%;">
<div class="tmb tmb-light tmb-media-first tmb-media-last tmb-content-overlay tmb-no-bg">
						<div class="t-inside"><div class="t-entry-visual" tabindex="0"><div class="t-entry-visual-tc"><div class="uncode-single-media-wrapper">
										<img src="http://www.valaismundi.ch/wp-content/uploads/2016/01/logo_valais-mundi-1.png" width="253" height="195" alt="" scale="0"></div>
								</div>
							</div></div>
					</div>
</div></div>
<div class="heading-text el-text font-color">
	<h5 class="h5 text-color-xsdn-color">
		Valais Mundi SA
	</h5>
	<p class="footer-text" style="text-align: center;">Ruelle de la Maya 12<br>
	Case postale 41<br>
	1958 Uvrier, Suisse
	</p>
</div>

</div></div></div></div></div>
<div class="pos-top pos-center align_center column_parent col-lg-4 single-internal-gutter"><div class="uncol style-dark"><div class="uncoltable"><div class="uncell no-block-padding"><div class="uncont">
	<div class="wpb_widgetised_column wpb_content_element">
		<div class="wpb_wrapper">
			
			<aside id="text-2" class="widget widget_text widget-container sidebar-widgets"><h3 style="text-transform:uppercase;"><?php echo $jemabonne; ?></h3>			<div class="textwidget"></div>
		</aside>
		</div>
	</div>

<div class="clear"></div>
<div class="uncode_text_column">
	<?php echo do_shortcode($mailshimp_form); ?>
</div>

</div></div></div></div></div><div class="pos-top pos-center align_center column_parent col-lg-4 single-internal-gutter"><div class="uncol style-dark"><div class="uncoltable"><div class="uncell no-block-padding"><div class="uncont"><div class="icon-box icon-box-top icon-inline"><div class="icon-box-icon fa-container" style="margin-bottom: 0px;"><a href="https://www.linkedin.com/company/3341227" target="_blank" class="text-color-uydo-color custom-link"><i class="fa fa-linkedin-square fa-2x fa-fw"></i></a></div></div><div class="icon-box icon-box-top icon-inline"><div class="icon-box-icon fa-container" style="margin-bottom: 0px;"><a href="https://twitter.com/electuswine" target="_blank" class="text-color-uydo-color custom-link"><i class="fa fa-twitter-square fa-2x fa-fw"></i></a></div></div><div class="icon-box icon-box-top icon-inline"><div class="icon-box-icon fa-container" style="margin-bottom: 0px;"><a href="https://www.facebook.com/Valais-Mundi-187611988237737/" target="_blank" class="text-color-uydo-color custom-link"><i class="fa fa-facebook-square fa-2x fa-fw"></i></a></div></div><div class="icon-box icon-box-top icon-inline"><div class="icon-box-icon fa-container" style="margin-bottom: 0px;"><a href="https://www.instagram.com/electuswine/" target="_blank" class="text-color-uydo-color custom-link"><i class="fa fa-instagram fa-2x fa-fw"></i></a></div></div></div></div></div></div></div><script id="script-189174" type="text/javascript">UNCODE.initRow(document.getElementById("script-189174"));</script></div></div></div><div class="row-container style-color-jevc-bg footer-center footer-last">
	  					<div class="row row-parent style-dark no-top-padding no-h-padding no-bottom-padding">
								<div class="site-info uncell col-lg-6 pos-middle text-center"><p><?php echo $copyright_footer; ?></p>
</div><!-- site info -->
							</div>
						</div>					










						<?php //echo __(do_shortcode( shortcode_unautop( $footer_content ) )); ?>
					</footer>
					<?php endif; ?>
				</div><!-- main container -->
			</div><!-- main wrapper -->
		</div><!-- box container -->
	</div><!-- box wrapper -->
	<?php
	$footer_uparrow = ot_get_option('_uncode_footer_uparrow');
	if (wp_is_mobile()) {
		$footer_uparrow_mobile = ot_get_option('_uncode_footer_uparrow_mobile');
		if ($footer_uparrow_mobile === 'off') $footer_uparrow = 'off';
	}
	if ($footer_uparrow !== 'off') {
		$scroll_higher = '';
		if (strpos($menutype ,'vmenu') === false) {
			if ($limit_content_width === '') $scroll_higher = ' footer-scroll-higher';
		}
		echo '<div class="style-light footer-scroll-top'.$scroll_higher.'"><a href="#" class="scroll-top"><i class="fa fa-angle-up fa-stack fa-rounded btn-default btn-hover-nobg"></i></a></div>';
	}
	$vertical = (strpos($menutype, 'vmenu') !== false || $menutype === 'menu-overlay') ? true : false;
	if (!$vertical) {

		$search_animation = ot_get_option('_uncode_menu_search_animation');
		if ($search_animation === '' || $search_animation === '3d') $search_animation = 'contentscale';

	?>
	<div class="overlay overlay-<?php echo $search_animation; ?> style-dark style-dark-bg overlay-search" data-area="search" data-container="box-container">
		<div class="mmb-container"><div class="mobile-menu-button menu-button-offcanvas mobile-menu-button-dark lines-button x2 overlay-close close" data-area="search" data-container="box-container"><span class="lines"></span></div></div>
		<div class="search-container"><?php get_search_form( true ); ?></div>
	</div>
<?php
	}
wp_footer(); ?>
</body>
</html>