<h2>Boards</h2>
<div class="cards">
  <?php foreach ($boards as $b): ?>
    <a class="card" href="/board?id=<?=$b['id']?>">
      <div class="card-title"><?=htmlspecialchars($b['name'])?></div>
      <div class="muted">Key: <?=htmlspecialchars($b['board_key'])?></div>
    </a>
  <?php endforeach; ?>
</div>
