/*------------------------- 
Frontend related javascript
-------------------------*/

(function( $ ) {

	"use strict";

    window.ouqw_price_format = function(price) {
        if ( typeof ouqw_wc_vars === 'undefined' ) {
            return false;
        }
    
        return accounting.formatMoney( price, {
            symbol:    ouqw_wc_vars.currency_format_symbol,
            decimal:   ouqw_wc_vars.currency_format_decimal_sep,
            thousand:  ouqw_wc_vars.currency_format_thousand_sep,
            precision: ouqw_wc_vars.currency_format_num_decimals,
            format:    ouqw_wc_vars.currency_format
        });
    }

    function update_price(ori_price, discount, priceBox) {
        if (discount > 0) {
            var new_price = ori_price - (ori_price * discount / 100);
            priceBox.html(ouqw_price_format(new_price));
        }
        else {
            priceBox.html(ouqw_price_format(ori_price));
        }

        $(document.body).trigger('ouqw_update_price', [ori_price, discount, priceBox]);
    }

    function eventChangeQtyBadge() {
        $(document).on('change keyup', 'form.cart .ouqw-qty-input', function() {
            var qty = $(this).val() != '' ? parseInt($(this).val()) : 0,
                discount = 0,
                tierList = $('.ouqw-badge .ouqw-range-discount'),
                tierItem = tierList.find('.ouqw-tier-discount'),
                index = 0,
                par = $(this).closest('.ouqw_wraper_qty');

            if (par.find('.tier-value').length) {
                par.find('.tier-value').text(qty);
            }
                
            if (ouqw_tiers.discount_type == 'cart_items') {
                if (ouqw_product.type == 'grouped' && $('.woocommerce-grouped-product-list-item').length) {
                    qty = 0;
                    $('.woocommerce-grouped-product-list-item').each(function() {
                        if ($(this).find('.ouqw-qty-input').length) {
                            let qty_item = $(this).find('.ouqw-qty-input').val();
                            if (qty_item != '' && typeof qty_item != 'undefined') {
                                qty += parseInt(qty_item);
                            }
                        }
                    })
                }
                var cart_count = ($('#ouqw_cart_count').length) ? $('#ouqw_cart_count').val() : 0;
                qty += parseInt(cart_count);
            }
            
            $.each(ouqw_tiers.range_data, function ($i, $tier) { 
                if ($tier.rule_range_number != '' && $tier.rule_discount_percent != '') {
                    if (qty >= parseInt($tier.rule_range_number)) {
                        discount = parseFloat($tier.rule_discount_percent);

                        if (ouqw_product.type != 'grouped') {
                            tierItem.removeClass('actived');
                            tierItem.eq(index).addClass('actived');
                        }

                        if (par.find('.ouqw-item').length) {
                            par.find('.ouqw-item').removeClass('actived');
                            par.find('.ouqw-item-'+index).addClass('actived');
                        }
                    }
                    index++;
                }
            });

            if (discount === 0) {
                tierItem.removeClass('actived');
                if (par.find('.ouqw-item').length) {
                    par.find('.ouqw-item').removeClass('actived');
                }
            }

            if (ouqw_product.type == 'variable') {
                var cur_variation = $('form.cart .variation_id').val();
                if ($('.woocommerce-variation-price .price').length) {
                    var priceBox = $('.woocommerce-variation-price .price > .woocommerce-Price-amount bdi, .woocommerce-variation-price .price ins.sale-price .woocommerce-Price-amount bdi');
                } else {
                    var priceBox = $('.ouqw_price_box');
                }
                if (cur_variation && cur_variation != 0) {
                    var ori_price = parseFloat(ouqw_product.variation[cur_variation].display_price);
                    update_price(ori_price, discount, priceBox);
                }
            }
            else if (ouqw_product.type == 'grouped') {
                if (ouqw_tiers.discount_type == 'cart_items') {
                    $('.woocommerce-grouped-product-list-item').each(function() {
                        var item_id = parseInt($(this).attr('id').replace("product-", "")),
                            ori_price = parseFloat(ouqw_product.grouped[item_id].display_price),
                            priceBox = $(this).find('.woocommerce-grouped-product-list-item__price > .woocommerce-Price-amount bdi, .woocommerce-grouped-product-list-item__price ins.sale-price .woocommerce-Price-amount bdi');
                        update_price(ori_price, discount, priceBox);  
                    })
                }
                else {
                    var item = $(this).closest('.woocommerce-grouped-product-list-item'),
                        item_id = parseInt(item.attr('id').replace("product-", "")),
                        ori_price = parseFloat(ouqw_product.grouped[item_id].display_price),
                        priceBox = item.find('.woocommerce-grouped-product-list-item__price > .woocommerce-Price-amount bdi, .woocommerce-grouped-product-list-item__price ins.sale-price .woocommerce-Price-amount bdi');
                    update_price(ori_price, discount, priceBox);
                }
            }
            else {
                var ori_price = ouqw_product.product_price;
                var priceBox = $('.ouqw_price_box > .woocommerce-Price-amount bdi, .ouqw_price_box ins.sale-price .woocommerce-Price-amount bdi');
                update_price(ori_price, discount, priceBox);
            }
        });
    }

    $(document).ready( function() {
        if (typeof ouqw_tiers != 'undefined' && ouqw_tiers.range_data.length > 0) {
            // console.log(ouqw_product);
            // console.log(ouqw_tiers);

            if (ouqw_tiers.product_render_type == 'badge') {
                eventChangeQtyBadge();
            }
        }

        if ($('.ouqw_wraper_qty .tier-value').length) {
            $('.ouqw_wraper_qty .tier-value').on('click touch', function(e) {
                e.preventDefault();
                $(this).next('.tier-table').slideToggle();

                $(document.body).trigger('ouqw_tier_table_toggle');
            })
            
            $('.ouqw_wraper_qty .show_qty_input').on('click touch', function(e) {
                e.preventDefault();
                $(this).parent().toggleClass('standard_show');
                var text = $(this).text(),
                    attr = $(this).attr('data-text_back');
                $(this).text(attr);
                $(this).attr('data-text_back', text);
            })
        
            $(document).mouseup(function(e) {
                var container = $(".wrapper-tier-action");
        
                // if the target of the click isn't the container nor a descendant of the container
                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    container.find('.tier-table').slideUp();
                }
            });
        }

        if ($('.ouqw_wraper_qty .ouqw-item').length) {
            $('.ouqw_wraper_qty .ouqw-item').on('click touch', function(e) {
                e.preventDefault();
                var qty = $(this).data('qty'),
                    discount_percent = $(this).data('discount_percent'),
                    par = $(this).closest('.ouqw_wraper_qty');
                
                par.find('.ouqw-qty-input').val(qty).change();

                $(document.body).trigger('ouqw_tier_item_selected', [$(this)]);
            })
        }

        $('form.cart .ouqw-qty-input').change();
    });

    $( document.body ).on( 'added_to_cart removed_from_cart wc_fragments_loaded', function(e) {
        $('form.cart .ouqw-qty-input').change();
    });

    $(document).on('found_variation', function(e, v) {
        var pid = $(e['target']).closest('.variations_form').data('product_id'),
            vid = v.variation_id,
            vprice = parseFloat(v.display_price);
        if ($('.ouqw_wraper_qty .ouqw-item').length) {
            $('.ouqw_wraper_qty .ouqw-item').each(function() {
                var discount_percent = parseFloat($(this).data('discount_percent')),
                    discount_price = ouqw_price_format(vprice - (vprice * discount_percent / 100));
                $(this).find('.ouqw-item-price-val').html(discount_price);
            })
        }

        if ($('.ouqw-summary-total').length) {
            $('.ouqw-summary-total').html(ouqw_price_format(vprice));
        }

        $(document.body).trigger('ouqw_found_variation');
    });

    $(document).on('reset_data', function(e) {
        if ($('.ouqw-summary-total').length) {
            $('.ouqw-summary-total').html(ouqw_price_format(parseFloat($('.ouqw-summary-total').data('raw_price'))));
        }
    
        $(document.body).trigger('ouqw_reset_data');
    });

    // $.fn.ouqw = ouqw;

})( jQuery );
