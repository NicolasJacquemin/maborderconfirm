{if $order_history.0.id_order_state == $id_status_shipped}
  <form action="{$link->getPageLink('order-detail', true)|escape:'html'}" onsubmit="maborderconfirm(this);return false;" method="post" class="std" id="markAsReceived">
    <input type="hidden" class="hidden" value="{$order->id|intval}" name="id_order" />
    <input type="hidden" class="hidden" value="{$order_history.0.id_order_state}" name="markAsReceived" />
    <input type="submit" class="btn btn-default" value="{l s='I have received this order' mod='maborderconfirm'}">
  </form>
{/if}
