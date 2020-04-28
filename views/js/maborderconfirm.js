/*
 * 2020 Mon Atelier Bronzage
 */
function maborderconfirm(form) {
  var action = $('#markAsReceived').attr("action");
  var paramString = "ajax=true";
  $(form).find('input').each(function () {
    if ($(this).attr('type') !== "submit") {
      paramString += '&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val());
    }
  });
  
  var orderId = $(form).find("input[name=orderId]")[0].value;
  var file = action + '&id_order=' + orderId;

  $.ajax({
    type: "POST",
    headers: {"cache-control": "no-cache"},
    url: action + '&rand=' + new Date().getTime(),
    data: paramString,
    success: function () {
      showOrder(1, orderId, file);
    }
  });
  return false;
}
