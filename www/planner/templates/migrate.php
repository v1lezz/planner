<h2>Migrations</h2>
<?php if (empty($log)): ?>
  <div class="alert">No new migrations. DB is up-to-date.</div>
<?php else: ?>
  <div class="alert success">
    <?php foreach ($log as $line): ?>
      <div><?=htmlspecialchars($line)?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<p>Now you can <a href="/">go to home</a>. Default admin: admin@example.com / admin123</p>
