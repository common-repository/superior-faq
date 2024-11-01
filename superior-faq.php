<?php
/*
Plugin Name: Superior FAQ
Plugin URI: https://radykal.de/superior-faq/
Description: Create your own FAQs easily and place them anywhere in your post or page.
Version: 1.0.2
Author: Rafael Dery
Author URI: https://codecanyon.net/user/radykal/portfolio?ref=radykal
*/



if(!class_exists('Superior_FAQ')) {

	class Superior_FAQ {

		//constants
		const VERSION = '1.0.2';
		const VERSION_FIELD_NAME = 'superior_faq_version';

		private $default_settings = array (
			'permalink_label' => 'Permalink',
		    'back_to_faqs_label' => 'Back to FAQs',
		    'search_placeholder' => 'Search FAQs...',
		    'general_parent_page' => -1,
		    'template' => 'single.php',
		    'voting_single' => 1
	    );

		//Constructer
		public function __construct() {

			register_activation_hook(  __FILE__, array( &$this, 'activate_plugin' ) );

			remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);

			add_action( 'init', array( &$this, 'init' ) );
			add_action( 'wp_head', array( &$this, 'track_post_views') );
			add_action( 'wp_enqueue_scripts', array( &$this, 'add_scripts_styles' ) );
			add_filter( 'template_include', array( &$this, 'set_template'), 99 );
			add_filter( 'the_content', array( &$this, 'faq_content_filter') , 20 );
			add_action( 'wp_ajax_sf_vote', array( &$this, 'vote_faq' ) );
			add_action( 'wp_ajax_nopriv_sf_vote', array( &$this, 'vote_faq' ) );

			//admin hooks
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_styles_scripts') );
			add_filter( 'wp_insert_post_data', array( &$this, 'save_post_data'), 99, 2  );
			add_action( 'admin_menu', array( &$this,'add_sub_pages' ) );


			//shortcodes
			add_shortcode( 'superior_faq', array( &$this, 'add_faq' ) );
			add_shortcode( 'superior_faq_search', array( &$this, 'add_faq_search' ) );
			add_shortcode( 'superior_faq_popular', array( &$this, 'add_most_popular_faq' ) );

		}

		public function init() {

			//CUSTOM POST TYPES
			$pp_labels = array(
			  'name' => _x('FAQs', 'post type general name', 'radykal'),
			  'singular_name' => _x('FAQ', 'post type singular name', 'radykal'),
			  'add_new' => _x('Add New', 'faq', 'radykal'),
			  'add_new_item' => __('Add New FAQ', 'radykal'),
			  'edit_item' => __('Edit FAQ', 'radykal'),
			  'new_item' => __('New FAQ', 'radykal'),
			  'all_items' => __('All FAQs', 'radykal'),
			  'view_item' => __('View FAQ', 'radykal'),
			  'search_items' => __('Search FAQs', 'radykal'),
			  'not_found' =>  __('No FAQs found', 'radykal'),
			  'not_found_in_trash' => __('No FAQs found in Trash', 'radykal'),
			  'parent_item_colon' => '',
			  'menu_name' => 'Superior FAQ'

			);

			$pp_args = array(
			  'labels' => $pp_labels,
			  'public' => true,
			  'exclude_from_search' => false,
			  'show_ui' => true,
			  'show_in_menu' => true,
			  'has_archive' => true,
			  'hierarchical' => false,
			  'menu_icon' => 'dashicons-shield-alt',
			  'supports' => array('title','editor', 'page-attributes', 'comments', 'custom_fields', 'excerpt'),
			  'register_meta_box_cb' => array(&$this, 'add_meta_boxes'),
			  'rewrite' => array('slug' => 'faq', 'with_front' => false)
			);

			register_post_type( 'faq', $pp_args );

			//TAXONOMIES
			$tax_categories_labels = array(
			  'name' => _x( 'Categories', 'taxonomy general name', 'radykal' ),
			  'singular_name' => _x( 'Category', 'taxonomy singular name', 'radykal' ),
			  'search_items' =>  __( 'Search Categories', 'radykal' ),
			  'all_items' => __( 'All Categories', 'radykal' ),
			  'parent_item' => __( 'Parent Category', 'radykal' ),
			  'parent_item_colon' => __( 'Parent Category:', 'radykal' ),
			  'edit_item' => __( 'Edit Category', 'radykal' ),
			  'update_item' => __( 'Update Category', 'radykal' ),
			  'add_new_item' => __( 'Add New Category', 'radykal' ),
			  'new_item_name' => __( 'New Category Name', 'radykal' ),
			  'menu_name' => __( 'Categories', 'radykal' ),
			);

			register_taxonomy('faq_category', 'faq', array(
			  'hierarchical' => true,
			  'labels' => $tax_categories_labels,
			  'show_ui' => true,
			  'query_var' => true,
			  'rewrite' => array( 'slug' => 'faq_category' ),
			));

		}

		public function activate_plugin( $networkwide ) {

			global $wpdb;

			if ( is_multisite() ) {
	    		if( isset($_GET['networkwide']) && ($_GET['networkwide'] == 1) ) {

	                $current_blog = $wpdb->blogid;
	    			// Get all blog ids
	    			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
	    			foreach ($blogids as $blog_id) {
	    				switch_to_blog($blog_id);
	    				$this->install();
	    			}
	    			switch_to_blog($current_blog);
	    			return;

	    		}
	    	}

			$this->install();

		}

		public function add_meta_boxes() {

			add_meta_box('superior-faq-meta-box', __('Parent Page', 'radykal'), array( &$this, 'create_meta_box'), 'faq', 'side' );

		}

		public function create_meta_box() {

			global $post;

			echo '<p class="description">'.__('Here you can set the page to be used as an parent page for this FAQ.', 'radykal').'</p>';

			$dropdown = wp_dropdown_pages( array(
					'name' => 'sf_parent_page',
					'selected' => $post->post_parent,
					'echo' => false
				)
			);

			$settings = get_option( 'superior_faq_settings' );
			$general_parent_page_id = $settings['general_parent_page'];

			$index = strpos($dropdown, '<option');
			echo substr_replace($dropdown, '<option value="0">'.__('Use general parent page from settings', 'radykal').'</option>', $index, 0);

			?>
			<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery('[name="sf_parent_page"]').chosen({});
				});
			</script>
			<?php

		}

		public function enqueue_admin_styles_scripts( $hook ) {

			global $post;

			if( (($hook == 'post-new.php' || $hook == 'post.php') && 'faq' === $post->post_type) || $hook == 'faq_page_sf-settings' || $hook == 'faq_page_sf-shortcode-builder' ) {

				wp_enqueue_style( 'sf-admin', plugins_url('/admin/css/admin.css', __FILE__), false, self::VERSION );
				wp_enqueue_style( 'sf-chosen', plugins_url('/admin/css/chosen.css', __FILE__), false, '1.1.0' );
				wp_enqueue_script( 'sf-chosen', plugins_url('/admin/js/chosen.jquery.min.js', __FILE__), false, '1.1.0' );

			}

		}

		public function save_post_data( $data, $postarr ) {

		    // verify if this is an auto save routine.
		    // If it is our form has not been submitted, so we dont want to do anything
		    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		        return $data;

		    if( $data['post_type'] == 'faq' ) {

			    if ( isset($_POST["sf_parent_page"]) ){
			        $data['post_parent'] = intval($_POST["sf_parent_page"]);
			    }

			    $count_key = 'sf_views_count';
			    $count = get_post_meta($postarr['ID'], $count_key, true);
				if( $count == '') {
			        delete_post_meta($postarr['ID'], $count_key);
			        update_post_meta($postarr['ID'], $count_key, '0');
			        update_post_meta($postarr['ID'], 'sf_vote_up', 0);
			        update_post_meta($postarr['ID'], 'sf_vote_down', 0);
			    }
		    }

		    return $data;

		}

		public function add_sub_pages() {

			//add settings page
			add_submenu_page( 'edit.php?post_type=faq', __('Settings', 'radykal'), __('Settings', 'radykal'), 'manage_options', 'sf-settings', array($this, 'settings_page') );
			//add shortcode builder page
			add_submenu_page( 'edit.php?post_type=faq', __('Shortcode Builder', 'radykal'), __('Shortcode Builder', 'radykal'), 'manage_options', 'sf-shortcode-builder', array($this, 'shortcode_builder_page') );

		}

		public function settings_page() {

			if( isset($_POST['submit']) ) {

				echo '<div class="updated"><p>'.__('Your settings have been saved.', 'radykal').'</p></div>';

				$settings = array();

				foreach($this->default_settings as $key => $value) {
					$settings[$key] = $_POST[$key] === null ? 0 : $_POST[$key];
				}

				update_option('superior_faq_settings', $settings);

			}
			//load settings from db
			else {
				$settings = get_option( 'superior_faq_settings' );
			}

			require_once(dirname(__FILE__) . '/admin/settings.php');

		}

		public function shortcode_builder_page() {

			require_once(dirname(__FILE__) . '/admin/shortcode-builder.php');

		}

		public function add_scripts_styles() {

			wp_enqueue_style( 'font-awesome-4', '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css', false, '4.1.0' );
			wp_enqueue_style( 'superior-faq', plugins_url('/css/superior-faq.css', __FILE__), false, self::VERSION );

			wp_enqueue_script( 'superior-faq-search', plugins_url('/js/jquery.smart_autocomplete.js', __FILE__), array('jquery') );

		}

		public function set_template( $template ) {

			global $post;

			if($post->post_type == 'faq') {
				//get selected template for faqs posts
				$settings = get_option( 'superior_faq_settings' );
				$template_path = get_template_directory().'/'.$settings['template'];

				//check if template exist in theme
				if( file_exists($template_path) ) {
					return $template_path;
				}
			}

			return $template;

		}

		public function track_post_views($post_id) {

			global $post;

		    if ( is_admin() || $post->post_type != 'faq' ) return;
		    if ( empty ( $post_id) ) {
		        $post_id = $post->ID;
		    }

		    $count_key = 'sf_views_count';
		    $count = get_post_meta($post_id, $count_key, true);
		    if( $count == ''){
		        $count = 0;
		        delete_post_meta($post_id, $count_key);
		        add_post_meta($post_id, $count_key, '0');
		    }
		    else{
		        $count++;
		        update_post_meta($post_id, $count_key, $count);
		    }


		}

		public function faq_content_filter( $content ) {

			global $post;

			if($post->post_type == 'faq') {

				$settings = get_option( 'superior_faq_settings' );

				$parent_page_id = $post->post_parent;

				if( $parent_page_id == 0) {
					$parent_page_id = $settings['general_parent_page'];
				}

				$parent_page_url = get_permalink($parent_page_id);

				$content .= '<p class="superior-faq-action-bar">';

				if( get_post_type($post->ID)  == 'faq' && $parent_page_url && is_single()) {

					$content .= '<a href="'.$parent_page_url.'">'.$settings['back_to_faqs_label'].'</a>';

					if( isset($settings['voting_single']) && $settings['voting_single'] == 1 ) {
						$content .= $this->get_vote_html($post->ID);
						$content .= $this->get_vote_script('post-'.$post->ID);
					}

				}

				$content .= '</p>';

			}

			return $content;

		}

		public function add_faq( $atts ) {

			extract( shortcode_atts( array(
				'categories' => '',
				'layout' => 'simple', //simple, boxed
				'open_icon' => '', //plus, plus-circle,plus-square,plus-square-o,search-plus
				'close_icon' => '', //minus, minus-circle,minus-square,minus-square-o,search-minus
				'headline_tag' => 'h4',
				'permalink' => 'yes',
				'deeplinking' => 'yes',
				'voting' => 'yes',
				'excerpt' => 'no',
				'effect' => 'accordion', //accordion, tooltip, none
				'category_style' => '',
				'title_style' => '',
				'content_style' => ''
			), $atts ) );

			$selector = uniqid('sf-');

			$settings = get_option( 'superior_faq_settings' );

			$categories = explode(',', trim($categories));
			$categories = array_filter($categories);

			if( empty($categories) ) {
				$category_objects = get_terms( 'faq_category' );
			}
			else {
				$category_objects = get_terms( 'faq_category', array(
						'include' => $categories
					)
				);
			}

			//html output
			ob_start();

			echo '<div id="'.$selector.'" class="superior-effect-'.$effect.'">';

			foreach($category_objects as $category_object) {

				echo '<h3 class="superior-faq-category-title" style="'.$category_style.'">'.$category_object->name.'</h3>';
				echo '<div class="superior-faq-category-faqs superior-faq-'.$layout.'">';

				$args = array(
					'orderby' => 'menu_order',
					'order' => 'ASC',
					'posts_per_page' => -1,
					'post_type' => 'faq',
					'tax_query' => array(
						array(
							'taxonomy' => 'faq_category',
							'field' => 'id',
							'terms' => $category_object->term_id
						)
					)
				);
				$query = new WP_Query( $args );

				//loop starts
				if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post();

				if($excerpt == 'yes') {
					$content_text = apply_filters( 'the_excerpt', get_the_excerpt() );
				}
				else {
					$content_text = apply_filters( 'the_content', get_the_content() );
				}
				$content_text = str_replace( ']]>', ']]&gt;', $content_text );
				$content_text = do_shortcode($content_text);
				$title_html = $effect == 'tooltip' ? 'title="'.htmlspecialchars($content_text).'"' : '';
				$href = $deeplinking == 'yes' ? '#faq/'.sanitize_title(get_the_title()) : '#';
				if($permalink == 'yes' && $effect == 'tooltip') {
					$href = get_the_permalink();
				}
				?>

				<div class="superior-faq-item">
					<<?php echo esc_html( $headline_tag ); ?> class="superior-faq-title" style="<?php echo esc_attr( $title_style ); ?>">
						<a href="<?php echo esc_attr( $href ); ?>" class="<?php echo esc_attr( $effect == 'tooltip' ? 'sf-tooltip' : '' ); ?>" <?php echo $title_html; ?>>
							<?php if($open_icon != ''): ?>
							<i class="superior-faq-icon fa fa-<?php echo esc_attr( $open_icon ); ?>"></i>
							<?php endif; ?>
							<?php the_title(); ?>
						</a>
					</<?php echo esc_html( $headline_tag ); ?>>
					<div class="superior-faq-content <?php echo esc_attr( $open_icon == '' ? '' : 'superior-faq-padding' ); ?>"  style="<?php echo esc_attr( $content_style ); ?>">
						<?php echo htmlspecialchars_decode(esc_html( $content_text ) );  ?>
						<?php if( $permalink == 'yes' || $voting == 'yes' ):?>
						<p class="superior-faq-action-bar superior-faq-clearfix">
							<?php if( $permalink == 'yes' ):?>
							<a href="<?php the_permalink(); ?>" target="_self" class="superior-faq-permalink"><?php echo esc_html( $settings['permalink_label'] ); ?></a>
							<?php endif; ?>
							<?php if( $voting == 'yes' ) { echo $this->get_vote_html(get_the_id()); } ?>
						</p>
						<?php endif; ?>
					</div>
				</div>

				<?php endwhile; endif; wp_reset_postdata();

				echo '</div>'; //category faqs

			}

			echo '</div>'; //faq container

			?>
			<script type="text/javascript">

				jQuery(document).ready(function() {

					var $faqContainer = jQuery('#<?php echo esc_js( $selector ); ?>'),
						$faqTitleAnchor = $faqContainer.find('.superior-faq-title');

					if( <?php echo intval($deeplinking == 'yes'); ?> && _sf_getHashtag() && <?php echo intval($effect != 'tooltip'); ?> ) {

						var hash = _sf_getHashtag(),
							$requestedFaqAnchor = $faqTitleAnchor.children('a[href="#'+hash+'"]:first');

						if($requestedFaqAnchor.size() > 0) {
							jQuery(document).scrollTop($requestedFaqAnchor.offset().top);

							$requestedFaqAnchor.parents('.superior-faq-item:first').children('.superior-faq-content').slideDown(500)
							.parent('.superior-faq-item').find('.superior-faq-icon').removeClass('fa-<?php echo $open_icon; ?>').addClass('fa-<?php echo $close_icon; ?>');
						}

					}

					if($faqTitleAnchor.parents('.superior-effect-accordion').size() > 0) {
						$faqTitleAnchor.click(function(evt) {

							var $this = jQuery(this),
								$faqContent = $this.parent().children('.superior-faq-content');

							if(<?php echo intval($deeplinking == 'no'); ?>) {
								evt.preventDefault();
							}
							else {
								if( $faqContent.is(':visible') ) {
									var scrollV = jQuery(document).scrollTop();
									location.hash = '';
									jQuery(document).scrollTop(scrollV);
									evt.preventDefault();
								}
							}

							if(<?php echo intval($open_icon != ''); ?>) {
								$faqTitleAnchor.find('.superior-faq-icon').removeClass('fa-<?php echo $close_icon; ?>').addClass('fa-<?php echo $open_icon; ?>');

								if( $faqContent.is(':hidden') ) {
									$this.find('.superior-faq-icon').removeClass('fa-<?php echo $open_icon; ?>').addClass('fa-<?php echo $close_icon; ?>');
								}
								else {
									$this.find('.superior-faq-icon').removeClass('fa-<?php echo $close_icon; ?>').addClass('fa-<?php echo $open_icon; ?>');
								}

							}

							$faqTitleAnchor.parent().children('.superior-faq-content').stop().slideUp(300);

							if( $faqContent.is(':hidden') ) {
								$faqContent.slideDown(500);
							}

						});
					}

					var targets = $faqTitleAnchor.find('.sf-tooltip'),
				        target  = false,
				        tooltip = false,
				        title   = false;

				    targets.bind( 'mouseenter', function()
				    {
				        target  = jQuery( this );
				        tip     = target.attr( 'title' );
				        tooltip = jQuery( '<div id="sf-tooltip-container"></div>' );

				        if( !tip || tip == '' )
				            return false;



				        target.removeAttr( 'title' );
				        tooltip.css( 'opacity', 0 )
				               .html( tip )
				               .appendTo( 'body' );

				        var init_tooltip = function()
				        {
				            if( jQuery( window ).width() < tooltip.outerWidth() * 1.5 )
				                tooltip.css( 'max-width', jQuery( window ).width() / 2 );
				            else
				                tooltip.css( 'max-width', 340 );

				            var pos_left = target.offset().left + ( target.outerWidth() / 2 ) - ( tooltip.outerWidth() / 2 ),
				                pos_top  = target.offset().top - tooltip.outerHeight() - 20;

				            if( pos_left < 0 )
				            {
				                pos_left = target.offset().left + target.outerWidth() / 2 - 20;
				                tooltip.addClass( 'left' );
				            }
				            else
				                tooltip.removeClass( 'left' );

				            if( pos_left + tooltip.outerWidth() > jQuery( window ).width() )
				            {
				                pos_left = target.offset().left - tooltip.outerWidth() + target.outerWidth() / 2 + 20;
				                tooltip.addClass( 'right' );
				            }
				            else
				                tooltip.removeClass( 'right' );

				            if( pos_top < 0 )
				            {
				                var pos_top  = target.offset().top + target.outerHeight();
				                tooltip.addClass( 'top' );
				            }
				            else
				                tooltip.removeClass( 'top' );

				            tooltip.css( { left: pos_left, top: pos_top } )
				                   .animate( { top: '+=10', opacity: 1 }, 50 );
				        };

				        init_tooltip();
				        jQuery( window ).resize( init_tooltip );

				        var remove_tooltip = function()
				        {
				            tooltip.animate( { top: '-=10', opacity: 0 }, 50, function()
				            {
				                jQuery( this ).remove();
				            });

				            target.attr( 'title', tip );
				        };

				        target.bind( 'mouseleave', remove_tooltip );
				        tooltip.bind( 'click', remove_tooltip );
				    });

				});

				function _sf_getHashtag(){
					var url = location.href;
					hashtag = (url.indexOf('#faq') !== -1) ? decodeURI(url.substring(url.indexOf('#faq')+1,url.length)) : false;

					return hashtag;
				};

			</script>
			<?php
			if($voting == 'yes') { echo $this->get_vote_script($selector); }

			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}

		public function add_faq_search( $atts ) {

			extract( shortcode_atts( array(
				'categories' => '',
				'browse_to_faq' => 'no',
				'subtract_scrolling' => 0
			), $atts ) );

			$settings = get_option( 'superior_faq_settings' );

			$selector = uniqid('sf-search-');

			$args = array(
				'posts_per_page' => -1,
				'post_type' => 'faq'
			);

			if( !empty($categories) ) {
				$categories = explode(',', $categories);
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'faq_category',
						'field' => 'id',
						'terms' => $categories
					)
				);
			}

			//html output
			ob_start();
			?>
			<input type="text" class="sf-faq-search" id="<?php echo esc_attr( $selector ); ?>" placeholder="<?php echo esc_attr( $settings['search_placeholder'] ); ?>" />
			<script type="text/javascript">

				jQuery(document).ready(function() {

					var faqs = [
						<?php
						$query = new WP_Query( $args );
						if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>

						{	label: "<?php the_title(); ?>",
							slug: "<?php echo sanitize_title(get_the_title()); ?>",
							permalink: "<?php the_permalink(); ?>",
							id: 'sfaq-'+<?php the_id(); ?>
						},

						<?php endwhile; endif; wp_reset_postdata(); ?>
					];

					$resultsContainer = jQuery('<div class="smart_autocomplete_container" style="display:none"></div>');
					$resultsContainer.appendTo("body");

					jQuery('#<?php echo esc_js( $selector ); ?>').smartAutoComplete({
						resultsContainer: $resultsContainer,
						alignResultsContainer: true,
						resultElement: 'div',
						resultFormatter: function(r){
							return ("<div>" + r + "</div>");
						},
					    source: faqs,
					    typeAhead: true,
					    filter: function(term, source){

							var filtered_and_sorted_list = jQuery.map(source, function(item) {

								var score = item.label.toLowerCase().score(term.toLowerCase());

								if(score > 0) {
									return { 'name': item.label, 'value': score }
								}

							}).sort(function(a, b){ return b.value - a.value });

							return jQuery.map(filtered_and_sorted_list, function(item){
								return item.name;
							});
						}
					})
					.on('itemSelect', function(evt, item) {

						for(var i=0; i < faqs.length; ++i) {
							var faq = faqs[i];
							var decodedLabel = jQuery('<textarea />').html(faq.label).text();
							if(decodedLabel == item.innerHTML) {

								//browse to faq
								if(<?php echo intval($browse_to_faq == 'yes'); ?>) {
									window.open(faq.permalink, '_self');
									return;
								}

								//animate to anchor and show content
								var $requestedFaqAnchor = jQuery('[href="#faq/'+faq.slug+'"]:first'),
									subtract_scrolling = parseInt(<?php echo $subtract_scrolling; ?>);
								jQuery('html, body').animate({scrollTop: $requestedFaqAnchor.offset().top-subtract_scrolling}, 300);
								setTimeout(function() {
									$requestedFaqAnchor.parent('.superior-faq-title').click();
								}, 300);

								break;
							}
						}

						jQuery(this).trigger('lostFocus');
						this.value = '';

						evt.preventDefault();

					});

				});

			</script>
			<?php
			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}

		public function add_most_popular_faq( $atts ) {

			extract( shortcode_atts( array(
				'amount' => 5
			), $atts ) );

			$args = array(
				'posts_per_page' => $amount,
				'post_type' => 'faq',
				'meta_key' => 'sf_views_count',
				'orderby' => 'meta_value_num',
				'order' => 'DESC'
			);

			//html output
			ob_start();

			echo '<ul class="superior-most-popular">';

			$query = new WP_Query( $args );
			if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>

			<li><a href="<?php the_permalink(); ?>" target="_self"><?php the_title(); ?></a></li>

			<?php endwhile; endif; wp_reset_postdata();

			echo '</ul>';

			$output = ob_get_contents();

			ob_end_clean();

			return $output;

		}

		public function vote_faq() {

			if ( !isset($_POST['id']) || !isset($_POST['type']) ) {
				die;
			}

			$id = intval(trim($_POST['id']));
			$type = trim($_POST['type']);

			if( $type == 'up' ) {

				$value = intval(get_post_meta( $id, 'sf_vote_up', true ));
				$value++;
				update_post_meta( $id, 'sf_vote_up', $value );

			}
			else {

				$value = intval(get_post_meta( $id, 'sf_vote_down', true ));
				$value++;
				update_post_meta( $id, 'sf_vote_down', $value );

			}

			//send answer
			header('Content-Type: application/json');

			echo json_encode(array('type' => $type, 'value' => $value));

			die;

		}

		private function get_vote_html( $faq_id ) {

			//html output
			$vote_up_value = get_post_meta( $faq_id, 'sf_vote_up', true );
			$vote_down_value = get_post_meta( $faq_id, 'sf_vote_down', true );
			$vote_up_value = $vote_up_value === '' ? '0' : (string) $vote_up_value;
			$vote_down_value = $vote_down_value === '' ? '0' : (string) $vote_down_value;
			ob_start();
			?>
			<span class="superior-faq-voting" id="superior-faq-<?php echo esc_attr( $faq_id ); ?>">
				<a href="#" class="superior-faq-vote-up"><i class="fa fa-thumbs-up"></i><span><?php echo esc_html( $vote_up_value ); ?></span></a>
				<a href="#" class="superior-faq-vote-down"><i class="fa fa-thumbs-down"></i><span><?php echo esc_html( $vote_down_value ); ?></span></a>
			</span>
			<?php
			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}

		private function get_vote_script( $selector ) {

			ob_start();
			?>
			<script type="text/javascript">

				jQuery(document).ready(function() {

					jQuery('#<?php echo esc_js( $selector ); ?>').find('.superior-faq-voting a').click(function(evt) {

						if(typeof(Storage) === "undefined") {
							return false;
						}

						var $this = jQuery(this),
							id = $this.parent('.superior-faq-voting').attr('id').replace('superior-faq-', ''),
							type = $this.hasClass('superior-faq-vote-up') ? 'up' : 'down',
							storageString = 'sf-faq-'+id+'';

						var checkStorage = localStorage.getItem(storageString);

						if(checkStorage == null) {

							localStorage.setItem(storageString, 'yes');

							jQuery.ajax({
								url: "<?php echo admin_url('admin-ajax.php'); ?>",
								data: {
									action: 'sf_vote',
									id: id,
									type: type
								},
								type: 'post',
								dataType: 'json',
								success: function(data) {

									$this.children('span').text(data.value);

								}
							});

						}


						evt.preventDefault();

					});

				});
			</script>
			<?php
			$output = ob_get_contents();
			ob_end_clean();

			return $output;

		}

		private function install() {

			add_option('superior_faq_settings', $this->default_settings);
			add_option($this->version_field_name, $this->version);

	    }

	}

	new Superior_FAQ();

}

?>