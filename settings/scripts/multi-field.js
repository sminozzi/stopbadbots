;(function($){

	$(document).ready(function()
	{
			var multiField = {
				$fieldWrappers: $('.multi-field-wrapper'),
				init: function() {
					
					var $removeBtns = this.$fieldWrappers.find('.remove-button');
					$removeBtns.bind('click', this.onRemoveBtnClick);

					this.$fieldWrappers.each(this.initWrapper);

				},
				initWrapper: function() {
					
					var self = multiField,
							$wrapper = $(this);

					self.makeSortable($wrapper);
					self.appendAddButton($wrapper);
					self.updateFields($wrapper);

				},
				makeSortable: function( $wrapper ) {
					
					var self = multiField;
					
					$wrapper.sortable({
						axis: "y",
						containment: "parent",
						handle: '.handle',
						update: self.onSortUpdate,
						start: self.onSortStart

					});
				},
				updateFields: function( $wrapper ) {

					var self = multiField,
							$fieldGroups = $wrapper.find('li.fields');

					$fieldGroups.each(function(key, value) {

						var $group = $(this),
								$fields = $group.find('[name]'),
								$removeBtn = $group.find('.remove-button'),
								$checkedFields = $fields.filter(':checked');


						$fields.each(function(){
							
							var $this = $(this),
									checked = $this.prop('checked');

							// Update the order values on the field
							// Order values are determined by the name value
							self.setOrderVal($this, key);

							//Update the id of the field and any data values
							self.setIdVal($this, key);

						});

						// Sorting unchecks the radio boxes
						// Loop the cached checked items and recheck them
						if(self.$checkedFields != undefined) {
							self.$checkedFields.each(function(){
								var self = multiField,
										$this = $(this);

								self.setCheckedState($this);
							});
						}

						// Update remove button display state
						if(key == 0) {
							$removeBtn.addClass('disabled');
						} else {
							$removeBtn.removeClass('disabled');
						}

					});
				},
				setIdVal: function( $field, key ) {

					var self = multiField,
							pattern = /_\d+$/, 
							replace = '_' + key,
							type = $field.attr('type'),
							$uploadBtn = $field.siblings('.button-upload');

					if(type == 'radio') {
						pattern = /_(\d+)_(\d+)$/;
						replace = function(match, p1, p2) {
							return '_' + key + '_' + p2;
						}
					}

					var newId = self.updateAttributeWithPattern($field, 'id', pattern, replace);

					// Modify the upload field data attribute
					if($uploadBtn.length > 0) {
						$uploadBtn.data('field', newId);
					}

					// Modify parent labels for radios and checkboxs
					$field.parents('label').attr('for', newId);

				},
				setOrderVal: function( $field, key ) {
					
					var self = multiField,
							pattern = /[\d]+/;

					self.updateAttributeWithPattern($field, 'name', pattern, key);

				},
				setCheckedState: function( $field ) {
							$field.prop('checked', true);
							$field.attr('checked', 'checked');
				},
				updateAttributeWithPattern: function($field, attr, pattern, replace) {

					var old = $field.attr(attr),
							newVal = old.replace(pattern, replace);
					$field.attr(attr, newVal);
					return newVal;
				},
				cacheCheckedFields: function($wrapper) {
					var self = multiField;
							
					self.$checkedFields = $wrapper.find(':checked');
				},
				appendAddButton: function( $wrapper ) {
					
					var self = multiField;

					$addButton = $('<button>', {
						'class': 'button-primary add-item-button'
					}).text('+')
						.data('wrapper', $wrapper.attr('id'))
						.css({position:'absolute', top:3, right:3})
						.click(self.onAddButtonClick);

					$wrapper.css({position:'relative'})
						.prepend($addButton);
				},
				resetTextualFields: function($fields) {

					$fields.each(function(){

						var $this = $(this),
								resetVal = ($this.data('reset')) ? $this.data('reset') : '';

						$this.val(resetVal);

					});
				},
				resetRadioFields: function($fields) {

					$fields.each(function(){

						var self = multiField,
								$this = $(this),
								resetVal = $this.parents('.radio-options').data('reset'),
								value = $this.val();

						if(resetVal == value) {
							self.setCheckedState($this);
						} else {
							$this.removeAttr('checked');
							$this.prop('checked', false);
						}

					});
				
				},
				resetCheckBoxFields: function($fields) {

					$fields.each(function(){

						var $this = $(this),
								resetVal = ($this.data('reset')) ? $this.data('reset') : 0;

						if(resetVal == 1) {
							$this.attr('checked', 'checked');
						} else {
							$this.removeAttr('checked');
						}

					});

				},
				resetColorFields: function($fields) {

					$fields.each(function(){

						$(this).css('background-color', '#f9f9f9' );

					});

				},
				onSortUpdate: function( event, ui ){

					var self = multiField,
							$item = ui.item,
							$wrapper = $item.parent();

					self.updateFields($wrapper);

				},
				onSortStart: function( event, ui ) {
					var self = multiField,
							$item = ui.item,
							$wrapper = $item.parent();

					self.cacheCheckedFields($wrapper);

				},
				onRemoveBtnClick: function(e) {
					var self = multiField,
							$removeBtn = $(this),
							$wrapper = $removeBtn.parents('.multi-field-wrapper'),
							$fieldGroup = $removeBtn.parents('li.fields');

					$fieldGroup.remove();

					self.updateFields($wrapper);
				},
				onAddButtonClick: function(e) {
					
					e.preventDefault();
					var self = multiField,
							$addButton = $(this),
							$wrapper = $('#' + $addButton.data('wrapper')),
							limit = $wrapper.data('limit');

					// Use limit to determine if a field should be added
					if($wrapper.find('li.fields').length < limit || limit == 0) {

						var $lastItem = $wrapper.find('li').last(),
								$lastItemChecked = $lastItem.find(':checked'),
								$fieldGroupCopy = $lastItem.clone(true, true),
								$fields = $fieldGroupCopy.find('[name]'),
								$textualFields = $fields.filter(function(){
									var $this = $(this)
											type = $this.attr('type');

									return ( type != 'radio' && type != 'checkbox' );
								}),
								$radioFields = $fields.filter('[type=radio]'),
								$checkboxFields = $fields.filter('[type=checkbox]'),
								$colorFields = $fieldGroupCopy.find('.wp-color-result');

						// Append New Field Group
						$fieldGroupCopy.appendTo($wrapper);

						// Update the fields for this wrapper
						self.updateFields($wrapper);

						// --- Reset all the field values on the cloned item
						self.resetTextualFields($textualFields);
						self.resetRadioFields($radioFields);
						self.resetCheckBoxFields($checkboxFields);
						self.resetColorFields($colorFields);

						// Cloning this removed any check values
						// So update any cached check values 
						// on the item that was used for cloning
						$lastItemChecked.each(function(){
							var self = multiField,
									$this = $(this);

							self.setCheckedState($this);
							
						});
					}	
				}
			}

			// Initialize
			multiField.init();

	});


})(jQuery);