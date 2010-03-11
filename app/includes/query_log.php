<? if ($db->testing) { ?>
  <p id="query-log-link"><a href="#" onclick="document.getElementById('query-log').style.display = 'block'">Show query log</a></p>
  <div id="query-log" style="display: none">
    <? foreach ($db->queries as $query) { ?>
      <p><?= $query ?></p>
    <? } ?>
  </div>
<? } ?>