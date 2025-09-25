<?php
/** @var array $board */
/** @var array $members */
/** @var array $candidates */
/** @var array $boardRoles */
?>
<h2>Edit board</h2>

<form method="post" action="/admin/boards/update" class="form">
    <input type="hidden" name="id" value="<?=$board['id']?>">
    <div class="grid-3">
        <label>Name <input name="name" value="<?=htmlspecialchars($board['name'])?>" required></label>
        <label>Key <input name="board_key" value="<?=htmlspecialchars($board['board_key'])?>" maxlength="16" required></label>
    </div>
    <a class="btn" href="/admin/boards">← Back</a>
    <button class="btn">Save</button>
</form>

<hr>

<h3>Members</h3>
<table class="table table-center">
    <thead><tr><th>User</th><th>Email</th><th>Role</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($members as $m): ?>
        <tr>
            <td><?=htmlspecialchars($m['full_name'])?></td>
            <td><?=htmlspecialchars($m['email'])?></td>
            <td>
                <form class="inline" method="post" action="/admin/boards/member/add">
                    <input type="hidden" name="board_id" value="<?=$board['id']?>">
                    <input type="hidden" name="user_id" value="<?=$m['user_id']?>">
                    <select name="role_id" onchange="this.form.submit()">
                        <?php foreach ($boardRoles as $r): ?>
                            <option value="<?=$r['id']?>" <?=$r['id']==$m['role_id']?'selected':''?>><?=$r['name']?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </td>
            <td>
                <form class="inline" method="post" action="/admin/boards/member/remove" onsubmit="return confirm('Remove user from board?')">
                    <input type="hidden" name="board_id" value="<?=$board['id']?>">
                    <input type="hidden" name="user_id" value="<?=$m['user_id']?>">
                    <button class="btn danger small">Remove</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<h4>Add member</h4>
<form class="form" method="post" action="/admin/boards/member/add">
    <input type="hidden" name="board_id" value="<?=$board['id']?>">
    <div class="grid-3">
        <label>User
            <select name="user_id" required>
                <option value="">— choose —</option>
                <?php foreach ($candidates as $u): ?>
                    <option value="<?=$u['id']?>"><?=htmlspecialchars($u['full_name'])?> (<?=$u['email']?>)</option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Role
            <select name="role_id">
                <?php foreach ($boardRoles as $r): ?>
                    <option value="<?=$r['id']?>"><?=$r['name']?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>
    <button class="btn">Add</button>
</form>
<hr>
<h3>Epics</h3>

<table class="table table-center">
    <thead>
    <tr>
        <th>ID</th>
        <th style="min-width:220px;">Title</th>
        <th style="min-width:180px;">Status</th>
        <th style="min-width:220px;">Owner</th>
        <th style="min-width:260px;">Description</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach (($epics ?? []) as $e): ?>
        <tr>
            <td><?=$e['id']?></td>

            <td>
                <form class="inline" method="post" action="/admin/boards/epic/update">
                    <input type="hidden" name="board_id" value="<?=$board['id']?>">
                    <input type="hidden" name="id" value="<?=$e['id']?>">
                    <input type="text" name="title" value="<?=htmlspecialchars($e['title'])?>" style="width:100%;">
            </td>

            <td>
                <select name="status_id">
                    <?php foreach ($statuses as $s): ?>
                        <option value="<?=$s['id']?>" <?=$s['id']==$e['status_id']?'selected':''?>><?=$s['name']?></option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td>
                <select name="owner_user_id">
                    <option value="">— None —</option>
                    <?php foreach ($members as $m): ?>
                        <option value="<?=$m['user_id']?>" <?=$m['user_id']==(int)$e['owner_user_id']?'selected':''?>>
                            <?=htmlspecialchars($m['full_name'])?> (<?=$m['role_name']?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>

            <td>
                <input type="text" name="description" value="<?=htmlspecialchars($e['description'] ?? '')?>" style="width:100%;">
            </td>

            <td>
                <button class="btn small">Save</button>
                </form>
                <form class="inline" method="post" action="/admin/boards/epic/delete" onsubmit="return confirm('Delete epic with its tasks?')">
                    <input type="hidden" name="board_id" value="<?=$board['id']?>">
                    <input type="hidden" name="id" value="<?=$e['id']?>">
                    <button class="btn danger small">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
