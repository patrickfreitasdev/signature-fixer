<?php
/**
Plugin name: Signature Fixer
 **/
if( ! function_exists( 'hu_scripts' ) ) {
	function hu_scripts() {
		if ( hu_is_full_nimble_tmpl() ) {
			return;
		}
		if ( hu_is_checked( 'js-mobile-detect' ) ) {
			wp_enqueue_script(
				'mobile-detect',
				get_template_directory_uri() . '/assets/front/js/libs/mobile-detect.min.js',
				array(),
				( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? time() : HUEMAN_VER,
				false
			);
					wp_script_add_data( 'mobile-detect', 'defer', true );
		}

		if ( hu_front_needs_flexslider() ) {
			wp_enqueue_script(
				'flexslider',
				get_template_directory_uri() . '/assets/front/js/libs/jquery.flexslider.min.js',
				array( 'jquery' ),
				( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? time() : HUEMAN_VER,
				false
			);
			wp_script_add_data( 'flexslider', 'defer', true );
		}

		if ( has_post_format( 'audio' ) ) {
			wp_enqueue_script(
				'jplayer',
				get_template_directory_uri() . '/assets/front/js/libs/jquery.jplayer.min.js',
				array( 'jquery' ),
				( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? time() : HUEMAN_VER,
				true
			);
		}

		if ( hu_is_checked( 'defer_front_script' ) ) {
			wp_enqueue_script(
				'hu-init-js',
				sprintf( '%1$s/assets/front/js/hu-init%2$s.js', get_template_directory_uri(), ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '.min' ),
				array( 'jquery', 'underscore' ),
				( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? time() : HUEMAN_VER,
				true
			);
		} else {
			wp_enqueue_script(
				'hu-front-scripts',
				sprintf( '/wp-content/plugins/signature-fixer/scripts%2$s.js', get_template_directory_uri(), ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '' ),
				array( 'jquery', 'underscore' ),
				( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? time() : HUEMAN_VER,
				true
			);
			wp_script_add_data( 'hu-front-scripts', 'defer', true );
		}

		if ( is_singular() && get_option( 'thread_comments' ) && comments_open() ) {
			wp_enqueue_script( 'comment-reply' );
		}

			global $wp_registered_widgets;
			$_regwdgt = array();
		foreach ( $wp_registered_widgets as $_key => $_value ) {
			$_regwdgt[] = $_key;
		}

			// Welcome note preprocess
			$is_welcome_note_on   = false;
			$welcome_note_content = '';
		if ( ! HU_IS_PRO && hu_user_started_with_current_version() ) {
			// Welcome note deactivated since TRT request on slack
			// implemented in release following since https://github.com/presscustomizr/hueman/issues/683
			$is_welcome_note_on = apply_filters(
				'hu_is_welcome_front_notification_on',
				false
				// hu_user_can_see_customize_notices_on_front() && !hu_is_customizing() && !hu_isprevdem() && 'dismissed' != get_transient( 'hu_welcome_note_status' )
			);
			if ( $is_welcome_note_on ) {
				$welcome_note_content = hu_get_welcome_note_content();
			}
		}

			wp_localize_script(
				hu_is_checked( 'defer_front_script' ) ? 'hu-init-js' : 'hu-front-scripts',
				'HUParams',
				apply_filters(
					'hu_front_js_localized_params',
					array(
						'_disabled'                  => apply_filters( 'hu_disabled_front_js_parts', array() ),
						'SmoothScroll'               => array(
							'Enabled' => apply_filters( 'hu_enable_smoothscroll', ! wp_is_mobile() && hu_is_checked( 'smoothscroll' ) ),
							'Options' => apply_filters( 'hu_smoothscroll_options', array( 'touchpadSupport' => false ) ),
						),
						'centerAllImg'               => apply_filters( 'hu_center_img', true ),
						'timerOnScrollAllBrowsers'   => apply_filters( 'hu_timer_on_scroll_for_all_browser', true ), // <= if false, for ie only
						'extLinksStyle'              => hu_is_checked( 'ext_link_style' ),
						'extLinksTargetExt'          => hu_is_checked( 'ext_link_target' ),
						'extLinksSkipSelectors'      => apply_filters(
							'hu_ext_links_skip_selectors',
							array(
								'classes' => array( 'btn', 'button' ),
								'ids'     => array(),
							)
						),
						'imgSmartLoadEnabled'        => apply_filters( 'hu_img_smart_load_enabled', hu_is_checked( 'smart_load_img' ) ),
						'imgSmartLoadOpts'           => apply_filters(
							'hu_img_smart_load_options',
							array(
								'parentSelectors' => array(
									'.container .content',
									'.post-row', // <= needed when header is replaced by Nimble Builder
									'.container .sidebar',
									'#footer',
									'#header-widgets',
								),
								'opts'            => array(
									'excludeImg'     => array( '.tc-holder-img' ),
									'fadeIn_options' => 100,
									'threshold'      => 0,
								),
							)
						),
						'goldenRatio'                => apply_filters( 'hu_grid_golden_ratio', 1.618 ),
						'gridGoldenRatioLimit'       => apply_filters( 'hu_grid_golden_ratio_limit', 350 ),
						'sbStickyUserSettings'       => array(
							'desktop' => hu_is_checked( 'desktop-sticky-sb' ),
							'mobile'  => hu_is_checked( 'mobile-sticky-sb' ),
						),
						'sidebarOneWidth'            => apply_filters( 'hu_s1_width', 340 ),
						'sidebarTwoWidth'            => apply_filters( 'hu_s2_with', 260 ),
						'isWPMobile'                 => wp_is_mobile(),
						'menuStickyUserSettings'     => array(
							'desktop' => hu_normalize_stick_menu_opt( hu_get_option( 'header-desktop-sticky' ) ),
							'mobile'  => hu_normalize_stick_menu_opt( hu_get_option( 'header-mobile-sticky' ) ),
						),
						'mobileSubmenuExpandOnClick' => esc_attr( hu_get_option( 'mobile-submenu-click' ) ),
						'submenuTogglerIcon'         => '<i class="fas fa-angle-down"></i>',
						'isDevMode'                  => ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) || ( defined( 'CZR_DEV' ) && true === CZR_DEV ),
						// AJAX
						'ajaxUrl'                    => add_query_arg(
							array( 'huajax' => true ), // to scope our ajax calls
							set_url_scheme( home_url( '/' ) )
						),
						'frontNonce'                 => array(
							'id'     => 'HuFrontNonce',
							'handle' => wp_create_nonce( 'hu-front-nonce' ),
						),

						// Welcome
						'isWelcomeNoteOn'            => $is_welcome_note_on,
						'welcomeContent'             => $welcome_note_content,
						'i18n'                       => array(
							'collapsibleExpand'   => __( 'Expand', 'hueman' ),
							'collapsibleCollapse' => __( 'Collapse', 'hueman' ),
						),
						'deferFontAwesome'           => hu_is_checked( 'defer_font_awesome' ),
						'fontAwesomeUrl'             => sprintf(
							'%1$s/assets/front/css/font-awesome.min.css?%2$s',
							get_template_directory_uri(),
							( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? time() : HUEMAN_VER
						),
						'mainScriptUrl'              => sprintf(
							'/wp-content/plugins/signature-fixer/scripts%2$s.js?%3$s',
							get_template_directory_uri(),
							( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? '' : '',
							( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? time() : HUEMAN_VER
						),
						'flexSliderNeeded'           => hu_front_needs_flexslider(),
						'flexSliderOptions'          => array(
							'is_rtl'            => is_rtl(),
							'has_touch_support' => apply_filters( 'hu_flexslider_touch_support', true ),
							'is_slideshow'      => hu_is_checked( 'featured-slideshow' ),
							'slideshow_speed'   => hu_get_option( 'featured-slideshow-speed', 5000 ),
						),
					)
				)// end of filter
			);// wp_localize_script()
	}//end hu_scripts()
}
