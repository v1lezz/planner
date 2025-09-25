<section class="hero">
  <h1>Добро пожаловать в Task Planner</h1>
  <p class="muted">Create boards, epics and tasks. Track progress in a lightweight way.</p>
</section>
<section>
  <h2>Your Boards</h2>
  <div class="cards">
    <?php foreach ($boards as $b): ?>
      <a class="card" href="/board?id=<?=$b['id']?>">
        <div class="card-title"><?=htmlspecialchars($b['name'])?></div>
        <div class="muted">Key: <?=htmlspecialchars($b['board_key'])?></div>
      </a>
    <?php endforeach; ?>
  </div>
</section>
