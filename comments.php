<?php
/**
 * Comments template
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area" style="margin-top: var(--spacing-2xl); padding-top: var(--spacing-2xl); border-top: 1px solid var(--color-border);">
	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			echo esc_html( sprintf(
				_n( '%d comment', '%d comments', $comment_count, 'islamic-scholars' ),
				$comment_count
			) );
			?>
		</h3>

		<ol class="comment-list" style="list-style: none; margin: var(--spacing-lg) 0;">
			<?php
			wp_list_comments( array(
				'style' => 'ol',
				'short_ping' => true,
				'avatar_size' => 48,
				'reply_text' => __( 'Reply', 'islamic-scholars' ),
			) );
			?>
		</ol>

		<?php
		the_comments_pagination( array(
			'prev_text' => __( '← Older Comments', 'islamic-scholars' ),
			'next_text' => __( 'Newer Comments →', 'islamic-scholars' ),
		) );
		?>
	<?php endif; ?>

	<?php
	if ( comments_open() ) :
		comment_form( array(
			'class_form' => 'comment-form',
			'title_reply' => __( 'Leave a Comment', 'islamic-scholars' ),
			'label_submit' => __( 'Post Comment', 'islamic-scholars' ),
			'comment_field' => '<p class="comment-form-comment"><label for="comment">' . __( 'Comment', 'islamic-scholars' ) . '</label><div id="comment-editor-wrapper"></div><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required style="display:none;"></textarea></p>',
		) );
	endif;
	?>

	<!-- Quill.js for comments -->
	<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
	<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
	
	<style>
		#comment-editor-wrapper {
			background: #fff;
			border-radius: 4px;
			margin-bottom: var(--spacing-md);
		}
		#comment-editor-wrapper .ql-toolbar {
			border-radius: 4px 4px 0 0;
			background: #f9f9f9;
		}
		#comment-editor-wrapper .ql-container {
			border-radius: 0 0 4px 4px;
			font-size: 16px;
			min-height: 150px;
			font-family: 'Merriweather', serif;
		}
		#comment-editor-wrapper .ql-editor {
			min-height: 150px;
		}
		#comment-editor-wrapper .ql-editor.ql-blank::before {
			font-style: normal;
			color: #999;
		}
	</style>
	
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		const editorWrapper = document.getElementById('comment-editor-wrapper');
		const commentTextarea = document.getElementById('comment');
		const commentForm = document.querySelector('.comment-form');
		
		if (!editorWrapper || !commentTextarea) return;
		
		// Initialize Quill
		const quill = new Quill('#comment-editor-wrapper', {
			theme: 'snow',
			modules: {
				toolbar: [
					['bold', 'italic', 'underline', 'strike'],
					['blockquote', 'code-block'],
					['link'],
					['clean']
				]
			},
			placeholder: '<?php echo esc_js( __( 'Write your comment...', 'islamic-scholars' ) ); ?>'
		});
		
		// Sync Quill content to hidden textarea before form submit
		if (commentForm) {
			commentForm.addEventListener('submit', function(e) {
				const html = quill.root.innerHTML;
				// Clean empty content
				commentTextarea.value = (html === '<p><br></p>') ? '' : html;
			});
		}
	});
	</script>
</div>
