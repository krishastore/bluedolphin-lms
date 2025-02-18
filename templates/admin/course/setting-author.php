<?php
/**
 * Template: Course setting - Author.
 *
 * @package BD\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$signature_text  = ! empty( $signature['text'] ) ? $signature['text'] : '';
$image_id        = ! empty( $signature['image_id'] ) ? $signature['image_id'] : 0;
$has_certificate = ! empty( $signature['certificate'] ) ? $signature['certificate'] : 0;
$active_class    = '';
?>
<div class="bdlms-tab-content<?php echo esc_attr( $active_class ); ?>" data-tab="author">
	<div class="bdlms-cs-row">
		<div class="bdlms-cs-col-left"><?php esc_html_e( 'Author', 'bluedolphin-lms' ); ?></div>
		<div class="bdlms-cs-col-right">
			<div class="bdlms-cs-drag-list">
				<ul class="cs-drag-list">
					<li>
						<?php
							wp_dropdown_users(
								array(
									'capability'       => array( $post_type_object->cap->edit_posts ), // @phpstan-ignore variable.undefined
									'name'             => 'post_author_override',
									'selected'         => empty( $post->ID ) ? $user_ID : $post->post_author, // @phpstan-ignore-line
									'include_selected' => true,
									'show'             => 'display_name_with_login',
								)
							);
							?>
					</li>
				</ul>
			</div>
		</div>
	</div>
	<div class="bdlms-author-box">
		<div class="bdlms-media-choose">
			<label>
				<?php esc_html_e( 'Authorised Signatory', 'bluedolphin-lms' ); ?>
			</label>
		</div>
		<div class="bdlms-media-choose show-certificate">
			<label>
				<?php esc_html_e( 'Show Course Certificate', 'bluedolphin-lms' ); ?>
			</label>
			<div>
				<label><input type="checkbox" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[signature][certificate]" value="1" <?php checked( $has_certificate, 1 ); ?>><?php esc_html_e( 'Yes', 'bluedolphin-lms' ); ?></label>
			</div>
		</div>
		<div class="bdlms-media-choose">
			<label>
				<?php esc_html_e( 'Choose Signature File', 'bluedolphin-lms' ); ?>
			</label>
			<div class="bdlms-media-file">
			<?php
			if ( $image_id ) :
				$image_url = wp_get_attachment_url( $image_id );
				?>
				<a href="javascript:;" class="bdlms-open-media button" data-library_type="image" data-ext="<?php echo esc_attr( apply_filters( 'bdlms_lesson_allowed_video_types', 'png,jpeg,jpg' ) ); ?>"><?php esc_html_e( 'Change File', 'bluedolphin-lms' ); ?></a>
				<span class="bdlms-media-name"><a href="<?php echo esc_url( $image_url ); ?>" target="_blank"><?php echo esc_html( basename( $image_url ) ); ?></a></span>
			<?php else : ?>
				<a href="javascript:;" class="bdlms-open-media button" data-library_type="image" data-ext="<?php echo esc_attr( apply_filters( 'bdlms_lesson_allowed_video_types', 'png,jpeg,jpg' ) ); ?>"><?php esc_html_e( 'Choose File', 'bluedolphin-lms' ); ?></a>
				<span class="bdlms-media-name"><?php esc_html_e( 'No File Chosen', 'bluedolphin-lms' ); ?></span>
			<?php endif; ?>
				<input type="hidden" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[signature][image_id]" value="<?php echo (int) $image_id; ?>">
			</div>
		</div>
		<div class="bdlms-media-choose">
			<label></label>
			<span>
				<?php esc_html_e( 'Recommended size: 220px by 80px. Accepted file format: jpeg or png.', 'bluedolphin-lms' ); ?>
			</span>
		</div>
		<div class="bdlms-or">
			<span><?php esc_html_e( 'OR', 'bluedolphin-lms' ); ?></span>
		</div>
		<div class="bdlms-media-choose">
			<label>
				<?php esc_html_e( 'Add text as signature', 'bluedolphin-lms' ); ?>
			</label>
			<input type="text" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[signature][text]" placeholder="<?php esc_attr_e( 'Eg. John Doe', 'bluedolphin-lms' ); ?>" value="<?php echo esc_html( $signature_text ); ?>">
		</div>
	</div>
</div>