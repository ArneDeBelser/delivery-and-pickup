jQuery(document).ready(function ($) {
  $("#delivery-and-pickup-datepicker").datepicker({
    minDate: new Date(),
    dateFormat: "dd-mm-yy",

    beforeShow: function () {
      $("#delivery-and-pickup-datepicker").css("font-size", 13);
    },

    beforeShowDay: function (date) {
      if (date.getDay() == 6 || date.getDay() == 0) {
        return [false, ""];
      }

      return [true, ""];
    },
  });
});
