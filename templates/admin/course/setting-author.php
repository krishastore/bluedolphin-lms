<?php
/**
 * Template: Course setting - Author.
 *
 * @package ST\Lms
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$signature_text  = ! empty( $signature['text'] ) ? $signature['text'] : '';
$image_id        = ! empty( $signature['image_id'] ) ? $signature['image_id'] : 0;
$has_certificate = ! empty( $signature['certificate'] ) ? $signature['certificate'] : 0;
$active_class    = '';
?>
<div class="stlms-tab-content<?php echo esc_attr( $active_class ); ?>" data-tab="author">
	<div class="stlms-cs-row">
		<div class="stlms-cs-col-left"><?php esc_html_e( 'Author', 'skilltriks' ); ?></div>
		<div class="stlms-cs-col-right">
			<div class="stlms-cs-drag-list">
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
	<div class="stlms-author-box">
		<div class="stlms-media-choose">
			<label>
				<?php esc_html_e( 'Authorised Signatory', 'skilltriks' ); ?>
			</label>
		</div>
		<div class="stlms-media-choose show-certificate">
			<label>
				<?php esc_html_e( 'Show Course Certificate', 'skilltriks' ); ?>
			</label>
			<div>
				<label><input type="checkbox" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[signature][certificate]" value="1" <?php checked( $has_certificate, 1 ); ?>><?php esc_html_e( 'Yes', 'skilltriks' ); ?></label>
			</div>
		</div>
		<div class="stlms-media-choose">
			<label>
				<?php esc_html_e( 'Choose Signature File', 'skilltriks' ); ?>
			</label>
			<div class="stlms-media-file">
			<?php
			if ( $image_id ) :
				$image_url = wp_get_attachment_url( $image_id );
				?>
				<a href="javascript:;" class="stlms-open-media button" data-library_type="image" data-ext="<?php echo esc_attr( apply_filters( 'stlms_lesson_allowed_video_types', 'png,jpeg,jpg' ) ); ?>"><?php esc_html_e( 'Change File', 'skilltriks' ); ?></a>
				<span class="stlms-media-name"><a href="<?php echo esc_url( $image_url ); ?>" target="_blank"><?php echo esc_html( basename( $image_url ) ); ?></a></span>
			<?php else : ?>
				<a href="javascript:;" class="stlms-open-media button" data-library_type="image" data-ext="<?php echo esc_attr( apply_filters( 'stlms_lesson_allowed_video_types', 'png,jpeg,jpg' ) ); ?>"><?php esc_html_e( 'Choose File', 'skilltriks' ); ?></a>
				<span class="stlms-media-name"><?php esc_html_e( 'No File Chosen', 'skilltriks' ); ?></span>
			<?php endif; ?>
				<input type="hidden" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[signature][image_id]" value="<?php echo (int) $image_id; ?>">
			</div>
		</div>
		<div class="stlms-media-choose">
			<label></label>
			<span>
				<?php esc_html_e( 'Recommended size: 220px by 80px. Accepted file format: jpeg or png.', 'skilltriks' ); ?>
			</span>
		</div>
		<div class="stlms-or">
			<span><?php esc_html_e( 'OR', 'skilltriks' ); ?></span>
		</div>
		<div class="stlms-media-choose">
			<label>
				<?php esc_html_e( 'Add text as signature', 'skilltriks' ); ?>
			</label>
			<input type="text" name="<?php echo esc_attr( $this->meta_key_prefix ); ?>[signature][text]" placeholder="<?php esc_attr_e( 'Eg. John Doe', 'skilltriks' ); ?>" value="<?php echo esc_html( $signature_text ); ?>">
		</div>
	</div>
</div>
