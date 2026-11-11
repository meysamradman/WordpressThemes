(($) => {
  function findMatchingVariations(variations, attributes) {
    const matching = [];

    for (let i = 0; i < variations.length; i++) {
      const variation = variations[i];

      if (isVariationMatchingAttributes(variation.attributes, attributes)) {
        matching.push(variation);
      }
    }

    return matching;
  }

  function isVariationMatchingAttributes(
    variationAttributes,
    selectedAttributes,
  ) {
    for (const attrName in variationAttributes) {
      const variationValue = variationAttributes[attrName];
      const selectedValue = selectedAttributes[attrName];

      if (
        variationValue !== "" &&
        variationValue !== undefined &&
        selectedValue !== undefined &&
        variationValue !== selectedValue
      ) {
        return false;
      }
    }
    return true;
  }

  $(document).on("change", ".archive select[data-attribute_name], .elementor-widget-ld_woo_products_list select[data-attribute_name]", function () {
    const select = $(this),
      chosenAtts = {},
      form = select.closest(".variations_form"),
      variations = form.data("product_variations"),
      productLi = form.closest("li.product"),
      attributeFields = form.find("select"),
      addToCartButton = productLi.find(".add_to_cart_button");

    let url = new URL(addToCartButton.attr("href"));

    attributeFields.each(function () {
      const attributeName = $(this).data("attribute_name");
      const value = $(this).val() || "";

      chosenAtts[attributeName] = value;
      url.searchParams.set(attributeName, value);
    });

    addToCartButton.attr("href", url);

    const foundVariations = findMatchingVariations(variations, chosenAtts);

    if (foundVariations.length && foundVariations[0].image) {
      productLi.find("figure img").attr({
        src: foundVariations[0].image.src,
        srcset: foundVariations[0].image.srcset,
        width: foundVariations[0].image.src_w,
        height: foundVariations[0].image.src_h,
      });
    }
  });

  $(document).on("click", ".variations_form .lqd-wc-swatch", function (e) {
    const el = $(this),
      select = el.closest(".value").find("select"),
      value = el.data("value");

    el.addClass("lqd-selected")
      .siblings(".lqd-selected")
      .removeClass("lqd-selected");
    select.val(value);
    select.change();
  });
})(jQuery);
