jQuery(document).ready(function ($) {
  $.fn.showFlex = function () {
    this.css("display", "flex");
  };

  var dapDeliveryPickup = $(".delivery-and-pickup");
  var take_away = $(".delivery-and-pickup-take_away");
  var delivery = $(".delivery-and-pickup-delivery");

  // Check if all fields exist
  if (!dapDeliveryPickup || !take_away || !delivery) {
    return;
  }

  jQuery(document.body).on("checkout_error", function () {
    jQuery("html, body").stop();

    jQuery("html, body").animate(
      { scrollTop: $(".woocommerce-error").offset().top - 230 },
      200
    );
  });

  $("#delivery_and_pickup_type_" + dap_checkout.delivery_and_pickup_type).attr(
    "checked",
    true
  );

  function toggleVisibleFields() {
    var selectedAnswer = dapDeliveryPickup.find("input:checked").val();
    $("delivery_and_pickup_location").prop("selectedIndex", 0); // Reset to default state

    if (selectedAnswer === "take_away") {
      jQuery("#place_oder").removeAttr("disabled");

      take_away.showFlex();
      delivery.hide();
    } else {
      take_away.hide();
      delivery.showFlex();
    }
  }

  function sendPostData() {
    console.log("here");
    var selectedAnswer = dapDeliveryPickup.find("input:checked").val();

    if (selectedAnswer === "take_away") {
      blockUI();

      jQuery.ajax({
        type: "POST",
        url: dbdp_ajax_object.ajax_url,
        data: {
          action: "change_shipping_method",
          shipping_method_type: "take_away",
        },
        success: function (res) {
          jQuery("body").trigger("update_checkout");
          unblockUI();
        },
      });
    } else {
      blockUI();

      jQuery.ajax({
        type: "POST",
        url: dbdp_ajax_object.ajax_url,
        data: {
          action: "change_shipping_method",
          shipping_method_type: "delivery",
        },
        success: function (res) {
          jQuery("body").trigger("update_checkout");
          unblockUI();
        },
      });
    }
  }

  $(document).on(
    "change",
    "input[name=delivery_and_pickup_type]",
    sendPostData
  );

  $(document).on(
    "change",
    "input[name=delivery_and_pickup_type]",
    toggleVisibleFields
  );

  toggleVisibleFields();
});

function blockUI() {
  jQuery("#order_review").block({
    message: null,
    overlayCSS: {
      background: "#fff",
      opacity: 0.6,
    },
  });
}

function unblockUI() {
  jQuery("#order_review").unblock();
}
