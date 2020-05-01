/*
 * 2020 Mon Atelier Bronzage
 */
function maborderconfirm(form) {
  var action = form.getAttribute("action");
  var callback = form.dataset.callback;
  var paramString = "ajax=true";
  $(form).find('input').each(function () {
    if ($(this).attr('type') !== "submit") {
      paramString += '&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val());
    }
  });
  
  var orderId = $(form).find("input[name=id_order]")[0].value;
  var file = callback + '&id_order=' + orderId;

  $.ajax({
    type: "POST",
    headers: {"cache-control": "no-cache"},
    url: action + '?rand=' + new Date().getTime(),
    data: paramString,
    success: function () {
      showOrder(1, orderId, file);
    }
  });
  return false;
}
