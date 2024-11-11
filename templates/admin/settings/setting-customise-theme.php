<?php
/**
 * Template: Setting Customize Theme Options Tab.
 *
 * @package BlueDolphin\Lms
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
			<?php echo esc_html_e( 'Choose your theme', 'bluedolphin-lms' ); ?>
		</h1>
		<div class="btn-wrap">
			<button type="submit" name="reset" class="button button-outline"><?php echo esc_html_e( 'Reset', 'bluedolphin-lms' ); ?></button>
			<input type="submit" class="button button-primary" name="submit" value="<?php echo esc_html_e( 'Save & Update', 'bluedolphin-lms' ); ?>" />
		</div>
		<div class="theme-status">
			<?php
			echo wp_kses(
				sprintf(
					'You are currently customizing <a href="%1$s">%2$s</a> Theme. <a href="%3$s">%4$s</a>',
					esc_url(
						add_query_arg(
							array(
								'tab'   => 'theme',
								'theme' => ! empty( $this->options['theme'] ) ? esc_html( $this->options['theme'] ) : '',
							),
							menu_page_url( 'bdlms-settings', false )
						)
					),
					! empty( $this->options['theme'] ) ? esc_html( $this->options['theme'] ) : '',
					! empty( $this->options['theme'] ) ? esc_html( $this->options['theme'] ) : '',
					esc_html_e( 'Change Theme', 'bluedolphin-lms' )
				),
				'bluedolphin-lms'
			);
			?>
		</div>
	</div>

	<div class="bdlms-customization-wrap">
		<div class="bdlms-customization-title">
			<?php echo esc_html_e( 'Choose Colors', 'bluedolphin-lms' ); ?>
		</div>
		<div class="bdlms-color-picker">
			<ul>
			<?php
			$theme_data = isset( $this->options[ $theme_name ] ) ? $this->options[ $theme_name ] : '';
			$colors     = array( 'primary_color', 'secondary_color', 'background_color', 'background_color_light', 'border_color', 'white_shade_color', 'heading_color', 'paragraph_color', 'paragraph_color_light', 'link_color', 'icon_color', 'success_color', 'error_color' );

			foreach ( $colors as $color ) :
				$color_name = ucwords( str_replace( '_', ' ', $color ) );
				$input_id   = str_replace( '_', '-', $color );
				?>
				<li>
					<div class="bdlms-form-group">
						<label for="<?php echo esc_html( $input_id ); ?>"><?php echo esc_attr( $color_name ); ?></label>
						<div class="picker">
							<input id="<?php echo esc_html( $input_id ); ?>" class="color-picker" type="color" value="<?php echo ! empty( $theme_data['colors'][ $color ] ) ? esc_attr( $theme_data['colors'][ $color ] ) : '#FFB61A'; ?>">
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
			<?php echo esc_html_e( 'Choose Fonts', 'bluedolphin-lms' ); ?>
		</div>
		<div class="bdlms-customization-tab">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="heading-1-tab" data-tab="heading-1" type="button" role="tab" aria-controls="heading-1" aria-selected="true"><?php echo esc_html_e( 'Heading 1', 'bluedolphin-lms' ); ?></button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="heading-2-tab" data-tab="heading-2" type="button" role="tab" aria-controls="heading-2" aria-selected="false"><?php echo esc_html_e( 'Heading 2', 'bluedolphin-lms' ); ?></button>
				</li>
			</ul>
			<div class="tab-content bdlms-tab-content">
				<div class="tab-pane bdlms-tab-pane active" id="heading-1" role="tabpanel" aria-labelledby="heading-1-tab" tabindex="0">
					<div class="tab-content-wrap">
						<div class="tab-title"><?php echo esc_html_e( 'Heading 1', 'bluedolphin-lms' ); ?></div>
						<div class="tab-content-row">
							<div class="font-prop-selector">
								<ul>
									<li>
										<div class="bdlms-form-group">
											<label for="font-family-h1"><?php echo esc_html_e( 'Font Family (Google Fonts)', 'bluedolphin-lms' ); ?></label>
											<select class="form-select" data-style="fontFamily" name="font_family_h1" data-target="preview-h1" id="font-family-h1">
												<option <?php echo empty( $this->options[ $theme_name ]['typography']['h1']['font_family'] ) ? 'selected' : ''; ?> value=""><?php echo esc_html_e( 'Default', 'bluedolphin-lms' ); ?></option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_family'] ) && 'cursive' === $this->options[ $theme_name ]['typography']['h1']['font_family'] ? 'selected' : ''; ?> value="cursive">cursive</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_family'] ) && 'sans-serif' === $this->options[ $theme_name ]['typography']['h1']['font_family'] ? 'selected' : ''; ?> value="sans-serif">sans-serif</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_family'] ) && 'serif' === $this->options[ $theme_name ]['typography']['h1']['font_family'] ? 'selected' : ''; ?> value="serif">serif</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_family'] ) && 'system-ui' === $this->options[ $theme_name ]['typography']['h1']['font_family'] ? 'selected' : ''; ?> value="system-ui">system-ui</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="font-weight-h1"><?php echo esc_html_e( 'Font Weight', 'bluedolphin-lms' ); ?></label>
											<select class="form-select" data-style="fontWeight" name="font_weight_h1" data-target="preview-h1" id="font-weight-h1">
												<option <?php echo empty( $this->options[ $theme_name ]['typography']['h1']['font_weight'] ) ? 'selected' : ''; ?> value=""><?php echo esc_html_e( 'Default', 'bluedolphin-lms' ); ?></option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_weight'] ) && '300' === $this->options[ $theme_name ]['typography']['h1']['font_weight'] ? 'selected' : ''; ?> value="300">300</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_weight'] ) && '400' === $this->options[ $theme_name ]['typography']['h1']['font_weight'] ? 'selected' : ''; ?> value="400">400</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_weight'] ) && '500' === $this->options[ $theme_name ]['typography']['h1']['font_weight'] ? 'selected' : ''; ?> value="500">500</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_weight'] ) && '600' === $this->options[ $theme_name ]['typography']['h1']['font_weight'] ? 'selected' : ''; ?> value="600">600</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_weight'] ) && '700' === $this->options[ $theme_name ]['typography']['h1']['font_weight'] ? 'selected' : ''; ?> value="700">700</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="font-size-h1"><?php echo esc_html_e( 'Font Size', 'bluedolphin-lms' ); ?></label>
											<select class="form-select" data-style="fontSize" name="font_size_h1" data-target="preview-h1" id="font-size-h1">
												<option <?php echo empty( $this->options[ $theme_name ]['typography']['h1']['font_size'] ) ? 'selected' : ''; ?> value=""><?php echo esc_html_e( 'Default', 'bluedolphin-lms' ); ?></option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_size'] ) && '16px' === $this->options[ $theme_name ]['typography']['h1']['font_size'] ? 'selected' : ''; ?> value="16px">16px</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_size'] ) && '18px' === $this->options[ $theme_name ]['typography']['h1']['font_size'] ? 'selected' : ''; ?> value="18px">18px</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['font_size'] ) && '20px' === $this->options[ $theme_name ]['typography']['h1']['font_size'] ? 'selected' : ''; ?> value="20px">20px</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="text-transform-h1"><?php echo esc_html_e( 'Text Transform', 'bluedolphin-lms' ); ?></label>
											<select class="form-select" data-style="textTransform" name="text_transform_h1" data-target="preview-h1" id="text-transform-h1">
												<option <?php echo empty( $this->options[ $theme_name ]['typography']['h1']['text_transform'] ) ? 'selected' : ''; ?> value=""><?php echo esc_html_e( 'Default', 'bluedolphin-lms' ); ?></option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['text_transform'] ) && 'none' === $this->options[ $theme_name ]['typography']['h1']['text_transform'] ? 'selected' : ''; ?> value="none">none</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['text_transform'] ) && 'capitalize' === $this->options[ $theme_name ]['typography']['h1']['text_transform'] ? 'selected' : ''; ?> value="capitalize">capitalize</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['text_transform'] ) && 'uppercase' === $this->options[ $theme_name ]['typography']['h1']['text_transform'] ? 'selected' : ''; ?> value="uppercase">uppercase</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['text_transform'] ) && 'lowercase' === $this->options[ $theme_name ]['typography']['h1']['text_transform'] ? 'selected' : ''; ?> value="lowercase">lowercase</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="line-height-h1"><?php echo esc_html_e( 'Line Height', 'bluedolphin-lms' ); ?></label>
											<select class="form-select" data-style="lineHeight" name="line_height_h1" data-target="preview-h1" id="line-height-h1">
												<option <?php echo empty( $this->options[ $theme_name ]['typography']['h1']['line_height'] ) ? 'selected' : ''; ?> value=""><?php echo esc_html_e( 'Default', 'bluedolphin-lms' ); ?></option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['line_height'] ) && 'normal' === $this->options[ $theme_name ]['typography']['h1']['line_height'] ? 'selected' : ''; ?> value="normal">normal</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['line_height'] ) && '1' === $this->options[ $theme_name ]['typography']['h1']['line_height'] ? 'selected' : ''; ?> value="1">1</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['line_height'] ) && '1.2' === $this->options[ $theme_name ]['typography']['h1']['line_height'] ? 'selected' : ''; ?> value="1.2">1.2</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['line_height'] ) && '1.5' === $this->options[ $theme_name ]['typography']['h1']['line_height'] ? 'selected' : ''; ?> value="1.5">1.5</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="letter-spacing-h1"><?php echo esc_html_e( 'Letter Spacing', 'bluedolphin-lms' ); ?></label>
											<select class="form-select" data-style="letterSpacing" name="letter_spacing_h1" data-target="preview-h1" id="letter-spacing-h1">
												<option <?php echo empty( $this->options[ $theme_name ]['typography']['h1']['letter_spacing'] ) ? 'selected' : ''; ?> value=""><?php echo esc_html_e( 'Default', 'bluedolphin-lms' ); ?></option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['letter_spacing'] ) && 'normal' === $this->options[ $theme_name ]['typography']['h1']['letter_spacing'] ? 'selected' : ''; ?> value="normal">normal</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['letter_spacing'] ) && '1px' === $this->options[ $theme_name ]['typography']['h1']['letter_spacing'] ? 'selected' : ''; ?> value="1px">1px</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['letter_spacing'] ) && '2px' === $this->options[ $theme_name ]['typography']['h1']['letter_spacing'] ? 'selected' : ''; ?> value="2px">2px</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['letter_spacing'] ) && '5px' === $this->options[ $theme_name ]['typography']['h1']['letter_spacing'] ? 'selected' : ''; ?> value="5px">5px</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="text-decoration-h1"><?php echo esc_html_e( 'Text Decoration', 'bluedolphin-lms' ); ?></label>
											<select class="form-select" data-style="textDecoration" name="text_decoration_h1" data-target="preview-h1" id="text-decoration-h1">
												<option <?php echo empty( $this->options[ $theme_name ]['typography']['h1']['text_decoration'] ) ? 'selected' : ''; ?> value=""><?php echo esc_html_e( 'Default', 'bluedolphin-lms' ); ?></option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['text_decoration'] ) && 'line-through' === $this->options[ $theme_name ]['typography']['h1']['text_decoration'] ? 'selected' : ''; ?> value="line-through">line-through</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['text_decoration'] ) && 'none' === $this->options[ $theme_name ]['typography']['h1']['text_decoration'] ? 'selected' : ''; ?> value="none">none</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['text_decoration'] ) && 'overline' === $this->options[ $theme_name ]['typography']['h1']['text_decoration'] ? 'selected' : ''; ?> value="overline">overline</option>
												<option <?php echo ! empty( $this->options[ $theme_name ]['typography']['h1']['text_decoration'] ) && 'underline' === $this->options[ $theme_name ]['typography']['h1']['text_decoration'] ? 'selected' : ''; ?> value="underline">underline</option>
											</select>
										</div>
									</li>
								</ul>
							</div>
							<div class="font-preview-screen">
								<div class="title"><?php echo esc_html_e( 'Preview', 'bluedolphin-lms' ); ?></div>
								<div class="preview-text" id="preview-h1">
									<?php echo esc_html_e( 'The quick brown fox jumps over the lazy dog', 'bluedolphin-lms' ); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="tab-pane bdlms-tab-pane" id="heading-2" role="tabpanel" aria-labelledby="heading-2-tab" tabindex="0">
					<div class="tab-content-wrap">
						<div class="tab-title">Heading 2</div>
						<div class="tab-content-row">
							<div class="font-prop-selector">
								<ul>
									<li>
										<div class="bdlms-form-group">
											<label for="font-family-h2">Font Family (Google Fonts)</label>
											<select class="form-select" data-style="fontFamily" data-target="preview-h2" id="font-family-h2">
												<option selected><?php echo esc_html_e( 'Default', 'bluedolphin-lms' ); ?></option>
												<option value="cursive">cursive</option>
												<option value="sans-serif">sans-serif</option>
												<option value="serif">serif</option>
												<option value="system-ui">system-ui</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="font-weight-h2">Font Weight</label>
											<select class="form-select" data-style="fontWeight" data-target="preview-h2" id="font-weight-h2">
												<option selected>Default</option>
												<option value="300">300</option>
												<option value="400">400</option>
												<option value="500">500</option>
												<option value="600">600</option>
												<option value="700">700</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="font-size-h2">Font Size</label>
											<select class="form-select" data-style="fontSize" data-target="preview-h2" id="font-size-h2">
												<option selected>Default</option>
												<option value="16px">16px</option>
												<option value="18px">18px</option>
												<option value="20px">20px</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="text-transform-h2">Text Transform</label>
											<select class="form-select" data-style="textTransform" data-target="preview-h2" id="text-transform-h2">
												<option selected>Default</option>
												<option value="none">none</option>
												<option value="capitalize">capitalize</option>
												<option value="uppercase">uppercase</option>
												<option value="lowercase">lowercase</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="line-height-h2">Line Height</label>
											<select class="form-select" data-style="lineHeight" data-target="preview-h2" id="line-height-h2">
												<option selected>Default</option>
												<option value="normal">normal</option>
												<option value="1">1</option>
												<option value="1.2">1.2</option>
												<option value="1.5">1.5</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="letter-spacing-h2">Letter Spacing</label>
											<select class="form-select" data-style="letterSpacing" data-target="preview-h2" id="letter-spacing-h2">
												<option selected>Default</option>
												<option value="normal">normal</option>
												<option value="1px">1px</option>
												<option value="2px">2px</option>
												<option value="5px">5px</option>
											</select>
										</div>
									</li>
									<li>
										<div class="bdlms-form-group">
											<label for="text-decoration-h2">Text Decoration</label>
											<select class="form-select" data-style="textDecoration" data-target="preview-h2" id="text-decoration-h2">
												<option selected>Default</option>
												<option value="none">none</option>
												<option value="line-through">line-through</option>
												<option value="overline">overline</option>
												<option value="underline">underline</option>
											</select>
										</div>
									</li>
								</ul>
							</div>
							<div class="font-preview-screen">
								<div class="title"><?php echo esc_html_e( 'Preview', 'bluedolphin-lms' ); ?></div>
								<div class="preview-text" id="preview-h2">
									<?php echo esc_html_e( 'The quick brown fox jumps over the lazy dog', 'bluedolphin-lms' ); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="bdlms-theme-update-wrap">
		<div class="btn-wrap">
			<button type="submit" name="reset" class="button button-outline"><?php echo esc_html_e( 'Reset', 'bluedolphin-lms' ); ?></button>
			<input type="submit" class="button button-primary" name="submit" value="<?php echo esc_html_e( 'Save & Update', 'bluedolphin-lms' ); ?>" />
		</div>
	</div>
</form>
