<?php
/**
 * Template: Course setting - Author.
 *
 * @package BlueDolphin\Lms
 */

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
									'capability'       => array( $post_type_object->cap->edit_posts ),
									'name'             => 'post_author_override',
									'selected'         => empty( $post->ID ) ? $user_ID : $post->post_author,
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
</div>