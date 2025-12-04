<?php
/**
 * Custom Meta Boxes and Fields
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register meta boxes for Scholar
 */
function islamic_scholars_register_scholar_metaboxes() {
	add_meta_box(
		'scholar_bio',
		__( 'Scholar Information', 'islamic-scholars' ),
		'islamic_scholars_scholar_bio_callback',
		'scholar',
		'normal',
		'high'
	);

	add_meta_box(
		'scholar_teachers',
		__( 'Teachers (Mentors)', 'islamic-scholars' ),
		'islamic_scholars_scholar_teachers_callback',
		'scholar',
		'normal',
		'high'
	);
	
	add_meta_box(
		'scholar_audio',
		__( 'Audio Lectures', 'islamic-scholars' ),
		'islamic_scholars_scholar_audio_callback',
		'scholar',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'islamic_scholars_register_scholar_metaboxes' );

/**
 * Scholar bio meta box callback
 */
function islamic_scholars_scholar_bio_callback( $post ) {
	wp_nonce_field( 'islamic_scholars_scholar_nonce', 'islamic_scholars_scholar_nonce' );
	
	$birth_year = get_post_meta( $post->ID, 'birth_year', true );
	$death_year = get_post_meta( $post->ID, 'death_year', true );
	$full_name = get_post_meta( $post->ID, 'full_name', true );
	?>
	<div style="padding: 10px 0;">
		<p>
			<label for="full_name"><?php _e( 'Full Name (Arabic)', 'islamic-scholars' ); ?></label><br>
			<input type="text" id="full_name" name="full_name" value="<?php echo esc_attr( $full_name ); ?>" style="width: 100%; padding: 8px;">
		</p>

		<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
			<p>
				<label for="birth_year"><?php _e( 'Birth Year (AH)', 'islamic-scholars' ); ?></label><br>
				<input type="number" id="birth_year" name="birth_year" value="<?php echo esc_attr( $birth_year ); ?>" placeholder="e.g., 689" style="width: 100%; padding: 8px;">
			</p>

			<p>
				<label for="death_year"><?php _e( 'Death Year (AH)', 'islamic-scholars' ); ?></label><br>
				<input type="number" id="death_year" name="death_year" value="<?php echo esc_attr( $death_year ); ?>" placeholder="e.g., 770" style="width: 100%; padding: 8px;">
			</p>
		</div>
		<p style="color: #666; font-size: 12px;">
			<?php _e( 'The century will be automatically assigned based on birth and death years.', 'islamic-scholars' ); ?>
		</p>
	</div>
	<?php
}

/**
 * Scholar teachers meta box callback
 */
function islamic_scholars_scholar_teachers_callback( $post ) {
	wp_nonce_field( 'islamic_scholars_teachers_nonce', 'islamic_scholars_teachers_nonce' );
	
	$teachers = get_post_meta( $post->ID, 'teachers', true );
	if ( ! is_array( $teachers ) ) {
		$teachers = array();
	}

	$all_scholars = get_posts( array(
		'post_type' => 'scholar',
		'posts_per_page' => -1,
		'exclude' => array( $post->ID ),
		'orderby' => 'title',
		'order' => 'ASC',
	) );
	?>
	<div style="padding: 10px 0;">
		<p style="color: #666; font-size: 14px; margin-bottom: 15px;">
			<?php _e( 'Select teachers/mentors of this scholar. Their students will be highlighted in the Chronology page when you select this scholar.', 'islamic-scholars' ); ?>
		</p>
		<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 10px;">
			<?php foreach ( $all_scholars as $scholar ) : ?>
				<label style="display: flex; align-items: center; gap: 8px; padding: 8px; background-color: #f5f5f5; border-radius: 4px; cursor: pointer;">
					<input 
						type="checkbox" 
						name="teachers[]" 
						value="<?php echo $scholar->ID; ?>"
						<?php checked( in_array( $scholar->ID, $teachers ), true ); ?>
					>
					<span><?php echo esc_html( $scholar->post_title ); ?></span>
				</label>
			<?php endforeach; ?>
		</div>
		<?php if ( empty( $all_scholars ) ) : ?>
			<p style="color: #999; font-style: italic;">
				<?php _e( 'No other scholars found. Create other scholars first.', 'islamic-scholars' ); ?>
			</p>
		<?php endif; ?>
	</div>
	<?php
}

/**
 * Scholar audio meta box callback
 */
function islamic_scholars_scholar_audio_callback( $post ) {
	wp_nonce_field( 'islamic_scholars_audio_nonce', 'islamic_scholars_audio_nonce' );
	
	// Enqueue media uploader
	wp_enqueue_media();
	
	$audio_files = get_post_meta( $post->ID, 'audio_files', true );
	if ( ! is_array( $audio_files ) ) {
		$audio_files = array();
	}
	
	$add_audio_label = __( '+ Add Audio', 'islamic-scholars' );
	$remove_label = __( 'Remove', 'islamic-scholars' );
	$title_placeholder = __( 'Audio title...', 'islamic-scholars' );
	$select_audio_label = __( 'Select Audio', 'islamic-scholars' );
	$choose_audio_label = __( 'Choose Audio File', 'islamic-scholars' );
	$use_audio_label = __( 'Use this audio', 'islamic-scholars' );
	?>
	<style>
		.audio-item {
			display: flex;
			gap: 10px;
			align-items: center;
			padding: 10px;
			background: #f9f9f9;
			border: 1px solid #ddd;
			border-radius: 4px;
			margin-bottom: 10px;
		}
		.audio-item input[type="text"] {
			flex: 1;
			padding: 8px;
		}
		.audio-item .audio-url {
			width: 300px;
			padding: 8px;
			background: #fff;
			border: 1px solid #ddd;
			border-radius: 4px;
			font-size: 12px;
			color: #666;
		}
		.audio-item .select-audio-btn {
			white-space: nowrap;
		}
		.audio-item .remove-audio-btn {
			background: #dc3545 !important;
			border-color: #dc3545 !important;
			color: #fff !important;
		}
	</style>
	
	<div id="audio-files-container" style="padding: 10px 0;">
		<p style="color: #666; font-size: 14px; margin-bottom: 15px;">
			<?php _e( 'Add audio lectures or recordings by this scholar.', 'islamic-scholars' ); ?>
		</p>
		
		<div id="audio-files-wrapper">
			<?php foreach ( $audio_files as $index => $audio ) : ?>
				<div class="audio-item" data-index="<?php echo $index; ?>">
					<input type="text" name="audio_files[<?php echo $index; ?>][title]" value="<?php echo esc_attr( $audio['title'] ?? '' ); ?>" placeholder="<?php echo esc_attr( $title_placeholder ); ?>">
					<input type="text" class="audio-url" name="audio_files[<?php echo $index; ?>][url]" value="<?php echo esc_url( $audio['url'] ?? '' ); ?>" readonly>
					<button type="button" class="button select-audio-btn"><?php echo esc_html( $select_audio_label ); ?></button>
					<button type="button" class="button remove-audio-btn"><?php echo esc_html( $remove_label ); ?></button>
				</div>
			<?php endforeach; ?>
		</div>
		
		<button type="button" id="add-audio-btn" class="button button-primary" style="margin-top: 10px;">
			<?php echo esc_html( $add_audio_label ); ?>
		</button>
	</div>
	
	<script>
	jQuery(document).ready(function($) {
		const wrapper = $('#audio-files-wrapper');
		const addBtn = $('#add-audio-btn');
		
		const titlePlaceholder = <?php echo json_encode( $title_placeholder ); ?>;
		const selectAudioLabel = <?php echo json_encode( $select_audio_label ); ?>;
		const removeLabel = <?php echo json_encode( $remove_label ); ?>;
		const chooseAudioLabel = <?php echo json_encode( $choose_audio_label ); ?>;
		const useAudioLabel = <?php echo json_encode( $use_audio_label ); ?>;
		
		function getNextIndex() {
			let maxIndex = -1;
			wrapper.find('.audio-item').each(function() {
				const idx = parseInt($(this).data('index')) || 0;
				if (idx > maxIndex) maxIndex = idx;
			});
			return maxIndex + 1;
		}
		
		// Add new audio item
		addBtn.on('click', function() {
			const index = getNextIndex();
			const html = `
				<div class="audio-item" data-index="${index}">
					<input type="text" name="audio_files[${index}][title]" placeholder="${titlePlaceholder}">
					<input type="text" class="audio-url" name="audio_files[${index}][url]" readonly>
					<button type="button" class="button select-audio-btn">${selectAudioLabel}</button>
					<button type="button" class="button remove-audio-btn">${removeLabel}</button>
				</div>
			`;
			wrapper.append(html);
		});
		
		// Remove audio item
		wrapper.on('click', '.remove-audio-btn', function() {
			$(this).closest('.audio-item').remove();
		});
		
		// Select audio file
		wrapper.on('click', '.select-audio-btn', function() {
			const item = $(this).closest('.audio-item');
			const urlInput = item.find('.audio-url');
			const titleInput = item.find('input[type="text"]:first');
			
			const frame = wp.media({
				title: chooseAudioLabel,
				button: { text: useAudioLabel },
				library: { type: 'audio' },
				multiple: false
			});
			
			frame.on('select', function() {
				const attachment = frame.state().get('selection').first().toJSON();
				urlInput.val(attachment.url);
				if (!titleInput.val()) {
					titleInput.val(attachment.title || attachment.filename);
				}
			});
			
			frame.open();
		});
	});
	</script>
	<?php
}

/**
 * Save scholar meta
 */
function islamic_scholars_save_scholar_meta( $post_id ) {
	if ( get_post_type( $post_id ) !== 'scholar' ) {
		return;
	}

	// Prevent infinite loops
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	// Verify nonce
	if ( ! isset( $_POST['islamic_scholars_scholar_nonce'] ) || 
		 ! wp_verify_nonce( $_POST['islamic_scholars_scholar_nonce'], 'islamic_scholars_scholar_nonce' ) ) {
		return;
	}

	// Save bio fields
	if ( isset( $_POST['full_name'] ) ) {
		update_post_meta( $post_id, 'full_name', sanitize_text_field( $_POST['full_name'] ) );
	}
	if ( isset( $_POST['birth_year'] ) ) {
		update_post_meta( $post_id, 'birth_year', intval( $_POST['birth_year'] ) );
	}
	if ( isset( $_POST['death_year'] ) ) {
		update_post_meta( $post_id, 'death_year', intval( $_POST['death_year'] ) );
	}

	// Save teachers
	if ( isset( $_POST['islamic_scholars_teachers_nonce'] ) &&
		 wp_verify_nonce( $_POST['islamic_scholars_teachers_nonce'], 'islamic_scholars_teachers_nonce' ) ) {
		$teachers = isset( $_POST['teachers'] ) ? array_map( 'intval', $_POST['teachers'] ) : array();
		update_post_meta( $post_id, 'teachers', $teachers );
	}
	
	// Save audio files
	if ( isset( $_POST['islamic_scholars_audio_nonce'] ) &&
		 wp_verify_nonce( $_POST['islamic_scholars_audio_nonce'], 'islamic_scholars_audio_nonce' ) ) {
		$audio_files = array();
		if ( isset( $_POST['audio_files'] ) && is_array( $_POST['audio_files'] ) ) {
			foreach ( $_POST['audio_files'] as $audio ) {
				if ( ! empty( $audio['url'] ) ) {
					$audio_files[] = array(
						'title' => sanitize_text_field( $audio['title'] ?? '' ),
						'url' => esc_url_raw( $audio['url'] ),
					);
				}
			}
		}
		update_post_meta( $post_id, 'audio_files', $audio_files );
	}
}
add_action( 'save_post', 'islamic_scholars_save_scholar_meta' );

/**
 * Register meta box for Translation pairs
 * Now used only for 'post'
 */
function islamic_scholars_register_translation_metabox() {
	// Register only for post type
	add_meta_box(
		'translation_pairs',
		__( 'Original & Translation Pairs', 'islamic-scholars' ),
		'islamic_scholars_translation_pairs_callback',
		'post',
		'normal',
		'high'
	);

	add_meta_box(
		'translation_metadata',
		__( 'Translation Metadata', 'islamic-scholars' ),
		'islamic_scholars_translation_metadata_callback',
		'post',
		'normal',
		'high'
	);
}
add_action( 'add_meta_boxes', 'islamic_scholars_register_translation_metabox' );

/**
 * Translation pairs meta box callback
 */
function islamic_scholars_translation_pairs_callback( $post ) {
	wp_nonce_field( 'islamic_scholars_translation_nonce', 'islamic_scholars_translation_nonce' );
	
	$pairs = get_post_meta( $post->ID, 'translation_pairs', true );
	if ( ! is_array( $pairs ) ) {
		$pairs = array();
	}
	
	// Get translated string for JavaScript
	$pair_label = __( 'Pair', 'islamic-scholars' );
	$footnotes_label = __( 'Footnotes', 'islamic-scholars' );
	$add_footnotes_label = __( '+ Add Footnotes', 'islamic-scholars' );
	$remove_footnotes_label = __( 'Remove Footnotes', 'islamic-scholars' );
	$footnote_original_label = __( 'Footnote (Original)', 'islamic-scholars' );
	$footnote_translation_label = __( 'Footnote (Translation)', 'islamic-scholars' );
	?>
	<!-- Quill.js CDN -->
	<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
	<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
	
	<style>
		#pairs-wrapper .translation-pair-row {
			transition: box-shadow 0.2s ease, background-color 0.2s ease;
			position: relative;
		}
		#pairs-wrapper .translation-pair-row:hover {
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
		#pairs-wrapper .translation-pair-row.dragging {
			opacity: 0.4;
			background-color: #e0e0e0;
		}
		#pairs-wrapper .translation-pair-row.drag-over-top::before {
			content: '';
			position: absolute;
			top: -12px;
			left: 0;
			right: 0;
			height: 4px;
			background: #0073aa;
			border-radius: 2px;
		}
		#pairs-wrapper .translation-pair-row.drag-over-bottom::after {
			content: '';
			position: absolute;
			bottom: -12px;
			left: 0;
			right: 0;
			height: 4px;
			background: #0073aa;
			border-radius: 2px;
		}
		.drag-handle {
			cursor: grab;
			padding: 5px 10px;
			color: #999;
			font-size: 18px;
			user-select: none;
		}
		.drag-handle:active {
			cursor: grabbing;
		}
		.move-btn {
			background: none;
			border: 1px solid #ddd;
			padding: 4px 8px;
			cursor: pointer;
			border-radius: 3px;
			font-size: 14px;
			transition: all 0.2s;
		}
		.move-btn:hover {
			background: #0073aa;
			color: #fff;
			border-color: #0073aa;
		}
		.move-btn:disabled {
			opacity: 0.3;
			cursor: not-allowed;
		}
		.move-btn:disabled:hover {
			background: none;
			color: inherit;
			border-color: #ddd;
		}
		/* Quill editor styles */
		.quill-wrapper {
			background: #fff;
			border-radius: 4px;
		}
		.quill-wrapper .ql-toolbar {
			border-radius: 4px 4px 0 0;
			background: #f9f9f9;
		}
		.quill-wrapper .ql-container {
			border-radius: 0 0 4px 4px;
			font-size: 16px;
			min-height: 120px;
		}
		.quill-wrapper .ql-editor {
			min-height: 120px;
		}
		.quill-wrapper.arabic-editor .ql-editor {
			font-family: 'Amiri', serif;
			direction: rtl;
			text-align: right;
		}
		.quill-wrapper.translation-editor .ql-editor {
			font-family: 'Merriweather', serif;
		}
		/* Footnotes styles */
		.footnotes-section {
			margin-top: 15px;
			padding: 15px;
			background-color: #fff8e1;
			border: 1px dashed #ffc107;
			border-radius: 4px;
		}
		.footnotes-section .quill-wrapper .ql-container {
			min-height: 80px;
		}
		.footnotes-section .quill-wrapper .ql-editor {
			min-height: 80px;
		}
		.add-footnotes-btn {
			margin-top: 10px;
			background: #ffc107 !important;
			border-color: #ffc107 !important;
			color: #333 !important;
		}
		.add-footnotes-btn:hover {
			background: #ffb300 !important;
			border-color: #ffb300 !important;
		}
		.remove-footnotes-btn {
			background: #fff !important;
			border-color: #ffc107 !important;
			color: #333 !important;
		}
	</style>
	
	<div id="translation-pairs-container" style="padding: 10px 0;">
		<p style="color: #666; font-size: 14px; margin-bottom: 15px;">
			<?php _e( 'Add pairs of original (Arabic) text and their translations. Each pair will be displayed side-by-side on the front-end (desktop) or stacked (mobile).', 'islamic-scholars' ); ?>
			<br><strong><?php _e( 'Use arrows or drag to reorder pairs.', 'islamic-scholars' ); ?></strong>
		</p>
		
		<div id="pairs-wrapper">
			<?php foreach ( $pairs as $index => $pair ) : 
				$has_footnotes = ! empty( $pair['footnote_original'] ) || ! empty( $pair['footnote_translation'] );
			?>
				<div class="translation-pair-row" draggable="true" data-index="<?php echo $index; ?>" style="margin-bottom: 20px; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
					<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
						<div style="display: flex; align-items: center; gap: 10px;">
							<span class="drag-handle" title="<?php esc_attr_e( 'Drag to reorder', 'islamic-scholars' ); ?>">☰</span>
							<strong class="pair-number"><?php echo esc_html( $pair_label . ' ' . ( $index + 1 ) ); ?></strong>
						</div>
						<div style="display: flex; align-items: center; gap: 5px;">
							<button type="button" class="move-btn move-up" title="<?php esc_attr_e( 'Move up', 'islamic-scholars' ); ?>">↑</button>
							<button type="button" class="move-btn move-down" title="<?php esc_attr_e( 'Move down', 'islamic-scholars' ); ?>">↓</button>
							<button type="button" class="button remove-pair" style="background-color: #dc3545; color: #fff; margin-left: 10px;"><?php _e( 'Remove', 'islamic-scholars' ); ?></button>
						</div>
					</div>
					
					<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
						<div>
							<label style="display: block; margin-bottom: 5px;"><?php _e( 'Original (Arabic)', 'islamic-scholars' ); ?></label>
							<div class="quill-wrapper arabic-editor">
								<div class="quill-editor" data-type="original"><?php echo wp_kses_post( $pair['original'] ?? '' ); ?></div>
							</div>
						</div>

						<div>
							<label style="display: block; margin-bottom: 5px;"><?php _e( 'Translation', 'islamic-scholars' ); ?></label>
							<div class="quill-wrapper translation-editor">
								<div class="quill-editor" data-type="translation"><?php echo wp_kses_post( $pair['translation'] ?? '' ); ?></div>
							</div>
						</div>
					</div>
					
					<!-- Footnotes toggle button -->
					<button type="button" class="button add-footnotes-btn" <?php echo $has_footnotes ? 'style="display:none;"' : ''; ?>>
						<?php echo esc_html( $add_footnotes_label ); ?>
					</button>
					
					<!-- Footnotes section -->
					<div class="footnotes-section" <?php echo ! $has_footnotes ? 'style="display:none;"' : ''; ?>>
						<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
							<strong>📝 <?php echo esc_html( $footnotes_label ); ?></strong>
							<button type="button" class="button remove-footnotes-btn"><?php echo esc_html( $remove_footnotes_label ); ?></button>
						</div>
						<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
							<div>
								<label style="display: block; margin-bottom: 5px;"><?php echo esc_html( $footnote_original_label ); ?></label>
								<div class="quill-wrapper arabic-editor">
									<div class="quill-editor" data-type="footnote_original"><?php echo wp_kses_post( $pair['footnote_original'] ?? '' ); ?></div>
								</div>
							</div>
							<div>
								<label style="display: block; margin-bottom: 5px;"><?php echo esc_html( $footnote_translation_label ); ?></label>
								<div class="quill-wrapper translation-editor">
									<div class="quill-editor" data-type="footnote_translation"><?php echo wp_kses_post( $pair['footnote_translation'] ?? '' ); ?></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<!-- Hidden inputs will be populated on form submit -->
		<div id="pairs-hidden-inputs"></div>

		<button type="button" id="add-pair-btn" class="button button-primary" style="margin-top: 15px;">
			<?php _e( '+ Add New Pair', 'islamic-scholars' ); ?>
		</button>
	</div>

	<script>
	document.addEventListener( 'DOMContentLoaded', function() {
		const wrapper = document.getElementById( 'pairs-wrapper' );
		const hiddenInputs = document.getElementById( 'pairs-hidden-inputs' );
		const pairLabel = <?php echo json_encode( $pair_label ); ?>;
		const footnotesLabel = <?php echo json_encode( $footnotes_label ); ?>;
		const addFootnotesLabel = <?php echo json_encode( $add_footnotes_label ); ?>;
		const removeFootnotesLabel = <?php echo json_encode( $remove_footnotes_label ); ?>;
		const footnoteOriginalLabel = <?php echo json_encode( $footnote_original_label ); ?>;
		const footnoteTranslationLabel = <?php echo json_encode( $footnote_translation_label ); ?>;
		let draggedItem = null;
		
		// Quill toolbar options
		const toolbarOptions = [
			['bold', 'italic', 'underline', 'strike'],
			['link'],
			[{ 'direction': 'rtl' }],
			['clean']
		];

		// Initialize Quill editor
		function initQuillEditor( container ) {
			const editorEl = container.querySelector( '.quill-editor' );
			if ( editorEl.quillInstance ) return editorEl.quillInstance;
			
			const isArabic = container.classList.contains( 'arabic-editor' );
			
			const quill = new Quill( editorEl, {
				theme: 'snow',
				modules: {
					toolbar: toolbarOptions
				},
				placeholder: isArabic ? '<?php echo esc_js( __( 'Enter Arabic text...', 'islamic-scholars' ) ); ?>' : '<?php echo esc_js( __( 'Enter translation...', 'islamic-scholars' ) ); ?>'
			});
			
			editorEl.quillInstance = quill;
			return quill;
		}

		// Initialize all Quill editors in a row
		function initQuillEditors( row ) {
			row.querySelectorAll( '.quill-wrapper' ).forEach( initQuillEditor );
		}

		// Update hidden inputs before form submit
		function updateHiddenInputs() {
			hiddenInputs.innerHTML = '';
			const rows = wrapper.querySelectorAll( '.translation-pair-row' );
			rows.forEach( ( row, index ) => {
				const originalEditor = row.querySelector( '.quill-editor[data-type="original"]' );
				const translationEditor = row.querySelector( '.quill-editor[data-type="translation"]' );
				const footnoteOrigEditor = row.querySelector( '.quill-editor[data-type="footnote_original"]' );
				const footnoteTransEditor = row.querySelector( '.quill-editor[data-type="footnote_translation"]' );
				
				const original = originalEditor.quillInstance ? originalEditor.quillInstance.root.innerHTML : '';
				const translation = translationEditor.quillInstance ? translationEditor.quillInstance.root.innerHTML : '';
				const footnoteOrig = footnoteOrigEditor && footnoteOrigEditor.quillInstance ? footnoteOrigEditor.quillInstance.root.innerHTML : '';
				const footnoteTrans = footnoteTransEditor && footnoteTransEditor.quillInstance ? footnoteTransEditor.quillInstance.root.innerHTML : '';
				
				// Clean empty paragraphs
				const cleanHtml = ( html ) => {
					return html === '<p><br></p>' ? '' : html;
				};
				
				const inputOrig = document.createElement( 'input' );
				inputOrig.type = 'hidden';
				inputOrig.name = 'translation_pairs[' + index + '][original]';
				inputOrig.value = cleanHtml( original );
				
				const inputTrans = document.createElement( 'input' );
				inputTrans.type = 'hidden';
				inputTrans.name = 'translation_pairs[' + index + '][translation]';
				inputTrans.value = cleanHtml( translation );
				
				const inputFootnoteOrig = document.createElement( 'input' );
				inputFootnoteOrig.type = 'hidden';
				inputFootnoteOrig.name = 'translation_pairs[' + index + '][footnote_original]';
				inputFootnoteOrig.value = cleanHtml( footnoteOrig );
				
				const inputFootnoteTrans = document.createElement( 'input' );
				inputFootnoteTrans.type = 'hidden';
				inputFootnoteTrans.name = 'translation_pairs[' + index + '][footnote_translation]';
				inputFootnoteTrans.value = cleanHtml( footnoteTrans );
				
				hiddenInputs.appendChild( inputOrig );
				hiddenInputs.appendChild( inputTrans );
				hiddenInputs.appendChild( inputFootnoteOrig );
				hiddenInputs.appendChild( inputFootnoteTrans );
			});
		}

		// Update on form submit
		document.querySelector( 'form#post' ).addEventListener( 'submit', updateHiddenInputs );

		// Add new pair
		document.getElementById( 'add-pair-btn' ).addEventListener( 'click', function() {
			const count = wrapper.querySelectorAll( '.translation-pair-row' ).length;
			const newPair = document.createElement( 'div' );
			newPair.className = 'translation-pair-row';
			newPair.draggable = true;
			newPair.style.cssText = 'margin-bottom: 20px; padding: 15px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;';
			newPair.innerHTML = `
				<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
					<div style="display: flex; align-items: center; gap: 10px;">
						<span class="drag-handle" title="<?php esc_attr_e( 'Drag to reorder', 'islamic-scholars' ); ?>">☰</span>
						<strong class="pair-number">${pairLabel} ${count + 1}</strong>
					</div>
					<div style="display: flex; align-items: center; gap: 5px;">
						<button type="button" class="move-btn move-up" title="<?php esc_attr_e( 'Move up', 'islamic-scholars' ); ?>">↑</button>
						<button type="button" class="move-btn move-down" title="<?php esc_attr_e( 'Move down', 'islamic-scholars' ); ?>">↓</button>
						<button type="button" class="button remove-pair" style="background-color: #dc3545; color: #fff; margin-left: 10px;"><?php _e( 'Remove', 'islamic-scholars' ); ?></button>
					</div>
				</div>
				<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
					<div>
						<label style="display: block; margin-bottom: 5px;"><?php _e( 'Original (Arabic)', 'islamic-scholars' ); ?></label>
						<div class="quill-wrapper arabic-editor">
							<div class="quill-editor" data-type="original"></div>
						</div>
					</div>
					<div>
						<label style="display: block; margin-bottom: 5px;"><?php _e( 'Translation', 'islamic-scholars' ); ?></label>
						<div class="quill-wrapper translation-editor">
							<div class="quill-editor" data-type="translation"></div>
						</div>
					</div>
				</div>
				<button type="button" class="button add-footnotes-btn">${addFootnotesLabel}</button>
				<div class="footnotes-section" style="display:none;">
					<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
						<strong>📝 ${footnotesLabel}</strong>
						<button type="button" class="button remove-footnotes-btn">${removeFootnotesLabel}</button>
					</div>
					<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
						<div>
							<label style="display: block; margin-bottom: 5px;">${footnoteOriginalLabel}</label>
							<div class="quill-wrapper arabic-editor">
								<div class="quill-editor" data-type="footnote_original"></div>
							</div>
						</div>
						<div>
							<label style="display: block; margin-bottom: 5px;">${footnoteTranslationLabel}</label>
							<div class="quill-wrapper translation-editor">
								<div class="quill-editor" data-type="footnote_translation"></div>
							</div>
						</div>
					</div>
				</div>
			`;
			wrapper.appendChild( newPair );
			initRow( newPair );
			initQuillEditors( newPair );
			updatePairNumbers();
			updateMoveButtons();
		});

		// Drag and drop
		function initRow( row ) {
			row.addEventListener( 'dragstart', function( e ) {
				draggedItem = this;
				this.classList.add( 'dragging' );
				e.dataTransfer.effectAllowed = 'move';
			});

			row.addEventListener( 'dragend', function() {
				this.classList.remove( 'dragging' );
				clearDragClasses();
				updatePairNumbers();
				updateMoveButtons();
			});

			row.addEventListener( 'dragover', function( e ) {
				e.preventDefault();
				if ( !draggedItem || draggedItem === this ) return;
				
				clearDragClasses();
				
				const rect = this.getBoundingClientRect();
				const midY = rect.top + rect.height / 2;
				
				if ( e.clientY < midY ) {
					this.classList.add( 'drag-over-top' );
				} else {
					this.classList.add( 'drag-over-bottom' );
				}
			});

			row.addEventListener( 'drop', function( e ) {
				e.preventDefault();
				if ( !draggedItem || draggedItem === this ) return;
				
				const rect = this.getBoundingClientRect();
				const midY = rect.top + rect.height / 2;
				
				if ( e.clientY < midY ) {
					wrapper.insertBefore( draggedItem, this );
				} else {
					wrapper.insertBefore( draggedItem, this.nextSibling );
				}
				
				clearDragClasses();
				updatePairNumbers();
				updateMoveButtons();
			});

			// Move buttons
			row.querySelector( '.move-up' ).addEventListener( 'click', function( e ) {
				e.preventDefault();
				const prev = row.previousElementSibling;
				if ( prev ) {
					wrapper.insertBefore( row, prev );
					updatePairNumbers();
					updateMoveButtons();
				}
			});

			row.querySelector( '.move-down' ).addEventListener( 'click', function( e ) {
				e.preventDefault();
				const next = row.nextElementSibling;
				if ( next ) {
					wrapper.insertBefore( next, row );
					updatePairNumbers();
					updateMoveButtons();
				}
			});

			// Remove button
			row.querySelector( '.remove-pair' ).addEventListener( 'click', function( e ) {
				e.preventDefault();
				row.remove();
				updatePairNumbers();
				updateMoveButtons();
			});
			
			// Add footnotes button
			const addFootnotesBtn = row.querySelector( '.add-footnotes-btn' );
			const footnotesSection = row.querySelector( '.footnotes-section' );
			const removeFootnotesBtn = row.querySelector( '.remove-footnotes-btn' );
			
			if ( addFootnotesBtn ) {
				addFootnotesBtn.addEventListener( 'click', function( e ) {
					e.preventDefault();
					addFootnotesBtn.style.display = 'none';
					footnotesSection.style.display = 'block';
					// Initialize Quill editors in footnotes section
					footnotesSection.querySelectorAll( '.quill-wrapper' ).forEach( initQuillEditor );
				});
			}
			
			if ( removeFootnotesBtn ) {
				removeFootnotesBtn.addEventListener( 'click', function( e ) {
					e.preventDefault();
					// Clear footnote editors
					footnotesSection.querySelectorAll( '.quill-editor' ).forEach( el => {
						if ( el.quillInstance ) {
							el.quillInstance.setContents([]);
						}
					});
					footnotesSection.style.display = 'none';
					addFootnotesBtn.style.display = 'inline-block';
				});
			}
		}

		function clearDragClasses() {
			wrapper.querySelectorAll( '.translation-pair-row' ).forEach( r => {
				r.classList.remove( 'drag-over-top', 'drag-over-bottom' );
			});
		}

		function updateMoveButtons() {
			const rows = wrapper.querySelectorAll( '.translation-pair-row' );
			rows.forEach( ( row, index ) => {
				row.querySelector( '.move-up' ).disabled = ( index === 0 );
				row.querySelector( '.move-down' ).disabled = ( index === rows.length - 1 );
			});
		}

		function updatePairNumbers() {
			const rows = wrapper.querySelectorAll( '.translation-pair-row' );
			rows.forEach( ( row, index ) => {
				row.querySelector( '.pair-number' ).textContent = pairLabel + ' ' + ( index + 1 );
			});
		}

		// Initialize existing rows
		wrapper.querySelectorAll( '.translation-pair-row' ).forEach( row => {
			initRow( row );
			initQuillEditors( row );
		});
		updateMoveButtons();
	});
	</script>
	<?php
}

/**
 * Translation metadata meta box callback
 */
function islamic_scholars_translation_metadata_callback( $post ) {
	wp_nonce_field( 'islamic_scholars_trans_meta_nonce', 'islamic_scholars_trans_meta_nonce' );
	
	$scholar_id = get_post_meta( $post->ID, 'scholar_id', true );
	$source = get_post_meta( $post->ID, 'source', true );

	$scholars = get_posts( array(
		'post_type' => 'scholar',
		'posts_per_page' => -1,
		'orderby' => 'title',
		'order' => 'ASC',
	) );
	?>
	<div style="padding: 10px 0;">
		<p>
			<label for="scholar_id"><?php _e( 'Scholar', 'islamic-scholars' ); ?></label><br>
			<select id="scholar_id" name="scholar_id" style="width: 100%; padding: 8px;">
				<option value=""><?php _e( '-- Select Scholar --', 'islamic-scholars' ); ?></option>
				<?php foreach ( $scholars as $scholar ) : ?>
					<option value="<?php echo $scholar->ID; ?>" <?php selected( $scholar_id, $scholar->ID ); ?>>
						<?php echo esc_html( $scholar->post_title ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="source"><?php _e( 'Source/Book', 'islamic-scholars' ); ?></label><br>
			<input type="text" id="source" name="source" value="<?php echo esc_attr( $source ); ?>" placeholder="e.g., Sahih Bukhari" style="width: 100%; padding: 8px;">
		</p>
	</div>
	<?php
}

/**
 * Save translation meta
 * Works for both 'post' and 'translation' CPT
 */
function islamic_scholars_save_translation_meta( $post_id ) {
	$post_type = get_post_type( $post_id );
	
	// Allow saving for both 'post' and 'translation'
	if ( ! in_array( $post_type, array( 'translation', 'post' ), true ) ) {
		return;
	}

	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
		return;
	}

	// Verify and save pairs
	if ( isset( $_POST['islamic_scholars_translation_nonce'] ) &&
		 wp_verify_nonce( $_POST['islamic_scholars_translation_nonce'], 'islamic_scholars_translation_nonce' ) ) {
		
		$pairs = array();
		if ( isset( $_POST['translation_pairs'] ) && is_array( $_POST['translation_pairs'] ) ) {
			// Reindex array to maintain order from form submission
			foreach ( $_POST['translation_pairs'] as $pair ) {
				if ( is_array( $pair ) && ( ! empty( $pair['original'] ) || ! empty( $pair['translation'] ) ) ) {
					$pairs[] = array(
						'original' => wp_kses_post( $pair['original'] ?? '' ),
						'translation' => wp_kses_post( $pair['translation'] ?? '' ),
						'footnote_original' => wp_kses_post( $pair['footnote_original'] ?? '' ),
						'footnote_translation' => wp_kses_post( $pair['footnote_translation'] ?? '' ),
					);
				}
			}
		}
		update_post_meta( $post_id, 'translation_pairs', $pairs );
	}

	// Verify and save metadata
	if ( isset( $_POST['islamic_scholars_trans_meta_nonce'] ) &&
		 wp_verify_nonce( $_POST['islamic_scholars_trans_meta_nonce'], 'islamic_scholars_trans_meta_nonce' ) ) {
		
		if ( isset( $_POST['scholar_id'] ) ) {
			update_post_meta( $post_id, 'scholar_id', intval( $_POST['scholar_id'] ) );
		}
		if ( isset( $_POST['source'] ) ) {
			update_post_meta( $post_id, 'source', sanitize_text_field( $_POST['source'] ) );
		}
	}
}
add_action( 'save_post', 'islamic_scholars_save_translation_meta' );
