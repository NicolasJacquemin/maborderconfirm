{*
 * 2020 Nicolas Jacquemin
 *}
<div class="panel">
  <div class="panel-heading"><i class="icon-envelope"></i> Reminders</div>
  <div class="row">
    <div class="col-xs-6">
      <table>
        <tr><td>total: </td><td>&#160;{$data.total}</td></tr>
        <tr><td>30 days: </td><td>&#160;{$data.c30}</td></tr>
        <tr><td>15 days: </td><td>&#160;{$data.c15}</td></tr>
        <tr><td>7 days: </td><td>&#160;{$data.c7}</td></tr>
      </table>
    </div>
    <div class="col-xs-6">
      <form action="{$action_url|escape:'html'}" 
            onsubmit="maborderconfirm(this);
             return false;" 
            method="post" class="std">
        <input type="hidden" name="sendreminder" value="9993" />
        <input type="submit" class="btn btn-default" class="icon-enveloppe" value="&#9993; send reminder email">
      </form>
    </div>
  </div>
</div>
