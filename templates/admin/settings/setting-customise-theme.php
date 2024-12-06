<?php
/**
 * Template: Setting Customize Theme Options Tab.
 *
 * @package BD\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$theme_name = isset( $this->options['theme'] ) ? $this->options['theme'] : '';
?>
<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
	<input type="hidden" name="action" value="customize_theme" />
	<?php wp_nonce_field( 'customize_theme', 'customize-theme-nonce' ); ?>
	<div class="bdlms-tab-title-wrap">
		<h1 class="title">
			<?php esc_html_e( 'Choose your theme', 'bluedolphin-lms' ); ?>
		</h1>
		<div class="btn-wrap">
			<button type="submit" name="reset" class="button button-outline"><?php esc_html_e( 'Reset', 'bluedolphin-lms' ); ?></button>
			<input type="submit" class="button button-primary" name="submit" value="<?php esc_html_e( 'Save & Update', 'bluedolphin-lms' ); ?>" />
		</div>
		<div class="theme-status">
			<?php
			echo wp_kses(
				sprintf(
					'You are currently customizing %1$s Theme. <a href="%2$s">%3$s</a>',
					! empty( $this->options['theme'] ) ? esc_html( $this->options['theme'] ) : '',
					esc_url(
						add_query_arg(
							array(
								'tab'   => 'theme',
								'theme' => ! empty( $this->options['theme'] ) ? esc_html( $this->options['theme'] ) : '',
							),
							menu_page_url( 'bdlms-settings', false )
						)
					),
					esc_html( 'Change Theme' )
				),
				'bluedolphin-lms'
			);
			?>
		</div>
	</div>

	<div class="bdlms-customization-wrap">
		<div class="bdlms-customization-title">
			<?php esc_html_e( 'Choose Colors', 'bluedolphin-lms' ); ?>
		</div>
		<div class="bdlms-color-picker">
			<ul>
			<?php
			$theme_data = isset( $this->options[ $theme_name ] ) ? $this->options[ $theme_name ] : '';
			$colors     = \BD\Lms\layout_colors();
			$colors     = isset( $colors[ $theme_name ] ) ? $colors[ $theme_name ] : array();

			foreach ( $colors as $color => $value ) :
				$color_name = ucwords( str_replace( '_', ' ', $color ) );
				$input_id   = str_replace( '_', '-', $color );
				?>
				<li>
					<div class="bdlms-form-group">
						<label for="<?php echo esc_html( $input_id ); ?>"><?php echo esc_attr( $color_name ); ?></label>
						<div class="picker">
							<input id="<?php echo esc_html( $input_id ); ?>" class="color-picker" type="color" value="<?php echo ! empty( $theme_data['colors'][ $color ] ) ? esc_attr( $theme_data['colors'][ $color ] ) : esc_attr( $value ); ?>">
							<input id="<?php echo esc_html( $input_id ); ?>" name="<?php echo esc_html( $color ); ?>" class="color-input" type="text" autocomplete="off" spellcheck="false">
						</div>
					</div>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="bdlms-customization-wrap">
		<div class="bdlms-customization-title">
			<?php esc_html_e( 'Choose Fonts', 'bluedolphin-lms' ); ?>
		</div>
		<div class="bdlms-customization-tab">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="font-family-tab" data-tab="font-family" type="button" role="tab" aria-controls="font-family" aria-selected="true"><?php esc_html_e( 'Font Family', 'bluedolphin-lms' ); ?></button>
				</li>
				<?php
				$layout = \BD\Lms\layout_typographies();
				foreach ( $layout['tag'] as $index => $html_tag ) :
					$tab_title = ucwords( str_replace( '_', ' ', $html_tag ) );
					?>
					<li class="nav-item" role="presentation">
						<button class="nav-link" id="<?php echo esc_attr( $html_tag ); ?>-tab" data-tab="<?php echo esc_attr( $html_tag ); ?>" type="button" role="tab" aria-controls="<?php echo esc_attr( $html_tag ); ?>" aria-selected="true"><?php echo esc_html( $tab_title ); ?></button>
					</li>
				<?php endforeach; ?>
			</ul>
			<div class="tab-content bdlms-tab-content">
				<div class="tab-pane active" id="font-family" role="tabpanel" aria-labelledby="font-family-tab" tabindex="0">
					<div class="tab-content-wrap">
						<div class="tab-title"><?php esc_html_e( 'Font Family', 'bluedolphin-lms' ); ?></div>
						<div class="tab-content-row">
							<div class="font-prop-selector">
								<ul>
									<li>
										<div class="bdlms-form-group">
											<label for="font-family-global"><?php esc_html_e( 'All Headings (Google Fonts)', 'bluedolphin-lms' ); ?></label>
											<select class="form-select" data-style="fontFamily" name="font_family_global" data-target="font-family-global" id="font_family_global">
												<option <?php echo empty( $this->options[ $theme_name ]['typography']['global']['font_family'] ) ? 'selected' : ''; ?> value=""><?php esc_html_e( 'Default', 'bluedolphin-lms' ); ?></option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['global']['font_family'] ) && 'cursive' === $this->options[ $theme_name ]['typography']['global']['font_family'] ? 'selected' : ''; ?> value="cursive">cursive</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['global']['font_family'] ) && 'sans-serif' === $this->options[ $theme_name ]['typography']['global']['font_family'] ? 'selected' : ''; ?> value="sans-serif">sans-serif</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['global']['font_family'] ) && 'serif' === $this->options[ $theme_name ]['typography']['global']['font_family'] ? 'selected' : ''; ?> value="serif">serif</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['global']['font_family'] ) && 'system-ui' === $this->options[ $theme_name ]['typography']['global']['font_family'] ? 'selected' : ''; ?> value="system-ui">system-ui</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="font-family-body"><?php esc_html_e( 'All Body (Google Fonts)', 'bluedolphin-lms' ); ?></label>
											<select class="form-select" data-style="fontFamily" name="font_family_body" data-target="font-family-body" id="font_family_body">
												<option <?php echo empty( $this->options[ $theme_name ]['typography']['body']['font_family'] ) ? 'selected' : ''; ?> value=""><?php esc_html_e( 'Default', 'bluedolphin-lms' ); ?></option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['body']['font_family'] ) && 'cursive' === $this->options[ $theme_name ]['typography']['body']['font_family'] ? 'selected' : ''; ?> value="cursive">cursive</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['body']['font_family'] ) && 'sans-serif' === $this->options[ $theme_name ]['typography']['body']['font_family'] ? 'selected' : ''; ?> value="sans-serif">sans-serif</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['body']['font_family'] ) && 'serif' === $this->options[ $theme_name ]['typography']['body']['font_family'] ? 'selected' : ''; ?> value="serif">serif</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['body']['font_family'] ) && 'system-ui' === $this->options[ $theme_name ]['typography']['body']['font_family'] ? 'selected' : ''; ?> value="system-ui">system-ui</option>
											</select>
										</div>
									</li>
								</ul>
							</div>
							<div class="font-preview-wrap">
								<div class="font-preview-screen">
									<div class="title"><?php esc_html_e( 'Preview', 'bluedolphin-lms' ); ?></div>
									<div class="preview-text" id="font-family-global">
										<?php esc_html_e( 'The quick brown fox jumps over the lazy dog', 'bluedolphin-lms' ); ?>
									</div>
								</div>
								<div class="font-preview-screen">
									<div class="title"><?php esc_html_e( 'Preview', 'bluedolphin-lms' ); ?></div>
									<div class="preview-text small" id="font-family-body">
										<?php esc_html_e( 'The quick brown fox jumps over the lazy dog', 'bluedolphin-lms' ); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
				$html_tags    = $layout['tag'];
				$typographies = $layout['typography'];
				foreach ( $html_tags as $index => $html_tag ) :
					$tab_title = ucwords( str_replace( '_', ' ', $html_tag ) );
					?>
					<div class="tab-pane bdlms-tab-pane" id="<?php echo esc_attr( $html_tag ); ?>" role="tabpanel" aria-labelledby="<?php echo esc_attr( $html_tag ); ?>-tab" tabindex="0">
						<div class="tab-content-wrap">
							<div class="tab-title"><?php echo esc_html( $tab_title ); ?></div>
							<div class="tab-content-row">
								<div class="font-prop-selector">
									<ul>
									<?php
									foreach ( $typographies as $typography => $value ) :
										$label      = ucwords( str_replace( '_', ' ', $typography ) );
										$data_style = str_replace( '_', '-', $typography );
										?>
										<li>
											<div class="bdlms-form-group">
												<label for="<?php echo esc_attr( $typography . '_' . $html_tag ); ?>"><?php echo esc_html( $label ); ?></label>
												<select class="form-select" data-style="<?php echo esc_attr( $data_style ); ?>" name="<?php echo esc_attr( $typography . '_' . $html_tag ); ?>" data-target="<?php echo esc_attr( 'preview_' . $html_tag ); ?>" id="<?php echo esc_attr( $typography . '_' . $html_tag ); ?>" <?php echo 0 !== $index ? 'disabled' : ''; ?>>
													<option <?php echo empty( $this->options[ $theme_name ]['typography'][ $html_tag ][ $typography ] ) ? 'selected' : ''; ?> value=""><?php esc_html_e( 'Default', 'bluedolphin-lms' ); ?></option>
												<?php foreach ( $value as $v ) : ?>
														<option <?php echo ! empty( $this->options[ $theme_name ]['typography'][ $html_tag ][ $typography ] ) && $v === $this->options[ $theme_name ]['typography'][ $html_tag ][ $typography ] ? 'selected' : ''; ?> value="<?php echo esc_attr( $v ); ?>"><?php echo esc_html( $v ); ?></option>
													<?php endforeach; ?>
												</select>
											</div>
										</li>
										<?php endforeach; ?>
									</ul>
								</div>
								<div class="font-preview-wrap">
									<div class="font-preview-screen">
										<div class="title"><?php esc_html_e( 'Preview', 'bluedolphin-lms' ); ?></div>
										<div class="preview-text" id="<?php echo esc_attr( 'preview_' . $html_tag ); ?>">
											<?php esc_html_e( 'The quick brown fox jumps over the lazy dog', 'bluedolphin-lms' ); ?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
				endforeach;
				?>
			</div>
		</div>
	</div>

	<div class="bdlms-theme-update-wrap">
		<div class="btn-wrap">
			<button type="submit" name="reset" class="button button-outline"><?php esc_html_e( 'Reset', 'bluedolphin-lms' ); ?></button>
			<input type="submit" class="button button-primary" name="submit" value="<?php esc_html_e( 'Save & Update', 'bluedolphin-lms' ); ?>" />
		</div>
	</div>
</form>
