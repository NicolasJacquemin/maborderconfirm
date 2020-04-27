{if $order_history.0.id_order_state == 4}
  <form action="{$link->getPageLink('order-detail', true)|escape:'html'}" method="post" class="std" id="markAsReceived">
    <input type="hidden" class="hidden" value="{$order->id|intval}" name="id_order" />
    <input type="submit" class="btn btn-default" name="markAsReceived" id="markAsReceivedBtn" value="{l s='I have received this order' mod='maborderconfirm'}">
    <p class="clear"></p>
  </form>
{/if}
