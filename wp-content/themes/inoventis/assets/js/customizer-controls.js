(function( $ ) {
	'use strict';

	/**
	 * Post Link Control Handler
	 */
	wp.customize.bind( 'ready', function() {
		// Handle post link controls
		$(document).on('click', '.post-link-add-item', function(e) {
			e.preventDefault();
			var $control = $(this).closest('.inoventis-post-link-control');
			var $itemsContainer = $control.find('.post-link-items');
			var $valueInput = $control.find('.post-link-value');
			var index = $itemsContainer.find('.post-link-item').length;
			
			// Get post options from data attribute or existing select
			var postOptionsHtml = $control.attr('data-post-options');
			if (!postOptionsHtml) {
				// Fallback: try to get from existing select
				var $existingSelect = $control.find('.post-link-select').first();
				if ($existingSelect.length) {
					postOptionsHtml = $existingSelect.html();
				} else {
					// Last fallback
					postOptionsHtml = '<option value="0">— Select —</option>';
				}
			}
			
			var html = '<div class="post-link-item" data-index="' + index + '">';
			html += '<div class="post-link-item-header">';
			html += '<span class="post-link-item-title">New Link ' + (index + 1) + '</span>';
			html += '<button type="button" class="post-link-item-toggle"><span class="dashicons dashicons-arrow-down-alt2"></span></button>';
			html += '<button type="button" class="post-link-item-remove"><span class="dashicons dashicons-trash"></span></button>';
			html += '</div>';
			html += '<div class="post-link-item-content" style="display: none;">';
			html += '<label><span>Link Text (optional)</span>';
			html += '<input type="text" class="post-link-text" data-field="text" value="" placeholder="Leave empty to use post title" /></label>';
			html += '<label><span>Select Page/Post/Product</span>';
			html += '<select class="post-link-select" data-field="post_id">' + postOptionsHtml + '</select></label>';
			html += '</div></div>';
			
			var $newItem = $(html);
			$itemsContainer.append($newItem);
			$newItem.find('.post-link-item-content').slideDown();
			$newItem.find('.post-link-item-toggle .dashicons').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
			updatePostLinkValue($control);
		});
		
		$(document).on('click', '.post-link-item-toggle', function(e) {
			e.preventDefault();
			var $item = $(this).closest('.post-link-item');
			var $content = $item.find('.post-link-item-content');
			var $icon = $(this).find('.dashicons');
			if ($content.is(':visible')) {
				$content.slideUp();
				$icon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
			} else {
				$content.slideDown();
				$icon.removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-up-alt2');
			}
		});
		
		$(document).on('click', '.post-link-item-remove', function(e) {
			e.preventDefault();
			if (confirm('Are you sure you want to remove this item?')) {
				var $control = $(this).closest('.inoventis-post-link-control');
				$(this).closest('.post-link-item').slideUp(300, function() {
					$(this).remove();
					updatePostLinkValue($control);
				});
			}
		});
		
		$(document).on('input change', '.post-link-text, .post-link-select', function() {
			var $control = $(this).closest('.inoventis-post-link-control');
			var $item = $(this).closest('.post-link-item');
			updatePostLinkItemTitle($item);
			updatePostLinkValue($control);
		});
		
		function updatePostLinkItemTitle($item) {
			var $textInput = $item.find('.post-link-text');
			var $select = $item.find('.post-link-select');
			var text = $textInput.val();
			var postId = $select.val();
			var displayText = text;
			if (!displayText && postId) {
				var $option = $select.find('option:selected');
				displayText = $option.text();
			}
			if (!displayText) {
				var index = $item.data('index') || 0;
				displayText = 'New Link ' + (index + 1);
			}
			$item.find('.post-link-item-title').text(displayText);
		}
		
		function updatePostLinkValue($control) {
			var items = [];
			$control.find('.post-link-item').each(function() {
				var $item = $(this);
				var item = {
					text: $item.find('.post-link-text').val(),
					post_id: $item.find('.post-link-select').val()
				};
				if (item.post_id && item.post_id != '0') {
					items.push(item);
				}
			});
			$control.find('.post-link-value').val(JSON.stringify(items)).trigger('change');
		}
		
		// Make items sortable
		$('.post-link-items').sortable({
			handle: '.post-link-item-header',
			axis: 'y',
			opacity: 0.8,
			update: function() {
				var $control = $(this).closest('.inoventis-post-link-control');
				updatePostLinkValue($control);
			}
		});
	});

	/**
	 * Repeater Control Handler (for social media)
	 */
	wp.customize.bind( 'ready', function() {
		// Handle repeater controls
		$( '.inoventis-repeater-control' ).each( function() {
			var $control = $( this );
			var $valueInput = $control.find( '.repeater-value' );
			var $itemsContainer = $control.find( '.repeater-items' );
			
			// Get fields from data attributes - parse JSON directly from attribute
			var fieldKeys = [];
			var fieldLabels = {};
			
			try {
				var fieldsAttr = $control.attr( 'data-fields' );
				var labelsAttr = $control.attr( 'data-field-labels' );
				
				if ( fieldsAttr ) {
					fieldKeys = JSON.parse( fieldsAttr );
				}
				if ( labelsAttr ) {
					fieldLabels = JSON.parse( labelsAttr );
				}
			} catch ( e ) {
				console.warn( 'Error parsing repeater control data attributes:', e );
			}
			
			// If no data attributes, try to get from existing items
			if ( fieldKeys.length === 0 ) {
				$control.find( '.repeater-item-content' ).first().each( function() {
					$( this ).find( '.repeater-field' ).each( function() {
						var fieldKey = $( this ).data( 'field' );
						var fieldLabel = $( this ).closest( 'label' ).find( 'span' ).text();
						if ( fieldKey ) {
							fieldKeys.push( fieldKey );
							fieldLabels[ fieldKey ] = fieldLabel;
						}
					} );
				} );
			}

			// If still no fields, determine from control context
			if ( fieldKeys.length === 0 ) {
				var controlId = $control.closest( '.customize-control' ).attr( 'id' ) || '';
				if ( controlId.includes( 'social' ) ) {
					fieldKeys = [ 'label', 'url' ];
					fieldLabels = {
						label: 'Label (e.g., Facebook, Instagram)',
						url: 'URL'
					};
				} else {
					fieldKeys = [ 'text', 'url' ];
					fieldLabels = {
						text: 'Link Text',
						url: 'Link URL'
					};
				}
			}
			
			// Debug log (remove in production if needed)
			if ( console && console.log ) {
				console.log( 'Repeater control initialized:', {
					fieldKeys: fieldKeys,
					fieldLabels: fieldLabels,
					controlId: $control.closest( '.customize-control' ).attr( 'id' )
				} );
			}

			/**
			 * Update the hidden input value
			 */
			function updateValue() {
				var items = [];
				$itemsContainer.find( '.repeater-item' ).each( function() {
					var $item = $( this );
					var itemData = {};
					$item.find( '.repeater-field' ).each( function() {
						var fieldKey = $( this ).data( 'field' );
						itemData[ fieldKey ] = $( this ).val();
					} );
					items.push( itemData );
				} );
				var jsonValue = JSON.stringify( items );
				$valueInput.val( jsonValue );
				
				// Trigger WordPress customizer change event
				$valueInput.trigger( 'change' );
				
				// Also trigger input event for better compatibility
				$valueInput.trigger( 'input' );
			}

			/**
			 * Get display title for item
			 */
			function getItemTitle( itemData ) {
				if ( itemData.text ) {
					return itemData.text;
				}
				if ( itemData.label ) {
					return itemData.label;
				}
				return 'New Item';
			}

			/**
			 * Create new item HTML
			 */
			function createItemHTML( itemData ) {
				itemData = itemData || {};
				var index = $itemsContainer.find( '.repeater-item' ).length;
				var title = getItemTitle( itemData );
				
				var html = '<div class="repeater-item" data-index="' + index + '">';
				html += '<div class="repeater-item-header">';
				html += '<span class="repeater-item-title">' + ( title || 'New Item' ) + '</span>';
				html += '<button type="button" class="repeater-item-toggle"><span class="dashicons dashicons-arrow-down-alt2"></span></button>';
				html += '<button type="button" class="repeater-item-remove"><span class="dashicons dashicons-trash"></span></button>';
				html += '</div>';
				html += '<div class="repeater-item-content" style="display: none;">';
				
				fieldKeys.forEach( function( fieldKey ) {
					var fieldLabel = fieldLabels[ fieldKey ] || fieldKey;
					var fieldType = fieldKey === 'url' ? 'url' : 'text';
					var fieldValue = itemData[ fieldKey ] || '';
					html += '<label>';
					html += '<span>' + fieldLabel + '</span>';
					html += '<input type="' + fieldType + '" class="repeater-field" data-field="' + fieldKey + '" value="' + fieldValue + '" />';
					html += '</label>';
				} );
				
				html += '</div>';
				html += '</div>';
				
				return html;
			}

			/**
			 * Add new item
			 */
			$control.on( 'click', '.repeater-add-item', function( e ) {
				e.preventDefault();
				e.stopPropagation();
				
				if ( ! fieldKeys || fieldKeys.length === 0 ) {
					alert( 'Error: Cannot determine field structure. Please refresh the page.' );
					console.error( 'Field keys not found:', fieldKeys );
					return;
				}
				
				var $newItem = $( createItemHTML() );
				$itemsContainer.append( $newItem );
				$newItem.find( '.repeater-item-content' ).slideDown();
				$newItem.find( '.repeater-item-toggle .dashicons' ).removeClass( 'dashicons-arrow-down-alt2' ).addClass( 'dashicons-arrow-up-alt2' );
				
				// Focus on first input
				$newItem.find( '.repeater-field' ).first().focus();
				
				updateValue();
			} );

			/**
			 * Toggle item accordion
			 */
			$control.on( 'click', '.repeater-item-toggle', function( e ) {
				e.preventDefault();
				var $item = $( this ).closest( '.repeater-item' );
				var $content = $item.find( '.repeater-item-content' );
				var $icon = $( this ).find( '.dashicons' );
				
				if ( $content.is( ':visible' ) ) {
					$content.slideUp();
					$icon.removeClass( 'dashicons-arrow-up-alt2' ).addClass( 'dashicons-arrow-down-alt2' );
				} else {
					$content.slideDown();
					$icon.removeClass( 'dashicons-arrow-down-alt2' ).addClass( 'dashicons-arrow-up-alt2' );
				}
			} );

			/**
			 * Remove item
			 */
			$control.on( 'click', '.repeater-item-remove', function( e ) {
				e.preventDefault();
				if ( confirm( 'Are you sure you want to remove this item?' ) ) {
					$( this ).closest( '.repeater-item' ).slideUp( 300, function() {
						$( this ).remove();
						updateValue();
					} );
				}
			} );

			/**
			 * Update item title when fields change
			 */
			$control.on( 'input change', '.repeater-field', function() {
				var $item = $( this ).closest( '.repeater-item' );
				var itemData = {};
				$item.find( '.repeater-field' ).each( function() {
					var fieldKey = $( this ).data( 'field' );
					itemData[ fieldKey ] = $( this ).val();
				} );
				$item.find( '.repeater-item-title' ).text( getItemTitle( itemData ) );
				updateValue();
			} );

			// Make items sortable
			$itemsContainer.sortable( {
				handle: '.repeater-item-header',
				axis: 'y',
				opacity: 0.8,
				update: function() {
					updateValue();
				}
			} );
		} );
	} );

})( jQuery );
