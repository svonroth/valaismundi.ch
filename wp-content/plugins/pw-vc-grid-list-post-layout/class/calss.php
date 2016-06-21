<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once('actions.php');

if (!class_exists('VCExtendAddonClass_GRID')) {
	class VCExtendAddonClass_GRID  {
		
		static $vars=array();
		public $classs='';
		
		function __construct() {
			
			//
			add_action( 'after_setup_theme', array( $this, 'createShortcodes' ), 5 );
			
			// We safely integrate with VC with this hook
			add_action( 'init', array( $this, 'integrateWithVC' ) );

			// Use this when creating a shortcode addon
			add_shortcode( 'pw_vc_grid', array( $this, 'pw_vc_grid' ) );
			add_shortcode( 'pw_vc_list', array( $this, 'pw_vc_list' ) );
			
			//add_action('wp_ajax_pw_pl_grid_list_swap', array( 'VC_PW_grid','pw_pl_grid_list_swap' ));
			//add_action('wp_ajax_nopriv_pw_pl_grid_list_swap', array( 'VC_PW_grid','pw_pl_grid_list_swap'));
			
		}
		
		public function pw_get_loop($loop)
		{
			@$this->getLoop($loop);
			$this->my_querys = $this->query;
			return $this->my_querys;
		}
		
		public function integrateWithVC() {
			// Check if Visual Composer is installed
			if ( ! defined( 'WPB_VC_VERSION' ) ) {
				// Display notice that Visual Compser is required
				add_action('admin_notices', array( $this, 'showVcVersionNotice' ));
				return;
			}
			
			if(function_exists('vc_add_shortcode_param'))
			{
				vc_add_shortcode_param('pw_number' , array($this, 'pw_number_settings_field' ) );	
				vc_add_shortcode_param('posttypes_dropdown',array($this,'posttypes_dropdown_settings_field'));
				vc_add_shortcode_param('image_radio',array($this,'image_radio_settings_field'));
				vc_add_shortcode_param('spinbutton',array($this,'spinbutton_settings_field'));
				vc_add_shortcode_param('taxonomy',array($this,'taxonomy_settings_field'));
				vc_add_shortcode_param('pw_googlefonts', array($this,'pw_googlefonts_settings_field'));
			}else if(function_exists('add_shortcode_param'))
			{
				add_shortcode_param('pw_number' , array($this, 'pw_number_settings_field' ) );			
				add_shortcode_param('posttypes_dropdown',array($this,'posttypes_dropdown_settings_field'));
				add_shortcode_param('image_radio',array($this,'image_radio_settings_field'));
				add_shortcode_param('spinbutton',array($this,'spinbutton_settings_field'));
				add_shortcode_param('taxonomy',array($this,'taxonomy_settings_field'));
				add_shortcode_param('pw_googlefonts', array($this,'pw_googlefonts_settings_field'));
				
			}
			
		}
		
		/*
		Add your Visual Composer logic here.
		Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.
	
		More info: http://kb.wpbakery.com/index.php?title=Vc_map
		*/

		// Function generate param type "number"
		function pw_number_settings_field($settings, $value)
		{
			$dependency = vc_generate_dependencies_attributes($settings);
			$param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
			$min = isset($settings['min']) ? $settings['min'] : '';
			$max = isset($settings['max']) ? $settings['max'] : '';
			$suffix = isset($settings['suffix']) ? $settings['suffix'] : '';		   
			$output = '<input type="number" min="'.$min.'" max="'.$max.'" class="wpb_vc_param_value ' . $param_name . '  " name="' . $param_name . '" value="'.($value=='' ? $min:$value).'" style="max-width:100px; margin-right: 10px;" />'.$suffix;
			return $output;
		}		
		public function posttypes_dropdown_settings_field($settings, $value) {
			$param_line='';
			$dependency = vc_generate_dependencies_attributes($settings);
			$args = array(
				'public'   => true
			);
			$param_line.='<select id="'.$settings['param_name'].'" name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-input wpb-select '.$settings['param_name'].' '.$settings['type'].'_field">';
			$post_types = get_post_types($args);
			foreach ( $post_types as $post_type ) {
				$checked = "";
				if ( $post_type != 'attachment' ) {
					if ( in_array($post_type, explode(",", $param_value)) ) $checked = ' selected="selected"';
					$param_line .= ' <option value="' . $post_type . '"  '.$checked.'> ' . $post_type;
				}
			}
			$param_line.='</select>';
			
			
			$post_types = get_post_types(array('public' => false, 'name' => 'attachment'), 'names', 'NOT');
			foreach($post_types as $type) {
				$taxonomies = get_object_taxonomies($type , '');
				foreach ( $taxonomies as $tax ) {
					$checked = "";
					if ( in_array($tax->name, explode(",", $param_value)) ) $checked = ' checked="checked"';
					$param_line .= ' <label data-post-type="' . $type . '"><input id="'. $settings['param_name'] . '-' . $tax->name .'" value="' . $tax->name . '" data-post-type="' . $type . '" class="wpb_vc_param_value '.$settings['param_name'].' '.$settings['type'].'" type="checkbox" name="'.$settings['param_name'].'"'.$checked.'> ' . $tax->label. '</label>';
				}
			}
			
			return $param_line;
		}
		
		public function image_radio_settings_field($settings, $value) {
			$param_line='';
			$dependency = vc_generate_dependencies_attributes($settings);

			$param_line = '<input name="'.$settings['param_name']
					 .'" class="wpb_vc_param_value wpb-textinput '
					 .$settings['param_name'].' '.$settings['type'].' '.$settings['class'].'" id="pw_box_type" type="hidden" value="'
					 .$value.'" ' . $dependency . '/>';
					 
			$current_value =  $value;
			$values = $settings['value'];
			foreach ( $values as $label => $v ) {
				$checked = ($v==$current_value) ? ' checked="checked"' : '';
				$param_line .= ' <input style="width: auto !important;" value="' . $v . '" class="pw_box_type_radio" name="mm" type="radio" '.$checked.'> ' . __($label, "js_composer");
			}
           $param_line.= '<script type="text/javascript">
							jQuery(document).ready(function(){								
								jQuery(".pw_box_type_radio").click(function(){
									jQuery("#pw_box_type").val(jQuery(this).val());									
								});
							});
						</script>';
			
			return $param_line;
		}

		public function spinbutton_settings_field($settings, $value) {
			$dependency = vc_generate_dependencies_attributes($settings);
			
		//	echo '<div class="pw_iconpicker" id="benefit_image_icon">';
		//	include(plugin_dir_path( __FILE__ ) .'../class/font-awesome.php');
		//	echo '</div>';
			
			echo  '<div class="pw_iconpicker my_param_block">'
					 .'<input name="'.$settings['param_name']
					 .'" class="wpb_vc_param_value wpb-textinput '
					 .$settings['param_name'].' '.$settings['type'].'_field" type="text" id="font_icon" value="'
					 .$value.'" ' . $dependency . '/>';
					 require_once(plugin_dir_path( __FILE__ ) ."/assets/ui/font-awesome.php");
			echo '</div>';
			?>
			<script type='text/javascript'>
			/* <![CDATA[ */                    
				jQuery(document).ready(function(){
					jQuery('.pw_iconpicker i').click(function(){
						var val=(jQuery(this).attr('class').split(' ')[0]!='fa-none' ? jQuery(this).attr('class').split(' ')[0]:"");
						jQuery(this).closest('input#font_icon').val("DSD");
						//jQuery('#font_icon').val(val);
						jQuery(this).siblings( '.active' ).removeClass( 'active' );
						jQuery(this).addClass('active');
					});
				});
			/* ]]> */
			</script>
			<?php
		}		
		
		public function taxonomy_settings_field($settings, $value) {
			
			$param_line='';
			$dependency = vc_generate_dependencies_attributes($settings);
			
			$param_line = '<input name="'.$settings['param_name']
					 .'" class="wpb_vc_param_value wpb-textinput '
					 .$settings['param_name'].' '.$settings['type'].' '.$settings['class'].'" id="pw_taxonomy" type="hidden" value="'.$value.'" ' . $dependency . '/>
					 <input type="hidden" id="pw_taxonomy_query" />';
					 
			$current_value =  $value;

			$param_line .='<div id="taxonomy_fetch"></div><div id="custom_fields_fetch"></div>';
			
           $param_line.= '<script type="text/javascript">
							jQuery(document).ready(function(){				
											
								jQuery(".pw_taxonomy_radio").click(function(){
									jQuery("#pw_taxonomy").val(jQuery(this).val());									
								});
								
								jQuery(".pw_query").change(function() {
									var old_value=jQuery("#pw_taxonomy_query").val();
									jQuery("#pw_taxonomy_query").val(jQuery(".pw_query").val());
									if(old_value!=jQuery("#pw_taxonomy_query").val())
									{
										var taxonomy="";
										value=jQuery("#pw_taxonomy_query").val();
										var tax_arr=Array();
										query_arr=value.split("|");
										query_arr.forEach(function(entry) {
											post_arr=entry.split(":");
											if(post_arr[0]=="post_type")
											{
												if(post_arr[1].indexOf(",")==-1)
												{
													//confirm(post_arr[1]);
													taxonomy=post_arr[1];
													jQuery.post(
														ajaxurl,
														{
															action : "pw_pl_fetch_taxonomy",
															post_name : taxonomy,
															current_value : "'.$current_value.'"
														},
														function(response){
															jQuery("#taxonomy_fetch").html(response); 
														}
													);
												}
												else
												{
													confirm("'.__("For Enable Filter in Grid, Select Just One Post!", __PW_POST_LAYOUT_TEXTDOMAN__ ).'");	
													jQuery("#taxonomy_fetch").html("'.__("You muse checke just one post for add filter to Grid!", __PW_POST_LAYOUT_TEXTDOMAN__ ).'"); 
													jQuery("#pw_taxonomy").val("");
												}
											}
										});
										
									}
								});
								
								jQuery(".wpb_edit_form_elements").bind("DOMSubtreeModified",function(){
									jQuery(".pw_query").trigger("change");
								});
								
							});
						</script>';
			
			return $param_line;

		}
		
		//GOOGLE FONTS
		function pw_get_googlefonts($selected=''){
			require PW_PS_PL_URL_GIRD_ROOT.'/inc/google-fonts.php';
			$font_options='';
			foreach($fonts_array as $key=>$value){
				$font_options.='<option '.selected($selected,$key,0).' value="'.$key.'">'.$value.'</option>';
			}
			return $font_options;
		}
		
		function pw_googlefonts_settings_field($settings, $value){
			$dependency = vc_generate_dependencies_attributes($settings);
			
			$param_line='';
			
			$param_line.='
	
			<select name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-select '
				.$settings['param_name'].' '.$settings['type'].'_field" id="'.$settings['param_name'].'-family">
				<option value="inherit">Inherit</option>
				'.$this->pw_get_googlefonts($value).'
			</select>
			
			<script type="text/javascript">
				function isNumber(n) {
				  return !isNaN(parseFloat(n)) && isFinite(n);
				}
			
				jQuery(document).ready(function(){
					
					if(jQuery("#'.$settings['param_name'].'-family").val()!="inherit")
					{
						jQuery("head").append("<link rel=\"stylesheet\" href=\"http://fonts.googleapis.com/css?family="+jQuery("#'.$settings['param_name'].'-family").val()+"\" />");	
						
						var $font_family=jQuery("#'.$settings['param_name'].'-family").val();
						var $font_arr=$font_family.split(":");
						if($font_arr.length>0 && isNumber($font_arr[1]))
						{
							$font_weight=$font_arr[1];
							$font_name=$font_arr[0].replace("+"," ");
							jQuery(".pw-check-font-'.$settings['param_name'].'-family").css({"font-family":$font_name,"font-weight":$font_weight});
						}else
						{
							jQuery(".pw-check-font-'.$settings['param_name'].'-family").css("font-family",jQuery("#'.$settings['param_name'].'-family").find(":selected").text());
						}
						
						jQuery(".pw-check-font-'.$settings['param_name'].'-family").css("font-family",jQuery("#'.$settings['param_name'].'-family").find(":selected").text());
					}
					
					jQuery("#'.$settings['param_name'].'-family").change(function(){
						jQuery("head").append("<link rel=\"stylesheet\" href=\"http://fonts.googleapis.com/css?family="+jQuery(this).val()+"\" />");	
						
						var $font_family=jQuery(this).val();
						var $font_arr=$font_family.split(":");
						if($font_arr.length>0 && isNumber($font_arr[1]))
						{
							$font_weight=$font_arr[1];
							$font_name=$font_arr[0].replace("+"," ");
							jQuery(".pw-check-font-'.$settings['param_name'].'-family").css({"font-family":$font_name,"font-weight":$font_weight});
						}else
						{
							jQuery(".pw-check-font-'.$settings['param_name'].'-family").css("font-family",jQuery(this).find(":selected").text());
						}
					});
					
				});
			
			</script>
			<p class="pw-check-font-'.$settings['param_name'].'-family">Grumpy wizards make toxic brew for the evil Queen and Jack.</p>
			
			';
			return $param_line;
		}
		
		public function vc_theme_after_vc_single_image($atts, $content = null) {
		   return '<div><p>Append this div before shortcode</p></div>';
		}


		public function pw_vc_grid( $atts, $content = null ) {
			
			wp_enqueue_style('pw-pl-fontawesome-style');
			wp_enqueue_style('pw-pl-framework-style');
			wp_enqueue_style('pw-pl-layouts-style');
			wp_enqueue_style('pw-pl-grid-responsive-style');
			wp_enqueue_style('pw-pl-lightbox-style');
			wp_enqueue_style('pw-pl-mixitup-style');

			//SCRIPTS	
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'pw-pl-masonry-script');
			wp_enqueue_script( 'pw-pl-lightbox-script');
			wp_enqueue_script( 'pw-pl-mixitup-script');
			wp_enqueue_script( 'pw-pl-custom-script');
			
			extract( shortcode_atts( array(
				'pw_title'							  => '',
				'pw_query'							  => 'size:5|order_by:date|order:ASC|post_type:posts',				
				'pw_link_target'						=> '_self',
				'pw_post_layout'						=> '',
				
				'pw_border_top_size'                    => '',
				'pw_border_top_type'                    => '',
				'pw_border_top_color'                   => '',
			
				'pw_border_right_size'                  => '',
				'pw_border_right_type'                  => '',
				'pw_border_right_color'                 => '',
				
				'pw_border_bottom_size'                 => '',
				'pw_border_bottom_type'                 => '',
				'pw_border_bottom_color'                => '',
				
				'pw_border_left_size'                   => '',
				'pw_border_left_type'                   => '',
				'pw_border_left_color'                  => '',
				
				'pw_item_border_size'                   => '',
				'pw_item_border_type'                   => '',
				'pw_item_border_color'                  => '',
				
				'pw_back_color'                      	=> '#ffffff',
				'pw_back_hcolor'                      	=> '#ffffff',
				'pw_item_back_color'                    => '',
				
				
				'pw_title_font_family'					=>'',
				'pw_title_font_size'					=>'14',			
				'pw_link_color'                         => '',
				'pw_link_hover_color'                   => '',
				'pw_meta_font_family'					=>'',
				'pw_meta_font_size'						=>'12',
				'pw_meta_color'                         => '',
				'pw_excerpt_font_family'				=>'',
				'pw_excerpt_font_size'					=>'11',
				'pw_excerpt_color'                      => '',
				'pw_readmore_type'                      => 'pl-permalink',
				'pw_readmore_translate'                 => __('Read More',__PW_POST_LAYOUT_TEXTDOMAN__ ),
				
				'pw_box_carousel_item'                  => '',
				'pw_box_speed'                          => '',
				'pw_box_carousel_pre_view'              => '3',
				'pw_box_slider_hide_prev_next_buttons'  => 'no',
				'pw_box_slider_loop'                    => 'yes',
				
				'pw_grid_display_type'					   => 'grid',
				'pw_grid_type'					      => 'pl-standard-grid',
				'pw_grid_desktop_columns_count'		 => 'pl-col-md-12',
				'pw_grid_tablet_columns_count'		  => 'pl-col-sm-12',
				'pw_grid_mobile_columns_count'		  => 'pl-col-xs-12',
				'pw_skin_type'			              => 'pl-gridskin-one',
				'pw_grid_skin_effect'				   => 'pl-gst-effect-1',
				'pw_grid_page_navigation'			   => 'no_thing',
				'pw_grid_page_number_style'			 => 'pl-paging-style1',
				'pw_grid_filter_by'					 => '',
				'pw_grid_order_by'					  => '',
				
				
				'pw_grid_author'         		       => 'no', //default:hide
				'pw_grid_tags'         			       => 'no',	 //default:show 	
				'pw_grid_show_num_comment'             => 'no',  //default:show
				
				'pw_grid_hide_recentpost'             => 'no',  //default:show
				'pw_grid_num_hide_recentpost'         => '1',  //default:show
				
				'pw_list_type'					      => 'pl-list-t1',
				'pw_grid_switch_icon'				   => 'no',
				'pw_marquee_type'					   => '',
				'pw_marquee_position'				   => '',
				'pw_marquee_height'				     => '',
				'pw_marquee_border_size'	     		=> '',
				'pw_marquee_border_radius'	     	  => '',
				'pw_marquee_border_type'	     		=> '',
				'pw_marquee_border_color'	     	   => '',
				'pw_marquee_direction'	     		  => '',
				'pw_marquee_title_fontsize'	     	 => '',
				'pw_marquee_title_width'	     	    => '',
				'pw_marquee_title_background'           => '', 
				'pw_marquee_content_background'         => '',
				'pw_marquee_content_fontsize'	       => '',
				'pw_marquee_content_showdate'	       => '',
				'pw_hide_date'         		       		=> 'off', //default:show
				'pw_date_format'	      				=> '',
				'pw_marquee_text_colour'			    => '',
				'pw_marquee_text_colour_hover'		  => '',
				'pw_text_align'				         => '',
				'pw_teasr_layout_img'				   => 'image|link_post,title|link_post,text|excerpt',
				'pw_image_thumb_size'				   => 'medium',
				'pw_excerpt_length'				     => '20',
				'pw_carousel_pre_view'				  => '',
				'pw_image_effect'					   => 'none',
				'pw_show_zoom_icon'		      	        => 'no',
				'pw_show_link_icon'		      	        => 'no',
				'pw_show_overlay'		      	        => 'no',
				'pw_icon_type'		      	        	=> 'none',
				'pw_icon_effect'		      	        => 'none',
				'pw_box_type'						   => '',
				
				'pw_image_type'					     => '',
				'pw_image_carousel_item'			    => '',
				'pw_image_carousel_pre_view'		    => '',
				'pw_image_speed'					    => '',
				'pw_image_slider_hide_prev_next_buttons'=> 'no',
				'pw_image_slider_loop'				  => 'yes',
				'pw_image_columns_count'				=> '3',
				
				'pw_speed'							  => '',
				'pw_slider_caption_type'				=> '',
				'pw_slider_type'						=> '',
				'pw_slider_elastic_animation'		   => '',
				'pw_slider_hide_pagination_control'	 => 'no',
				'pw_slider_hide_prev_next_buttons'	  => 'no',
				'pw_slider_loop'					    => 'yes',
				'pw_hide_excerpt'					   => 'no',
				'pw_hide_readmore'					  => 'no',
				'pw_marquee_icon'					   => '',
				'pw_slider_overlay_background'          => '',
				'pw_slider_link_fontsize'       		   => '',
				'pw_slider_excerpt_fontsize'            => '',
				'pw_box_item_hide_excerpt'              => 'no',
				'pw_taxonomy'         			       => '',
				
				

			), $atts ) );
			$content = wpb_js_remove_wpautop($content, true); // fix unclosed/unwanted paragraph tags in $content
			
			self::$vars=$atts;
			
			//SET DEFAULT VALUE TO CHECKBOX
			$pw_box_slider_hide_prev_next_buttons 	= ($pw_box_slider_hide_prev_next_buttons!='' ? $pw_box_slider_hide_prev_next_buttons :'no');
			$pw_box_slider_loop 	= ($pw_box_slider_loop!='' ? $pw_box_slider_loop :'yes');
			
			$pw_grid_author 	= ($pw_grid_author!='' ? $pw_grid_author :'no');
			$pw_grid_tags 	= ($pw_grid_tags!='' ? $pw_grid_tags :'no');
			$pw_grid_show_num_comment 	= ($pw_grid_show_num_comment!='' ? $pw_grid_show_num_comment :'no');
			$pw_grid_hide_recentpost 	= ($pw_grid_hide_recentpost!='' ? $pw_grid_hide_recentpost :'no');
			$pw_grid_switch_icon 	= ($pw_grid_switch_icon!='' ? $pw_grid_switch_icon :'no');
			$pw_hide_date 	= ($pw_hide_date!='' ? $pw_hide_date :'off');
			$pw_show_zoom_icon 	= ($pw_show_zoom_icon!='' ? $pw_show_zoom_icon :'no');
			$pw_show_link_icon 	= ($pw_show_link_icon!='' ? $pw_show_link_icon :'no');
			$pw_show_overlay 	= ($pw_show_overlay!='' ? $pw_show_overlay :'no');
			$pw_image_slider_hide_prev_next_buttons 	= ($pw_image_slider_hide_prev_next_buttons!='' ? $pw_image_slider_hide_prev_next_buttons :'no');
			$pw_image_slider_loop 	= ($pw_image_slider_loop!='' ? $pw_image_slider_loop :'yes');
			$pw_slider_hide_pagination_control 	= ($pw_slider_hide_pagination_control!='' ? $pw_slider_hide_pagination_control :'no');
			$pw_slider_hide_prev_next_buttons 	= ($pw_slider_hide_prev_next_buttons!='' ? $pw_slider_hide_prev_next_buttons :'no');
			
			$pw_slider_loop 	= ($pw_slider_loop!='' ? $pw_slider_loop :'yes');
			$pw_hide_excerpt 	= ($pw_hide_excerpt!='' ? $pw_hide_excerpt :'no');
			$pw_hide_readmore 	= ($pw_hide_readmore!='' ? $pw_hide_readmore :'no');
			$pw_box_item_hide_excerpt 	= ($pw_box_item_hide_excerpt!='' ? $pw_box_item_hide_excerpt :'no');
			
			$pw_readmore_translate 	= ($pw_readmore_translate!='' ? $pw_readmore_translate :__("Read More", __PW_POST_LAYOUT_TEXTDOMAN__ ));
			
			//echo $pw_readmore_translate;
			
			//global 
			$GLOBALS['output']="";

			$GLOBALS['classs']=new VC_PW_grid(
				$pw_title,
				$pw_query,
				$pw_grid_display_type,
				$pw_grid_type,
				
				
				$pw_border_top_size,
				$pw_border_top_type,
				$pw_border_top_color,
				
				$pw_border_right_size,
				$pw_border_right_type,
				$pw_border_right_color,
				
				$pw_border_bottom_size,
				$pw_border_bottom_type,
				$pw_border_bottom_color,
				
				$pw_border_left_size,
				$pw_border_left_type,
				$pw_border_left_color,

				$pw_back_color,
				$pw_back_hcolor,
				$pw_item_back_color,
				
				
				$pw_title_font_family,
				$pw_title_font_size,
				$pw_link_color,
				$pw_link_hover_color,
				$pw_meta_font_family,
				$pw_meta_font_size,
				$pw_meta_color,
				$pw_excerpt_font_family,
				$pw_excerpt_font_size,
				$pw_excerpt_color,
				$pw_readmore_type,
				$pw_readmore_translate,
				
				$pw_link_target,
				$pw_post_layout,
				$pw_grid_desktop_columns_count,
				$pw_grid_tablet_columns_count,
				$pw_grid_mobile_columns_count,
				$pw_skin_type,
				$pw_grid_skin_effect,
				$pw_grid_page_navigation,
				$pw_grid_page_number_style,
				$pw_grid_filter_by,
				$pw_grid_order_by,
				
				$pw_grid_author,
				$pw_grid_tags,
				$pw_grid_show_num_comment,
				
				$pw_grid_hide_recentpost,  //default:show
				$pw_grid_num_hide_recentpost,
				
				$pw_grid_switch_icon,
				$pw_teasr_layout_img,
				$pw_image_thumb_size,
				$pw_excerpt_length,
				$pw_image_effect,
				
				$pw_show_zoom_icon,
				$pw_show_link_icon,
				$pw_show_overlay,
				$pw_icon_type,
				$pw_icon_effect,
				$pw_hide_date,
				$pw_date_format,
				$pw_taxonomy
				
			);
			
			
			
			//do_action('wp_ajax_pw_pl_grid_list_swap', array($this,'pw_pl_grid_list_swap' ));
			
			return $GLOBALS['output'];
		}
		
		public function pw_vc_list( $atts, $content = null ) {
			
			wp_enqueue_style('pw-pl-fontawesome-style');
			wp_enqueue_style('pw-pl-framework-style');
			wp_enqueue_style('pw-pl-layouts-style');
			wp_enqueue_style('pw-pl-grid-responsive-style');
			wp_enqueue_style('pw-pl-lightbox-style');
			wp_enqueue_style('pw-pl-mixitup-style');

			//SCRIPTS	
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'pw-pl-masonry-script');
			wp_enqueue_script( 'pw-pl-lightbox-script');
			wp_enqueue_script( 'pw-pl-mixitup-script');
			wp_enqueue_script( 'pw-pl-custom-script');
			
			extract( shortcode_atts( array(
				'pw_title'							  => '',
				'pw_query'							  => 'size:5|order_by:date|order:ASC|post_type:posts',				
				'pw_link_target'						=> '_self',
				'pw_post_layout'						=> '',
				
				'pw_border_top_size'                    => '',
				'pw_border_top_type'                    => 'solid',
				'pw_border_top_color'                   => '',
			
				'pw_border_right_size'                  => '',
				'pw_border_right_type'                  => 'solid',
				'pw_border_right_color'                 => '',
				
				'pw_border_bottom_size'                 => '',
				'pw_border_bottom_type'                 => 'solid',
				'pw_border_bottom_color'                => '',
				
				'pw_border_left_size'                   => '',
				'pw_border_left_type'                   => 'solid',
				'pw_border_left_color'                  => '',
				
				'pw_item_border_size'                   => '',
				'pw_item_border_type'                   => 'solid',
				'pw_item_border_color'                  => '',
				
				'pw_back_color'                      	 => '#ffffff',
				'pw_back_hcolor'                      	 => '#ffffff',
				'pw_item_back_color'                    => '',
				
				
				'pw_title_font_family'					=>'',
				'pw_title_font_size'					=>'14',			
				'pw_link_color'                         => '',
				'pw_link_hover_color'                   => '',
				'pw_meta_font_family'					=>'',
				'pw_meta_font_size'						=>'12',
				'pw_meta_color'                         => '',
				'pw_excerpt_font_family'				=>'',
				'pw_excerpt_font_size'					=>'11',
				'pw_excerpt_color'                      => '',
				'pw_readmore_type'                      => 'pl-permalink',
				'pw_readmore_translate'                 => __('Read More',__PW_POST_LAYOUT_TEXTDOMAN__ ),
				
				'pw_box_carousel_item'                  => '',
				'pw_box_speed'                          => '',
				'pw_box_carousel_pre_view'              => '3',
				'pw_box_slider_hide_prev_next_buttons'  => 'no',
				'pw_box_slider_loop'                    => 'yes',
				
				'pw_grid_type'					      => 'pl-standard-grid',
				'pw_grid_desktop_columns_count'		 => 'pl-col-md-12',
				'pw_grid_tablet_columns_count'		  => 'pl-col-sm-12',
				'pw_grid_mobile_columns_count'		  => 'pl-col-xs-12',
				'pw_skin_type'			              => 'pl-gridskin-one',
				'pw_grid_skin_effect'				   => 'pl-gst-effect-1',
				'pw_grid_page_navigation'			   => 'no_thing',
				'pw_grid_page_number_style'			 => 'pl-paging-style1',
				'pw_grid_filter_by'					 => '',
				'pw_grid_order_by'					  => '',
				
				'pw_grid_tags'         			       => 'no',	 //default:show 	
				'pw_grid_show_num_comment'             => 'no',  //default:show
				
				
				'pw_list_type'					      => 'pl-list-t1',
				'pw_marquee_type'					   => '',
				'pw_marquee_position'				   => '',
				'pw_marquee_height'				     => '',
				'pw_marquee_border_size'	     		=> '',
				'pw_marquee_border_radius'	     	  => '',
				'pw_marquee_border_type'	     		=> '',
				'pw_marquee_border_color'	     	   => '',
				'pw_marquee_direction'	     		  => '',
				'pw_marquee_title_fontsize'	     	 => '',
				'pw_marquee_title_width'	     	    => '',
				'pw_marquee_title_background'           => '', 
				'pw_marquee_content_background'         => '',
				'pw_marquee_content_fontsize'	       => '',
				'pw_marquee_content_showdate'	       => '',
				'pw_hide_date'         		       		=> 'off', //default:hide
				'pw_date_format'	      				=> '',
				'pw_marquee_text_colour'			    => '',
				'pw_marquee_text_colour_hover'		  => '',
				'pw_text_align'				         => '',
				'pw_teasr_layout_img'				   => 'image|link_post,title|link_post,text|excerpt',
				'pw_image_thumb_size'				   => 'medium',
				'pw_excerpt_length'				     => '20',
				'pw_grid_text_type'				     => 'full_text',
				'pw_carousel_pre_view'				  => '',
				'pw_image_effect'					   => 'none',
				'pw_show_zoom_icon'		      	        => 'no',
				'pw_show_link_icon'		      	        => 'no',
				'pw_show_overlay'		      	        => 'no',
				'pw_icon_type'		      	       		=> 'none',
				'pw_icon_effect'		      	        => 'none',
				'pw_box_type'						   => '',
				
				'pw_image_type'					     => '',
				'pw_image_carousel_item'			    => '',
				'pw_image_carousel_pre_view'		    => '',
				'pw_image_speed'					    => '',
				'pw_image_slider_hide_prev_next_buttons'=> 'no',
				'pw_image_slider_loop'				  => 'yes',
				'pw_image_columns_count'				=> '3',
				
				'pw_speed'							  => '',
				'pw_slider_caption_type'				=> '',
				'pw_slider_type'						=> '',
				'pw_slider_elastic_animation'		   => '',
				'pw_slider_hide_pagination_control'	 => 'no',
				'pw_slider_hide_prev_next_buttons'	  => 'no',
				'pw_slider_loop'					    => 'yes',
				'pw_hide_excerpt'					   => 'no',
				'pw_hide_readmore'					  => 'no',
				'pw_marquee_icon'					   => '',
				'pw_slider_overlay_background'          => '',
				'pw_slider_link_fontsize'       		   => '',
				'pw_slider_excerpt_fontsize'            => '',
				'pw_box_item_hide_excerpt'              => 'no',


			), $atts ) );
			$content = wpb_js_remove_wpautop($content, true); // fix unclosed/unwanted paragraph tags in $content
			
			//SET DEFAULT VALUE TO CHECKBOX
			$pw_box_slider_hide_prev_next_buttons 	= ($pw_box_slider_hide_prev_next_buttons!='' ? $pw_box_slider_hide_prev_next_buttons :'no');
			$pw_box_slider_loop 	= ($pw_box_slider_loop!='' ? $pw_box_slider_loop :'yes');
			
			
			$pw_grid_tags 	= ($pw_grid_tags!='' ? $pw_grid_tags :'no');
			$pw_grid_show_num_comment 	= ($pw_grid_show_num_comment!='' ? $pw_grid_show_num_comment :'no');
			
			$pw_hide_date 	= ($pw_hide_date!='' ? $pw_hide_date :'off');
			$pw_show_zoom_icon 	= ($pw_show_zoom_icon!='' ? $pw_show_zoom_icon :'no');
			$pw_show_link_icon 	= ($pw_show_link_icon!='' ? $pw_show_link_icon :'no');
			$pw_show_overlay 	= ($pw_show_overlay!='' ? $pw_show_overlay :'no');
			$pw_image_slider_hide_prev_next_buttons 	= ($pw_image_slider_hide_prev_next_buttons!='' ? $pw_image_slider_hide_prev_next_buttons :'no');
			$pw_image_slider_loop 	= ($pw_image_slider_loop!='' ? $pw_image_slider_loop :'yes');
			$pw_slider_hide_pagination_control 	= ($pw_slider_hide_pagination_control!='' ? $pw_slider_hide_pagination_control :'no');
			$pw_slider_hide_prev_next_buttons 	= ($pw_slider_hide_prev_next_buttons!='' ? $pw_slider_hide_prev_next_buttons :'no');
			
			$pw_slider_loop 	= ($pw_slider_loop!='' ? $pw_slider_loop :'yes');
			$pw_hide_excerpt 	= ($pw_hide_excerpt!='' ? $pw_hide_excerpt :'no');
			$pw_hide_readmore 	= ($pw_hide_readmore!='' ? $pw_hide_readmore :'no');
			$pw_box_item_hide_excerpt 	= ($pw_box_item_hide_excerpt!='' ? $pw_box_item_hide_excerpt :'no');
			
			$pw_readmore_translate 	= ($pw_readmore_translate!='' ? $pw_readmore_translate :__("Read More", __PW_POST_LAYOUT_TEXTDOMAN__ ));
			
			//global 
			$GLOBALS['output']="";
			$slider_class = new VC_PW_list(
				$pw_title,
				$pw_query,
				$pw_list_type,
				
				$pw_grid_tags,
				$pw_grid_show_num_comment,
				
				$pw_border_top_size,
				$pw_border_top_type,
				$pw_border_top_color,
				
				$pw_border_right_size,
				$pw_border_right_type,
				$pw_border_right_color,
				
				$pw_border_bottom_size,
				$pw_border_bottom_type,
				$pw_border_bottom_color,
				
				$pw_border_left_size,
				$pw_border_left_type,
				$pw_border_left_color,
				
				
				$pw_back_color,
				$pw_back_hcolor,
				$pw_item_back_color,
				
				
				$pw_title_font_family,
				$pw_title_font_size,
				$pw_link_color,
				$pw_link_hover_color,
				$pw_meta_font_family,
				$pw_meta_font_size,
				$pw_meta_color,
				$pw_excerpt_font_family,
				$pw_excerpt_font_size,
				$pw_excerpt_color,
				$pw_readmore_type,
				$pw_readmore_translate,
				
				$pw_link_target,
				$pw_post_layout,

				$pw_grid_page_navigation,
				$pw_grid_page_number_style,
				$pw_teasr_layout_img,
				$pw_image_thumb_size,
				$pw_excerpt_length,
				$pw_grid_text_type,
				$pw_image_effect,
				$pw_show_zoom_icon,
				$pw_show_link_icon,
				$pw_show_overlay,
				$pw_icon_type,
				$pw_icon_effect,
				$pw_hide_date,
				$pw_date_format
			);
			return $GLOBALS['output'];
		}

		public function createShortcodes() {	

			require_once("admin-grid-ui.php");
			require_once("admin-list-ui.php");						
		}
		
		public function excerpt($text,$excerpt_length,$content_type='excerpt') {
			global $post;
			if(trim($excerpt_length)=='') $excerpt_length=10;
			$limit=$excerpt_length;
			if($content_type=='excerpt')	
			{
				$excerpt = explode(' ', $text, $limit);
				if (count($excerpt)>=$limit) {
					array_pop($excerpt);
					$excerpt = implode(" ",$excerpt).'...';
				} else {
					$excerpt = implode(" ",$excerpt);
				}	
				
				//RUN SHORTCODE IN EXCERPT
				//$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
				//return do_shortcode($excerpt);
				
				//Nor Excute SHROTCODE
				$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
				return $excerpt;
				
			}else{
				$content = explode(' ', $text, $limit);
				if (count($content)>=$limit) {
					array_pop($content);
					$content = implode(" ",$content).'...';
				} else {
					$content = implode(" ",$content);
				}	
				//REMOVE SHORTCODE
				//$content = preg_replace('/\[.+\]/','', $content);
				
				$content = apply_filters('the_content', $content); 
				$content = str_replace(']]>', ']]&gt;', $content);
				return $content;
			}
		}
		
		/*public function excerpt($text,$excerpt_length) {
			 global $post;

			  if ( '' == $text ) {
				$text = get_the_content('');
				$text = apply_filters('the_content', $text);
				$text = str_replace('\]\]\>', ']]&gt;', $text);
				$text = strip_tags($text);

				$words = explode(' ', $text, $excerpt_length + 1);

				if (count($words)> $excerpt_length) {
				  array_pop($words);
				  array_push($words, '[...]');
				  $text = implode(' ', $words);
				}
			  }else
			  {
				$text = str_replace('\]\]\>', ']]&gt;', $text);
				$text = strip_tags($text);

				$words = explode(' ', $text, $excerpt_length + 1);

				if (count($words)> $excerpt_length) {
				  array_pop($words);
				  array_push($words, '[...]');
				  $text = implode(' ', $words);
				}
			  }
			return $text;
		}*/
		
		/*public function excerpts($excerpt,$limit) {
			$excerpt = explode(' ', $excerpt, $limit);
			if (count($excerpt)>=$limit) {
				array_pop($excerpt);
				$excerpt = implode(" ",$excerpt).'...';
			} else {
				$excerpt = implode(" ",$excerpt);
			} 
			$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
			return $excerpt;
		}*/

	}
	// Finally initialize code
	$GLOBALS['VCExtendAddonClass_GRID'] = new VCExtendAddonClass_GRID;
}

?>