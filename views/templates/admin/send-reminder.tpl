{*
 * 2020 Nicolas Jacquemin
 *}
<div class="panel">
  <div class="panel-heading"><i class="icon-envelope"></i> Reminders</div>
  <div class="row">
    <div class="col-xs-6">
      {l s='Pending for a reminder' mod='maborderconfirm'}
      <table>
        <tr><td>{l s='Pending total:' mod='maborderconfirm'}</td><td>&#160;{$data.total}</td></tr>
        <tr><td>{l s='> 30 days:' mod='maborderconfirm'}</td><td>&#160;{$data.c30}</td></tr>
        <tr><td>{l s='> 15 days:' mod='maborderconfirm'}</td><td>&#160;{$data.c15}</td></tr>
        <tr><td>{l s='> 7 days:' mod='maborderconfirm'}</td><td>&#160;{$data.c7}</td></tr>
      </table>
    </div>
    <div class="col-xs-6">
      <form action="{$action_url|escape:'html'}" 
            onsubmit="maborderconfirm(this);
             return false;" 
            method="post" class="std">
        <input type="hidden" name="sendreminder" value="9993" />
        <input type="submit" class="btn btn-default" class="icon-enveloppe" value="&#9993; {l s='Send reminder email' mod='maborderconfirm'}">
      </form>
    </div>
  </div>
</div>
