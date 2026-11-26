(($) => {
  const $quickCart = $("div.header-quickcart");

  $(document).on("click", ".ld-cart-product-price-qty-update", function (e) {
    e.preventDefault();

    const element = $(this);
    const qtyWrapper = element.closest(".ld-cart-product-price-qty");
    const cartItemKey = qtyWrapper.attr("data-cart-item-key");
    const type = element.attr("data-cart-action");
    const productsWrapper = element.closest(".ld-cart-products");

    let data = {
      action: "liquid_update_qty",
      cartItemKey: cartItemKey,
      type: type,
    };

    $.ajax({
      url: liquid_product_qty_object.ajax_url,
      type: "POST",
      dataType: "json",
      data,
      beforeSend: function () {
        productsWrapper.block({
          message: null,
          overlayCSS: {
            background: "#fff",
            opacity: 0.6,
          },
        });
      },
      success: function (results) {
        productsWrapper.unblock();
        $quickCart.empty().append(results.data.html);
        $(".header-cart-fragments").html(
          $(".item-count", $quickCart).first().html()
        );
      },
    });
  });
})(jQuery);
