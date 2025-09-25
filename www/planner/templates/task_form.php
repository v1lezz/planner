<h2>Create task</h2>
<?php if (!empty($error)): ?><div class="alert error"><?=$error?></div><?php endif; ?>
<form method="post" class="form">
    <input type="hidden" name="board_id" value="<?=$board ? $board['id'] : 0?>">

    <label>Title <input type="text" name="title" required></label>
    <label>Description <textarea name="description" rows="4"></textarea></label>

    <div class="grid-2">
        <label>Status
            <select name="status_id">
                <?php foreach ($statuses as $s): ?><option value="<?=$s['id']?>"><?=$s['name']?></option><?php endforeach; ?>
            </select>
        </label>
        <label>Type
            <select name="type_id">
                <?php foreach ($types as $t): ?><option value="<?=$t['id']?>"><?=$t['name']?></option><?php endforeach; ?>
            </select>
        </label>
    </div>

    <div class="grid-2">
        <fieldset>
            <legend>Priority</legend>
            <?php foreach ($priorities as $p): ?>
                <label class="inline"><input type="radio" name="priority_id" value="<?=$p['id']?>" <?=$p['name']=='Medium'?'checked':''?>> <?=$p['name']?></label>
            <?php endforeach; ?>
        </fieldset>
        <label>Story points <input type="number" name="story_points" min="1" max="21"></label>
    </div>

    <div class="grid-2">
        <label>Epic
            <select name="epic_id">
                <option value="">— None —</option>
                <?php foreach (($epics ?? []) as $e): ?>
                    <option value="<?=$e['id']?>"><?=htmlspecialchars($e['title'])?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Assignee
            <select name="assignee_id">
                <option value="">— Unassigned —</option>
                <?php foreach (($members ?? []) as $m): ?>
                    <option value="<?=$m['user_id']?>"><?=htmlspecialchars($m['full_name'])?> (<?=$m['role_name']?>)</option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <div class="grid-2">
        <label>Due date <input type="date" name="due_date" required></label>
        <label class="inline"><input type="checkbox" name="notify_email"> Notify assignee by email</label>
    </div>

    <button class="btn">Create</button>
</form>
