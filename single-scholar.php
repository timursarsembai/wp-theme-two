<?php
/**
 * Scholar single post template
 */

get_header(); ?>

<main class="site-content container">
	<article class="scholar-post">
		<?php
		while ( have_posts() ) :
			the_post();

			try {
				islamic_scholars_breadcrumb();

				$birth_year = intval( get_post_meta( get_the_ID(), 'birth_year', true ) );
				$death_year = intval( get_post_meta( get_the_ID(), 'death_year', true ) );
				$full_name = get_post_meta( get_the_ID(), 'full_name', true );
				$teachers = (array) get_post_meta( get_the_ID(), 'teachers', true );
				
				// Debug logging
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					error_log( 'Scholar loaded: ' . get_the_ID() . ', birth: ' . $birth_year . ', death: ' . $death_year );
					error_log( 'Current locale: ' . get_locale() );
					error_log( 'Textdomain test: ' . __( 'Century: %s', 'islamic-scholars' ) );
				}
				?>

			<header class="entry-header" style="display: grid; grid-template-columns: auto 1fr; gap: var(--spacing-lg); align-items: start; margin-bottom: var(--spacing-2xl);">
				<?php if ( has_post_thumbnail() ) : ?>
					<div style="max-width: 200px;">
						<?php the_post_thumbnail( 'medium', array( 
							'style' => 'width: 100%; height: auto; border-radius: 8px;',
							'fetchpriority' => 'high',
							'decoding' => 'async',
						) ); ?>
					</div>
				<?php endif; ?>

				<div>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					
					<?php if ( $full_name ) : ?>
						<p style="color: var(--color-text-light); font-style: italic;">
							<?php echo sprintf( __( 'Full name: %s', 'islamic-scholars' ), '<strong>' . esc_html( $full_name ) . '</strong>' ); ?>
						</p>
					<?php endif; ?>

					<?php if ( $birth_year && $death_year ) : ?>
						<p class="scholar-dates" style="font-size: var(--fs-lg); font-weight: 600; color: var(--color-primary-dark);">
							<?php printf(
								__( '%d–%d AH', 'islamic-scholars' ),
								$birth_year,
								$death_year
							); ?>
						</p>
					<?php endif; ?>

					<!-- Century -->
					<?php
					$centuries = wp_get_post_terms( get_the_ID(), 'centuries' );
					if ( ! is_wp_error( $centuries ) && $centuries ) :
						?>
						<p class="scholar-century">
							<strong><?php _e( 'Century:', 'islamic-scholars' ); ?></strong>
							<?php
							$century_links = array_map( function( $term ) {
								return '<a href="' . get_term_link( $term ) . '">' . $term->name . '</a>';
							}, $centuries );
							echo implode( ', ', $century_links );
							?>
						</p>
					<?php endif; ?>
				</div>
			</header>

			<!-- Teachers -->
			<?php if ( ! empty( $teachers ) ) : ?>
				<div class="scholar-teachers card" style="margin-bottom: var(--spacing-2xl); border-left: 4px solid var(--color-accent-secondary);">
					<h3><?php _e( 'Teachers (Mentors)', 'islamic-scholars' ); ?></h3>
					<ul style="list-style: none; display: flex; flex-wrap: wrap; gap: var(--spacing-md);">
						<?php
						foreach ( $teachers as $teacher_id ) :
							$teacher = get_post( $teacher_id );
							if ( $teacher && isset( $teacher->post_type ) && $teacher->post_type === 'scholar' ) :
								?>
								<li>
									<a href="<?php echo esc_url( get_permalink( $teacher_id ) ); ?>" style="display: inline-block; padding: var(--spacing-sm) var(--spacing-md); background-color: rgba(55, 48, 163, 0.1); border-radius: 4px; color: var(--color-accent-secondary);">
										<?php echo esc_html( $teacher->post_title ); ?>
									</a>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<!-- Students (scholars who list this scholar as teacher) -->
			<?php
			$students = get_scholar_students( get_the_ID() );
			if ( ! empty( $students ) ) :
				?>
				<div class="scholar-students card" style="margin-bottom: var(--spacing-2xl); border-left: 4px solid var(--color-accent);">
					<h3><?php _e( 'Known Students', 'islamic-scholars' ); ?></h3>
					<ul style="list-style: none; display: flex; flex-wrap: wrap; gap: var(--spacing-md);">
						<?php foreach ( $students as $student_id ) : ?>
							<li>
								<a href="<?php echo esc_url( get_permalink( $student_id ) ); ?>" style="display: inline-block; padding: var(--spacing-sm) var(--spacing-md); background-color: rgba(192, 132, 0, 0.1); border-radius: 4px; color: var(--color-accent);">
									<?php echo esc_html( get_the_title( $student_id ) ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<!-- Biography -->
			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<!-- Translations by this scholar -->
			<?php
			$translations = get_posts( array(
				'post_type' => 'post',
				'meta_query' => array(
					array(
						'key' => 'scholar_id',
						'value' => get_the_ID(),
						'compare' => '=',
					),
				),
				'posts_per_page' => -1,
			) );

			if ( ! empty( $translations ) ) :
				?>
				<div style="margin-top: var(--spacing-2xl); padding-top: var(--spacing-2xl); border-top: 1px solid var(--color-border);">
					
					<h3><?php printf( __( '%s — his works', 'islamic-scholars' ), get_the_title() ); ?></h3>
					<div class="scholar-works" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: var(--spacing-lg); margin-bottom: var(--spacing-2xl);">
						<?php foreach ( $translations as $post ) : setup_postdata( $post ); ?>
							<div class="card">
								<?php if ( has_post_thumbnail() ) : ?>
									<a href="<?php the_permalink(); ?>" class="post-thumbnail">
										<?php the_post_thumbnail( 'medium', array( 'style' => 'width: 100%; height: auto; border-radius: 4px; display: block; margin-bottom: var(--spacing-md);' ) ); ?>
									</a>
								<?php endif; ?>
								<h4 class="card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
								<p class="card-meta">
									<?php echo esc_html( get_the_excerpt() ); ?>
								</p>
							</div>
						<?php endforeach; wp_reset_postdata(); ?>
					</div>

				</div>
			<?php endif; ?>

			<?php
			} catch ( Throwable $e ) {
				echo '<div style="background: #fee; padding: 20px; border: 2px solid red; margin: 20px 0; border-radius: 4px;">';
				echo '<h2 style="color: red; margin-top: 0;">⚠️ Error Loading Scholar</h2>';
				echo '<p><strong>Error:</strong> ' . esc_html( $e->getMessage() ) . '</p>';
				echo '<p><strong>File:</strong> ' . esc_html( $e->getFile() ) . ':' . $e->getLine() . '</p>';
				echo '<pre style="background: white; padding: 10px; border: 1px solid #ccc; overflow: auto; font-size: 11px;">' . esc_html( $e->getTraceAsString() ) . '</pre>';
				echo '</div>';
			}
			?>

		<?php endwhile; ?>
	</article>
</main>

<?php get_footer();
