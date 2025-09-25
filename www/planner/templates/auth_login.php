<h2>Login</h2>
<?php if (!empty($error)): ?><div class="alert error"><?=$error?></div><?php endif; ?>
<form method="post" class="form">
  <label>E-mail <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <label class="inline"><input type="checkbox" name="remember"> Remember me</label>
  <button class="btn">Login</button>
</form>
<p class="muted small">Tip: after migrations, try admin@example.com / admin123</p>
