jQuery( function( $ ) {

	// class OUQW_Backend_Handler {
	// 	constructor() {
	
	// 	}
	// }

	function clear_form_elements($selector) {
		$selector.each(function(i) {
			$(this).off();
			switch(this.type) {
				case 'select-one':
					$(this).val($(this).find('option:first-child').val());
					break;
				case 'password':
				case 'text':
				case 'textarea':
				case 'file':
				case 'date':
				case 'number':
				case 'tel':
				case 'email':
					$(this).val('');
					break;
				case 'checkbox':
				case 'radio':
					this.checked = false;
					$(this).change();
					break;
			}
			$(this).change();
		});
	}

	function ouqw_toggle_field_conditon($selector = false) {
		var $selector = !$selector ? $('.ouqw_setting_field[type="checkbox"]') : $selector;
		$selector.on('change', function(e) {
			e.preventDefault();
			var curCheck = $(this).is(":checked"),
				par = $(this).closest('.options_group_condition'),
				fieldCondition = par.next('.ouqw_group_settings_mt.ouqw_group_settings_condition');
			if (fieldCondition.length) {
				fieldCondition.each(function() {
					if ($(this).hasClass('toggle_hidden')) {
						if (curCheck) {
							$(this).slideDown();
						} 
						else {
							$(this).slideUp();
						}
					}
					else {
						$(this).toggleClass('setting_hidden');
					}
				})
			}
		});
	}

	function ouqw_save_settings() {
		if (!$('#ouqw_submit_settings').length) return false;

		$('#ouqw_submit_settings').on('click', function(e) {
			e.preventDefault();
			var btn = $(this),
				data = {};

			if (btn.hasClass('loading')) return;

			data['action'] = 'ouqw_handle_settings_form';
			data['ajax_nonce_parameter'] = ouqw_script.security_nonce;
			$('.ouqw_g_set_tabcontents .ouqw_setting_field').each(function() {
				if ($(this).attr('type') == 'checkbox' && !$(this).is(":checked")) {
					data[$(this).attr('name')] = 0;	
				}
				else {
					data[$(this).attr('name')] = $(this).val();
				}
			});

			$.ajax({ 
				url: ouqw_script.ajaxurl,
				type: "post", 
				dataType: 'json', 
				data: data, 
				beforeSend: function(){
					btn.addClass('loading');
					btn.closest('.ouqw_wrap_settings').addClass('submitting');
				},
				success: function(data) { 
					$.toast({
						heading: 'Success',
						text: data.data.message,
						showHideTransition: 'slide',
						icon: 'success',
						position: 'top-right',
						hideAfter: 6000
					})
					
				}, 
				error: function() { 
					alert("An error occured, please try again.");          
				},
				complete: function() {
					btn.removeClass('loading');
					btn.closest('.ouqw_wrap_settings').removeClass('submitting');
				}
			});   
		});
	}

	function ouqw_init_select2_settings($selector = false) {
		var $selector = !$selector ? $('.ouqw_init_select2') : $selector;

		if (!$selector.length) return false;
		
		$selector.each(function() {
			var optionSelect2;
			if ($(this).hasClass('ouqw_rules_apply')) {
				optionSelect2 = {
					ajax: {
						url: ouqw_script.ajaxurl,
						dataType: 'json',
						delay: 250,
						data: function (params) {
							var term = this.closest('.ouqw_wrapper_rules_apply').parent().find('.ouqw_rule_apply_for').val();
							return {
								term: term,
								q: params.term, // search query
								ajax_nonce_parameter: ouqw_script.security_nonce,
								action: 'ouqw_load_rule_apply_ajax'
							};
						},
						processResults: function( data ) {
							var options = [];
							if ( data ) {
								$.each( data, function( index, text ) {
									options.push( { id: text[0], text: text[1]  } );
								});
							}
							return {
								results: options
							};
						},
						cache: true
					},
					minimumInputLength: 1,
					// placeholder: function(){
					// 	$(this).data('placeholder');
					// },
				};
			}

			// Init select2
			$(this).select2(optionSelect2);
		})
	}

	function ouqw_init_number_format($selector = false) {
		var $selector = !$selector ? $('.ouqw_input_float') : $selector;

		if (!$selector.length) return false;
		
		$selector.each(function() {
			$(this).inputNumberFormat();
			$(this).on('change keyup', function() {
				var val = $(this).val() != '' ? parseFloat($(this).val()) : 0;
				if (val > 99.99) {
					$(this).val(99.99);
				}
			})
		})
	}
	
	function ouqw_change_rule_apply_for($selector = false) {
		var $selector = !$selector ? $('.ouqw_rule_apply_for') : $selector;

		if (!$selector.length) return false;
		if (!$('.ouqw_rule_apply_for').length) return false;

		$('.ouqw_rule_apply_for').on('change', function(e) {
			e.preventDefault();
			var rule = $(this).val(),
				applyField = $(this).closest('.ouqw_setting_form').next('.ouqw_wrapper_rules_apply');
			if ($.inArray(rule, ['all', 'instock', 'outofstock', 'onbackorder']) === -1) {
				if (applyField.hasClass('ouqw_hidden')) applyField.removeClass('ouqw_hidden');
				applyField.find('.ouqw_rules_apply').val(null).trigger('change');
			}
			else {
				applyField.addClass('ouqw_hidden');
			}
		})
	}

	function ouqw_init_repeater_rules() {
		if (!$('#ouqw_rules_settings .ouqw_wrapper_rules').length) return false;

		$('#ouqw_rules_settings .ouqw_wrapper_rules').ouqwRepeater({
			btnAddClass: 'rpt_btn_add',
			btnRemoveClass: 'rpt_btn_remove',
			groupClass: 'ouqw_rules_box',
			minItems: 1,
			maxItems: 0,
			startingIndex: $('.ouqw_rules_box').length - 1,
			showMinItemsOnLoad: true,
			reindexOnDelete: true,
			repeatMode: 'insertAfterLast',
			animation: 'fade',
			animationSpeed: 400,
			animationEasing: 'swing',
			clearValues: true,
			afterAdd: function($item) {
				if (!$item.hasClass('added')) {
					// Clear new field
					clear_form_elements($item.find('.ouqw_setting_field'));

					// Reinit toggle condition
					ouqw_toggle_field_conditon($item.find('.ouqw_setting_field[type="checkbox"]'));

					// Reinit select2
					$item.find('.ouqw_rules_apply').empty();
					$item.find('.ouqw_init_select2').val('');
					ouqw_init_select2_settings($item.find('.ouqw_init_select2'));

					// Recatch event change
					ouqw_change_rule_apply_for($item.find('.ouqw_rule_apply_for'));

					// Trigger change
					$item.find('.ouqw_setting_field').change();

					// Init number format input
					ouqw_init_number_format($item.find('.ouqw_input_float'));
				}
				
				//afterAdded
				$item.addClass('added');
			},
		});
	}

    $(document).ready(function($) {
		// Setting page
		$( '.ouqw_wrap_settings .ouqw_g_set_tabs li a' ).on( 'click', function(e) {
			// e.preventDefault();
			$( '.ouqw_wrap_settings .ouqw_g_set_tabs li a' ).removeClass('active');
			$(this).addClass('active');
			$('.ouqw_tabcontent').hide();
			$($(this).attr('href')).show();
		})

		ouqw_init_repeater_rules();
		ouqw_toggle_field_conditon();
		ouqw_save_settings();
		ouqw_init_select2_settings();
		ouqw_change_rule_apply_for();
		ouqw_init_number_format();

    });

});