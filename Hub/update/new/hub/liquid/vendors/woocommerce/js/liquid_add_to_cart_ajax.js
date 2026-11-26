(($) => {

  const $addToCartBtn = $('.single_add_to_cart_button');

  $addToCartBtn.on('click', function (e) {

    const $btn = $(e.currentTarget);
    const yithWapo = $btn.siblings('#yith-wapo-container');

    if (yithWapo.length) return;

    e.preventDefault();

    var btnData = {};

    // Fetch changes that are directly added by calling $btn.data( key, value )
    $.each($btn.data(), function (key, value) {
      btnData[key] = value;
    });

    // Fetch data attributes in $btn. Give preference to data-attributes because they can be directly modified by javascript
    // while `.data` are jquery specific memory stores.
    $.each($btn[0].dataset, function (key, value) {
      btnData[key] = value;
    });

    if (!btnData.product_name) {
      const $nameInput = $btn.siblings('input.lqd-product-name');
      if ($nameInput.length) {
        btnData['product_name'] = $nameInput.val();
      }
    }

    const form = $btn.closest('form');
    const $quickCart = $('div.header-quickcart');
    const $wooMsg = $('.lqd-woo-added-msg');
    const product_id = $('input[name=product_id]').val() || $btn.val();
    const variation_id = $('input[name="variation_id"]').val();
    const quantity = $('input[name="quantity"]').val();
    const isGrouped = form.find("input[name^='quantity[']").length > 0;
    let data = {
      action: 'liquid_add_cart_single',
      product_id,
      quantity
    }

    if (variation_id && variation_id != '') {
      data = {
        action: 'liquid_add_cart_single',
        product_id,
        variation_id,
        quantity
      }
    }

    if (isGrouped) {
      const quantities = {};

      form.find("input[name^='quantity[']").each(function () {
        const qty = parseInt($(this).val());
        const id = $(this).attr('name').match(/\d+/)[0];

        if (qty > 0) quantities[id] = qty;
      });

      data = {
        action: 'liquid_add_cart_single',
        product_id,
        quantities,
        isGrouped
      }

      if (!Object.keys(quantities).length) return;
    }

    $btn.addClass('adding-to-cart loading');
    $wooMsg.fadeIn().addClass('adding-to-cart').removeClass('added-to-cart');

    $.ajax({
      url: liquid_ajax_object.ajax_url,
      type: 'POST',
      dataType: 'json',
      data,
      beforeSend: function () {
        $(document.body).trigger('adding_to_cart', [$btn, btnData]);
      },
      success: function (results) {
        $wooMsg.removeClass('adding-to-cart').addClass('added-to-cart');
        $quickCart.empty().append(results.data.html);

        $('.header-cart-fragments').html($('.item-count', $quickCart).first().html());
        $btn.removeClass('adding-to-cart loading');

        setTimeout(function () {
          $wooMsg.fadeOut();
        }, 6000);

        // Trigger event so themes can refresh other areas.
        $(document.body).trigger('added_to_cart', [results.fragments, results.cart_hash, $btn]);
      }
    });
  });

  $(document).on('yith_quick_view_loaded', function (e) {
    $('.single_add_to_cart_button').on('click', function (e) {
      const $btn = $(this);
      const yithWapo = $btn.siblings('#yith-wapo-container');

      if (yithWapo.length) return;

      e.preventDefault();

      const $quickCart = $('div.header-quickcart');
      const $wooMsg = $('.lqd-woo-added-msg');
      const product_id = $('input[name=product_id]').val() || $btn.val();
      const variation_id = $('input[name="variation_id"]').val();
      const quantity = $('input[name="quantity"]').val();
      const form = $btn.closest('form');
      const isGrouped = form.find("input[name^='quantity[']").length > 0;

      var btnData = {};

      $.each($btn.data(), function (key, value) {
        btnData[key] = value;
      });

      $.each($btn[0].dataset, function (key, value) {
        btnData[key] = value;
      });

      if (!btnData.product_name) {
        const title = form.parent().find('.entry-title').text();
        btnData['product_name'] = title;
      }

      let data = {
        action: 'liquid_add_cart_single',
        product_id,
        quantity
      }

      if (variation_id && variation_id != '') {
        data = {
          action: 'liquid_add_cart_single',
          product_id,
          variation_id,
          quantity
        }
      }

      if (isGrouped) {
        const quantities = {};

        form.find("input[name^='quantity[']").each(function () {
          const qty = parseInt($(this).val());
          const id = $(this).attr('name').match(/\d+/)[0];

          if (qty > 0) quantities[id] = qty;
        });

        data = {
          action: 'liquid_add_cart_single',
          product_id,
          quantities,
          isGrouped
        }

        if (!Object.keys(quantities).length) return;
      }

      $btn.addClass('adding-to-cart');
      $wooMsg.fadeIn().addClass('adding-to-cart').removeClass('added-to-cart');

      $.ajax({
        url: liquid_ajax_object.ajax_url,
        type: 'POST',
        data,
        dataType: 'json',
        beforeSend: function () {
          $(document.body).trigger('adding_to_cart', [$btn, btnData]);
        },
        success: function (results) {
          $wooMsg.removeClass('adding-to-cart').addClass('added-to-cart');
          $quickCart.empty().append(results.data.html);

          $('.header-cart-fragments').html($('.item-count', $quickCart).first().html());
          $addToCartBtn.removeClass('adding-to-cart');

          $('#yith-quick-view-modal').removeClass('open').removeClass('loading');
          $('html').removeClass('yith-quick-view-is-open');

          $(document.body).trigger('added_to_cart', [results.fragments, results.cart_hash, $btn]);
        }
      });

    });
  })
})(jQuery);
