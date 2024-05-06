<?php
/**
 * Template: Course detail page content.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

?>
<div class="bdlms-lesson-view__body">
	<div class="bdlms-lesson-video-box">
		<video class="lesson-video" controls crossorigin playsinline
			poster="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.jpg">
			<source src="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4"
				type="video/mp4" size="576">
			<source src="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-720p.mp4"
				type="video/mp4" size="720">
			<source src="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-1080p.mp4"
				type="video/mp4" size="1080">

			<track kind="captions" label="English" srclang="en"
				src="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.en.vtt" default>
			<track kind="captions" label="Français" srclang="fr"
				src="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-HD.fr.vtt">
			<a href="https://cdn.plyr.io/static/demo/View_From_A_Blue_Moon_Trailer-576p.mp4"
				download><?php esc_html_e( 'Download', 'bluedolphin-lms' ); ?></a>
		</video>
		<!-- <div id="player" class="lesson-video" data-plyr-provider="youtube" data-plyr-embed-id="bTqVqk7FSmY">
		</div> -->
		<!-- <div id="player" class="lesson-video" data-plyr-provider="vimeo" data-plyr-embed-id="76979871">
		</div> -->
	</div>
</div>
<?php if ( ! empty( $args['course_data']['curriculums'] ) ) : ?>
<div class="bdlms-lesson-sidebar">
	<div class="bdlms-lesson-toggle">
		<svg class="icon" width="20" height="20">
			<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#menu-burger"></use>
		</svg>
		<span><?php esc_html_e( 'Course Content', 'bluedolphin-lms' ); ?></span>
		<svg class="icon-cross" width="20" height="20">
			<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#cross"></use>
		</svg>
	</div>
	<div class="bdlms-lesson-accordion">
		<div class="bdlms-accordion">
			<?php
			foreach ( $args['course_data']['curriculums'] as $item_key => $curriculums ) :
				$items          = empty( $curriculums['items'] ) ? $curriculums['items'] : array();
				$total_duration = \BlueDolphin\Lms\count_duration( $items, \BlueDolphin\Lms\META_KEY_LESSON_SETTINGS );
				$duration_str   = \BlueDolphin\Lms\seconds_to_hours_str( $total_duration );
				?>
				<div class="bdlms-accordion-item" data-expanded="true">
					<div class="bdlms-accordion-header">
						<div class="bdlms-lesson-title">
							<div class="no"><?php echo esc_html( ++$item_key ); ?>.</div>
							<div class="bdlms-lesson-name">
								<div class="name"><?php echo isset( $curriculums['section_name'] ) ? esc_html( $curriculums['section_name'] ) : ''; ?></div>
								<div class="info">
									<span><?php printf( '%d/%d', 1, count( $curriculums['items'] ) ); ?></span>
									<span><?php echo esc_html( $duration_str ); ?></span>
								</div>
							</div>
						</div>
					</div>
					<div class="bdlms-accordion-collapse">
						<div class="bdlms-lesson-list">
							<ul>
								<li>
									<label>
										<input type="checkbox" class="bdlms-check" checked>
										<span class="bdlms-lesson-class">
											<span class="class-name"><span>1.1.</span> What is Sales?</span>
											<span class="class-type">
												<svg class="icon" width="16" height="16">
													<use xlink:href="<?php echo esc_url( BDLMS_ASSETS ); ?>/images/sprite-front.svg#video">
													</use>
												</svg>
												30 mins
											</span>
										</span>
									</label>
								</li>
							</ul>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>
	<?php
endif;
