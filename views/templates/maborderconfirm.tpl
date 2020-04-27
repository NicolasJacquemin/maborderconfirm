<form action="{$link->getPageLink('order-detail', true)|escape:'html'}" method="post" class="std" id="markAsReceived">
  <input type="hidden" class="hidden" value="{$order->id|intval}" name="id_order" />
  <input type="submit" class="exclusive" name="markAsReceived" id="markAsReceivedBtn" value="{l s='I have received this order'}">
  <p class="clear"></p>
</form>
