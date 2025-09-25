<?php
use App\Core\Auth;
$user = Auth::user();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Task Planner</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
<header class="site-header">
  <div class="container flex between">
    <div class="logo"><a href="/">Task Planner</a></div>
    <nav class="main-nav">
        <a href="/dashboard">Dashboards</a>
        <a href="/boards">Boards</a>
      <?php if ($user): ?>
        <span class="muted">Hi, <?=htmlspecialchars($user['full_name'])?></span>
        <a href="/logout">Logout</a>
        <?php if (($user['global_role_id'] ?? 1) >= 4): ?>
          <a href="/admin">Admin</a>
        <?php endif; ?>
      <?php else: ?>
        <a href="/login">Login</a>
        <a href="/register" class="btn">Register</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container">
  <?php require $templatePath; ?>
</main>

<footer class="site-footer">
  <div class="container">
    <div class="grid-3">
      <div><h4>About</h4><p>Task Planner.</p></div>
<!--      <div><h4>Links</h4><p><a href="/migrate">Run migrations</a></p></div>-->
      <div><h4>Contact</h4><p>Email: chaban.ai@edu.spbstu.ru</p></div>
    </div>
    <p class="muted small">© <?=date('Y')?> Task Planner</p>
  </div>
</footer>
<script>

const toggle = (el, cls) => document.querySelectorAll(cls).forEach(cb=>cb.checked=el.checked);
</script>
</body>
</html>
