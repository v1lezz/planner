<h2>Users</h2>
<form method="post" action="/admin/users/create" class="form">
  <h3>Add user</h3>
  <div class="grid-3">
    <label>Name <input name="name"></label>
    <label>Email <input type="email" name="email"></label>
    <label>Password <input type="text" name="password" value="changeme"></label>
  </div>
  <label>Role
    <select name="role">
      <option value="2">client</option>
      <option value="3">staff</option>
      <option value="4">admin</option>
    </select>
  </label>
  <button class="btn">Add</button>
</form>

<table class="table">
  <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?=$u['id']?></td>
        <td><?=htmlspecialchars($u['full_name'])?></td>
        <td><?=htmlspecialchars($u['email'])?></td>
        <td><?=$u['role_name']?></td>
        <td>
          <form method="post" action="/admin/users/update-role" class="inline">
            <input type="hidden" name="id" value="<?=$u['id']?>">
            <select name="role">
              <option value="2" <?=$u['global_role_id']==2?'selected':''?>>client</option>
              <option value="3" <?=$u['global_role_id']==3?'selected':''?>>staff</option>
              <option value="4" <?=$u['global_role_id']==4?'selected':''?>>admin</option>
            </select>
            <button class="btn small">Update</button>
          </form>
          <form method="post" action="/admin/users/delete" class="inline" onsubmit="return confirm('Delete user?')">
            <input type="hidden" name="id" value="<?=$u['id']?>">
            <button class="btn danger small">Delete</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
