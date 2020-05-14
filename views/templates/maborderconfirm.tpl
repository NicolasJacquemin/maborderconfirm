{*
 * 2020 Nicolas Jacquemin
 *}
{if $order->current_state == $id_status_shipped}
  <form action="{$action_url|escape:'html'}" 
        data-callback="{$link->getPageLink('order-detail', true)|escape:'html'}"
        onsubmit="maborderconfirm(this);return false;" 
        method="post" class="std">
    <input type="hidden" class="hidden" value="{$order->id|intval}" name="id_order" />
    <input type="hidden" class="hidden" value="{$order->current_state}" name="markAsReceived" />
    <input type="submit" class="btn btn-default" value="{l s='I have received this order' mod='maborderconfirm'}">
  </form>
{/if}
