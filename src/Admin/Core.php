<?php
/**
 * The file that defines the admin plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://getbluedolphin.com
 * @since      1.0.0
 *
 * @package    BlueDolphin\Lms\Admin
 *
 * phpcs:disable WordPress.NamingConventions.ValidHookName.UseUnderscores
 */

namespace BlueDolphin\Lms\Admin;

use const BlueDolphin\Lms\PARENT_MENU_SLUG;

/**
 * Admin class
 */
class Core implements \BlueDolphin\Lms\Interfaces\AdminCore {

	/**
	 * Plugin version.
	 *
	 * @var int Plugin version.
	 * @since 1.0.0
	 */
	public $version;

	/**
	 * The main instance.
	 *
	 * @var BlueDolphin Main class instance.
	 * @since 1.0.0
	 */
	public $instance;

	/**
	 * Calling class construct.
	 *
	 * @param string $version Plugin version.
	 * @param object $instance Plugin main instance.
	 */
	public function __construct( $version, \BlueDolphin\Lms\BlueDolphin $instance ) { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$this->version  = $version;
		$this->instance = $instance;

		// Load modules.
		new \BlueDolphin\Lms\Admin\Users\Users();

		// Hooks.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'backend_scripts' ) );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'disable_gutenberg_editor' ), 10, 2 );
		add_action( 'admin_footer', array( $this, 'js_templates' ) );
	}

	/**
	 * Register admin menu.
	 */
	public function register_admin_menu() {
		$hook = add_menu_page(
			__( 'BlueDolphin LMS', 'bluedolphin-lms' ),
			__( 'BlueDolphin LMS', 'bluedolphin-lms' ),
			apply_filters( 'bluedolphin/menu/capability', 'manage_options' ),
			PARENT_MENU_SLUG,
			array( $this, 'render_menu_page' ),
			'dashicons-welcome-learn-more',
			apply_filters( 'bluedolphin/menu/position', 4 )
		);
	}

	/**
	 * Render admin page.
	 */
	public function render_menu_page() {
		echo 'main page';
	}

	/**
	 * Filters whether a post is able to be edited in the block editor.
	 *
	 * @since 5.0.0
	 *
	 * @param bool   $use_block_editor  Whether the post type can be edited or not. Default true.
	 * @param string $post_type         The post type being checked.
	 */
	public function disable_gutenberg_editor( $use_block_editor, $post_type ) {
		if ( ! $use_block_editor ) {
			return $use_block_editor;
		}
		if ( in_array( $post_type, apply_filters( 'bluedolphin/disable/block-editor', array( \BlueDolphin\Lms\BDLMS_QUESTION_CPT ) ), true ) ) {
			return false;
		}
		return $use_block_editor;
	}

	/**
	 * Enqueue scripts/styles for backend area.
	 */
	public function backend_scripts() {
		wp_register_script( \BlueDolphin\Lms\BDLMS_QUESTION_CPT, BDLMS_ASSETS . '/js/questions.js', array( 'jquery' ), $this->version, true );
		wp_register_style( \BlueDolphin\Lms\BDLMS_QUESTION_CPT, BDLMS_ASSETS . '/css/questions.css', array(), $this->version );
	}

	/**
	 * Load JS based templates.
	 */
	public function js_templates() {
		?>
			<script type="text/template" id="show_answer">
				<td colspan="8" class="colspanchange">
					<div class="inline-edit-wrapper" role="region" aria-labelledby="quick-edit-legend">
						<fieldset class="inline-edit-col-left">
							<div class="bdlms-show-ans-wrap">
								<div class="bdlms-show-ans-header">
									<legend class="inline-edit-legend"><?php esc_html_e( 'Show Answers', 'bluedolphin-lms' ); ?></legend>
									<div>
										<label>Type:</label>
										<select>
											<option>option 1</option>
											<option>option 2</option>
											<option>option 3</option>
										</select>
									</div>
								</div>
								<div class="bdlms-show-ans-title-marks">
									<div>
										<label>Title</label>
										<input type="text">
									</div>
									<div class="marks-input">
										<label>Marks</label>
										<input type="text">
									</div>
								</div>

								<div class="bdlms-options-table">
									<div class="bdlms-options-table__header">
										<ul class="bdlms-options-table__list">
											<li>Options</li>
											<li class="bdlms-option-check-td">Correct Option</li>
											<li class="bdlms-option-action"></li>
										</ul>
									</div>
									<div class="bdlms-options-table__body">
										<div class="bdlms-options-table__list-wrap">
											<ul class="bdlms-options-table__list">
												<li>
													<div class="bdlms-options-value">
														<div class="bdlms-options-drag">
															<svg class="icon" width="8" height="13">
																<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
															</svg>
														</div>
														<div class="bdlms-options-no">A.</div>
														<input type="text">
													</div>
												</li>
												<li class="bdlms-option-check-td">
													<input type="radio">
												</li>
												<li class="bdlms-option-action">
													<button type="button">
														<svg class="icon" width="12" height="12">
															<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
														</svg>
													</button>
												</li>
											</ul>
											<ul class="bdlms-options-table__list">
												<li>
													<div class="bdlms-options-value">
														<div class="bdlms-options-drag">
															<svg class="icon" width="8" height="13">
																<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
															</svg>
														</div>
														<div class="bdlms-options-no">A.</div>
														<input type="text">
													</div>
												</li>
												<li class="bdlms-option-check-td">
													<input type="radio">
												</li>
												<li class="bdlms-option-action">
													<button type="button">
														<svg class="icon" width="12" height="12">
															<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
														</svg>
													</button>
												</li>
											</ul>
											<ul class="bdlms-options-table__list">
												<li>
													<div class="bdlms-options-value">
														<div class="bdlms-options-drag">
															<svg class="icon" width="8" height="13">
																<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
															</svg>
														</div>
														<div class="bdlms-options-no">A.</div>
														<input type="text">
													</div>
												</li>
												<li class="bdlms-option-check-td">
													<input type="radio">
												</li>
												<li class="bdlms-option-action">
													<button type="button">
														<svg class="icon" width="12" height="12">
															<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
														</svg>
													</button>
												</li>
											</ul>
										</div>
									</div>
								</div>

								<div class="bdlms-options-table">
									<div class="bdlms-options-table__header">
										<ul class="bdlms-options-table__list">
											<li>Options</li>
											<li class="bdlms-option-check-td">Correct Option</li>
										</ul>
									</div>
									<div class="bdlms-options-table__body">
										<div class="bdlms-options-table__list-wrap">
											<ul class="bdlms-options-table__list">
												<li>
													<div class="bdlms-options-value">
														<div class="bdlms-options-drag">
															<svg class="icon" width="8" height="13">
																<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
															</svg>
														</div>
														<input type="text" class="bdlms-option-value-input" value="True" readonly>
													</div>
												</li>
												<li class="bdlms-option-check-td">
													<input type="radio">
												</li>
											</ul>
											<ul class="bdlms-options-table__list">
												<li>
													<div class="bdlms-options-value">
														<div class="bdlms-options-drag">
															<svg class="icon" width="8" height="13">
																<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
															</svg>
														</div>
														<input type="text" class="bdlms-option-value-input" value="True" readonly>
													</div>
												</li>
												<li class="bdlms-option-check-td">
													<input type="radio">
												</li>
											</ul>
										</div>
									</div>
								</div>

								<div class="bdlms-options-table">
									<div class="bdlms-options-table__header">
										<ul class="bdlms-options-table__list">
											<li>Options</li>
											<li class="bdlms-option-check-td">Correct Option</li>
											<li class="bdlms-option-action"></li>
										</ul>
									</div>
									<div class="bdlms-options-table__body">
										<div class="bdlms-options-table__list-wrap">
											<ul class="bdlms-options-table__list">
												<li>
													<div class="bdlms-options-value">
														<div class="bdlms-options-drag">
															<svg class="icon" width="8" height="13">
																<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
															</svg>
														</div>
														<div class="bdlms-options-no">A.</div>
														<input type="text">
													</div>
												</li>
												<li class="bdlms-option-check-td">
													<input type="checkbox">
												</li>
												<li class="bdlms-option-action">
													<button type="button">
														<svg class="icon" width="12" height="12">
															<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
														</svg>
													</button>
												</li>
											</ul>
											<ul class="bdlms-options-table__list">
												<li>
													<div class="bdlms-options-value">
														<div class="bdlms-options-drag">
															<svg class="icon" width="8" height="13">
																<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
															</svg>
														</div>
														<div class="bdlms-options-no">A.</div>
														<input type="text">
													</div>
												</li>
												<li class="bdlms-option-check-td">
													<input type="checkbox">
												</li>
												<li class="bdlms-option-action">
													<button type="button">
														<svg class="icon" width="12" height="12">
															<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
														</svg>
													</button>
												</li>
											</ul>
											<ul class="bdlms-options-table__list">
												<li>
													<div class="bdlms-options-value">
														<div class="bdlms-options-drag">
															<svg class="icon" width="8" height="13">
																<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#drag"></use>
															</svg>
														</div>
														<div class="bdlms-options-no">A.</div>
														<input type="text">
													</div>
												</li>
												<li class="bdlms-option-check-td">
													<input type="checkbox">
												</li>
												<li class="bdlms-option-action">
													<button type="button">
														<svg class="icon" width="12" height="12">
															<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite.svg#trash"></use>
														</svg>
													</button>
												</li>
											</ul>
										</div>
									</div>
								</div>

								<div class="bdlms-add-accepted-answers">
									<h3>Add Accepted Answers</h3>
									<ul>
										<li>
											<label>Mandatory</label>
											<input type="text">
										</li>
										<li>
											<label>Optional</label>
											<input type="text">
										</li>
										<li>
											<label>Optional</label>
											<input type="text">
										</li>
										<li>
											<label>Optional</label>
											<input type="text">
										</li>
										<li>
											<label>Optional</label>
											<input type="text">
										</li>
									</ul>
								</div>

								<div class="bdlms-show-ans-action">
									<button type="button" class="button">Add a New Answer</button>
									<button type="button" class="button">Cancel</button>
									<button type="button" class="button button-primary">Save</button>
								</div>
							</div>
							<div class="inline-edit-col">
								<h1><?php echo esc_attr( time() ); ?></h1>
							<div>
						</fieldset>
					</div>
				</td>
			</script>
		<?php
	}
}
