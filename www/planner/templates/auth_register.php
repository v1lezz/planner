<h2>Register</h2>
<?php if (!empty($error)): ?><div class="alert error"><?=$error?></div><?php endif; ?>
<form method="post" class="form">
  <label>Full name <input type="text" name="name" required></label>
  <label>E-mail <input type="email" name="email" required></label>
  <label>Password <input type="password" name="password" required></label>
  <button class="btn">Create account</button>
</form>
