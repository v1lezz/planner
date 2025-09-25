<h2>Create epic</h2>
<?php if (!empty($error)): ?><div class="alert error"><?=$error?></div><?php endif; ?>

<form method="post" class="form" action="/epic/create">
    <input type="hidden" name="board_id" value="<?=$board ? $board['id'] : 0?>">

    <label>Title <input type="text" name="title" required></label>
    <label>Description <textarea name="description" rows="4"></textarea></label>

    <div class="grid-2">
        <label>Status
            <select name="status_id">
                <?php foreach ($statuses as $s): ?>
                    <option value="<?=$s['id']?>"><?=$s['name']?></option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Owner
            <select name="owner_user_id">
                <option value="">— None —</option>
                <?php foreach ($members as $m): ?>
                    <option value="<?=$m['user_id']?>"><?=htmlspecialchars($m['full_name'])?> (<?=$m['role_name']?>)</option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <button class="btn">Create epic</button>
    <a class="btn" href="/board?id=<?=$board['id']?>">Cancel</a>
</form>
