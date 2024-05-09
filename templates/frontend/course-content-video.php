<?php
/**
 * Template: Course Curriculum - Video.
 *
 * @package BlueDolphin\Lms
 *
 * phpcs:disable WordPress.Security.NonceVerification.Recommended
 */

?>
<div class="bdlms-lesson-view__body">
	<div class="bdlms-lesson-video-box">
		<?php
		if ( ! empty( $args['curriculum']['media']['video_id'] ) ) :
			$video_id   = $args['curriculum']['media']['video_id'];
			$media_url  = wp_get_attachment_url( $video_id );
			$media_data = wp_get_attachment_metadata( $video_id );
			$fileformat = isset( $media_data['fileformat'] ) ? $media_data['fileformat'] : 'mp4';
			$filesize   = isset( $media_data['filesize'] ) ? $media_data['filesize'] : '';
			?>
			<video class="lesson-video" controls crossorigin playsinline>
				<source src="<?php echo esc_url( $media_url ); ?>"
					type="video/<?php echo esc_attr( $fileformat ); ?>" size="<?php echo (int) $filesize; ?>">

				<a href="<?php echo esc_url( $media_url ); ?>"
					download><?php esc_html_e( 'Download', 'bluedolphin-lms' ); ?></a>
			</video>
			<?php
		elseif ( ! empty( $args['curriculum']['media']['embed_video_url'] ) ) :
			$embed_video_url = $args['curriculum']['media']['embed_video_url'];
			$parse_url       = wp_parse_url( $args['curriculum']['media']['embed_video_url'], PHP_URL_HOST );
			if ( str_contains( $parse_url, 'youtube' ) ) :
				$video_id = wp_parse_url( $embed_video_url, PHP_URL_PATH );
				?>
				<div id="player" class="lesson-video" data-plyr-provider="youtube" data-plyr-embed-id="<?php echo esc_html( str_replace( '/embed/', '', $video_id ) ); ?>"></div>
				<?php
			elseif ( str_contains( $parse_url, 'vimeo' ) ) :
				$video_id = wp_parse_url( $embed_video_url, PHP_URL_PATH );
				?>
				<div id="player" class="lesson-video" data-plyr-provider="vimeo" data-plyr-embed-id="<?php echo esc_html( str_replace( '/video/', '', $video_id ) ); ?>"></div>
			<?php else : ?>
				<iframe src="<?php echo esc_url( $args['curriculum']['media']['embed_video_url'] ); ?>" frameborder="0"></iframe>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>