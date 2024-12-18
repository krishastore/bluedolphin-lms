<?php
/**
 * The file that defines the shortcode register functionality.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Shortcode
 */

namespace BlueDolphin\Lms\Shortcode;

use const BlueDolphin\Lms\BDLMS_SCRIPT_HANDLE;
use const BlueDolphin\Lms\BDLMS_QUESTION_VALIDATE_NONCE;

/**
 * Shortcode register manage class.
 */
abstract class Register {

	/**
	 * Shortcode tagName.
	 *
	 * @var string $shortcode_tag
	 * @since 1.0.0
	 */
	public $shortcode_tag = '';

	/**
	 * Script/Style handler.
	 *
	 * @var string $handler Handler.
	 */
	public $handler = BDLMS_SCRIPT_HANDLE . 'frontend';

	/**
	 * Init hooks.
	 */
	public function init() {
		// Calling hooks.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		if ( ! shortcode_exists( $this->shortcode_tag ) ) {
			add_shortcode( 'bdlms_' . $this->shortcode_tag, array( $this, 'register_shortcode' ) );
		}
	}

	/**
	 * Set shortcode tab.
	 *
	 * @param string $tag Shortcode tag name.
	 */
	public function set_shortcode_tag( $tag = '' ) {
		$this->shortcode_tag = $tag;
	}

	/**
	 * Register frontend scripts.
	 */
	public function enqueue_scripts() {
		$version = bdlms_run()->get_version();
		if ( defined( 'BDLMS_ASSETS_VERSION' ) && ! empty( BDLMS_ASSETS_VERSION ) ) {
			$version = BDLMS_ASSETS_VERSION;
		}
		if ( function_exists( 'bdlms_addons_styles' ) ) {
			\bdlms_addons_styles();
		} else {
			wp_register_style( $this->handler, BDLMS_ASSETS . '/css/frontend.css', array(), $version );
		}
		wp_register_script( $this->handler, BDLMS_ASSETS . '/js/build/frontend.js', array( 'jquery' ), $version, true );
		wp_register_script( $this->handler . '-plyr', BDLMS_ASSETS . '/js/build/plyr.js', array( 'jquery', $this->handler ), $version, true );
		wp_register_script( $this->handler . '-smartwizard', BDLMS_ASSETS . '/js/build/smartwizard.js', array( 'jquery' ), $version, true );
		wp_register_script( $this->handler . '-countdowntimer', BDLMS_ASSETS . '/js/build/countdowntimer.js', array( 'jquery' ), $version, true );
		wp_register_script( $this->handler . '-swiper', BDLMS_ASSETS . '/js/build/swiper.js', array( 'jquery' ), $version, true );
		$curriculum_type = get_query_var( 'curriculum_type' );
		$userinfo        = wp_get_current_user();
		$user_name       = $userinfo->display_name;

		wp_localize_script(
			$this->handler,
			'BdlmsObject',
			array(
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				'securityNonce' => wp_create_nonce( BDLMS_QUESTION_VALIDATE_NONCE ),
				'nonce'         => wp_create_nonce( BDLMS_BASEFILE ),
				'quizId'        => ! empty( $curriculum_type ) && 'quiz' === $curriculum_type ? (int) get_query_var( 'item_id' ) : 0,
				'courseId'      => ! empty( $curriculum_type ) && 'quiz' === $curriculum_type ? get_the_ID() : 0,
				'fileName'      => 'BD-' . substr( strtoupper( wp_hash( $user_name ) ), 0, 5 ),
				'currentUrl'    => get_the_permalink(),
				'iconUrl'       => BDLMS_ASSETS . '/images/plyr.svg',
				'blankVideo'    => BDLMS_ASSETS . '/images/blank.mp4',
			)
		);

		wp_register_style( $this->handler . '-plyr', BDLMS_ASSETS . '/css/plyr.css', array(), $version );
		wp_register_style( $this->handler . '-smartwizard', BDLMS_ASSETS . '/css/smartwizard.css', array(), $version );
		wp_register_style( $this->handler . '-swiper', BDLMS_ASSETS . '/css/swiper.css', array(), $version );
	}

	/**
	 * Register shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public function register_shortcode( $atts ) {}
}
