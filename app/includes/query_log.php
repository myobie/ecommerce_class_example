<? if ($db->testing) { ?>
  <p id="query-log-link"><a href="#" onclick="$('#query-log').toggle(); return false">Query log</a></p>
  <div id="query-log" style="display: none">
    <? foreach ($db->queries as $query) { ?>
      <p><?= $query ?></p>
    <? } ?>
  </div>
<? } ?>