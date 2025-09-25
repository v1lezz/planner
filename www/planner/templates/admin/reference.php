<h2>Reference tables</h2>
<div class="grid-3">
  <?php
    $refs = [
      'statuses' => $statuses,
      'priorities' => $priorities,
      'task_types' => $types,
      'board_roles' => $boardRoles,
    ];
  ?>
  <?php foreach ($refs as $name => $rows): ?>
    <div class="card">
      <div class="card-title"><?=ucwords(str_replace('_',' ',$name))?></div>
      <ul class="list">
        <?php foreach ($rows as $r): ?>
          <li><?=$r['name']?>
            <form class="inline" method="post" action="/admin/reference/delete">
              <input type="hidden" name="table" value="<?=$name?>">
              <input type="hidden" name="id" value="<?=$r['id']?>">
              <button class="btn danger small">x</button>
            </form>
          </li>
        <?php endforeach; ?>
      </ul>
      <form method="post" action="/admin/reference/add" class="form">
        <input type="hidden" name="table" value="<?=$name?>">
        <label>Add new <input name="name"></label>
        <button class="btn small">Add</button>
      </form>
    </div>
  <?php endforeach; ?>
</div>
