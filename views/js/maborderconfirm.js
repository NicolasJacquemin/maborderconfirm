/*
 * 2020 Mon Atelier Bronzage
 */

function markAsReceived()
{
	paramString = "ajax=true";
	$('#markAsReceived').find('input').each(function(){
		paramString += '&' + $(this).attr('name') + '=' + encodeURIComponent($(this).val());
	});
	$.ajax({
		type: "POST",
		headers: { "cache-control": "no-cache" },
		url: $('#markAsReceived').attr("action") + '?rand=' + new Date().getTime(),
		data: paramString,
		success: function (msg){
          // TODO dynamic block-order-detail ID
			$('#block-order-detail').fadeOut('slow', function() {
				$(this).html(msg);
				//catch the submit event of sendOrderMessage form
				
				$(this).find('#markAsReceivedBtn').fadeOut;
				$(this).fadeIn('slow');
			});
		}
	});
	return false;
}
