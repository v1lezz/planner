<?php
$cols = $statuses;
?>
<div class="flex between">
    <h2><?=htmlspecialchars($board['name'])?></h2>
    <div class="flex">
        <a class="btn" href="/epic/create?board_id=<?=$board['id']?>">+ New Epic</a>
        <a class="btn" href="/task/create?board_id=<?=$board['id']?>">+ New Task</a>
    </div>
</div>

<div class="kanban">
    <?php foreach ($cols as $col): ?>
        <div class="kanban-col">
            <div class="kanban-col-title"><?=$col['name']?></div>
            <?php $tasks = $tasksByStatus[$col['id']] ?? []; ?>
            <?php foreach ($tasks as $t): ?>
                <div class="task-card">
                    <label class="selectbox">
                        <input type="checkbox" class="bulk" name="ids[]" value="<?=$t['id']?>" form="bulkForm">
                        <span></span>
                    </label>

                    <div class="task-title"><?=htmlspecialchars($t['title'])?></div>

                    <div class="small" style="margin-top:.35rem">
                        <strong>Type:</strong>
                        <form method="post" action="/task/change-type" class="inline" style="margin-left:.25rem">
                            <input type="hidden" name="task_id" value="<?=$t['id']?>">
                            <input type="hidden" name="board_id" value="<?=$board['id']?>">
                            <select name="type_id" onchange="this.form.submit()">
                                <?php foreach (($types ?? []) as $tp): ?>
                                    <option value="<?=$tp['id']?>" <?=$tp['id']==$t['type_id']?'selected':''?>><?=$tp['name']?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>

                    <div class="small" style="margin-top:.35rem">
                        <strong>Priority:</strong>
                        <form method="post" action="/task/change-priority" class="inline" style="margin-left:.25rem">
                            <input type="hidden" name="task_id" value="<?=$t['id']?>">
                            <input type="hidden" name="board_id" value="<?=$board['id']?>">
                            <select name="priority_id" onchange="this.form.submit()">
                                <?php foreach (($priorities ?? []) as $pr): ?>
                                    <option value="<?=$pr['id']?>" <?=$pr['id']==$t['priority_id']?'selected':''?>><?=$pr['name']?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>

                    <div class="small" style="margin-top:.35rem">
                        <strong>Assignee:</strong>
                        <form method="post" action="/task/change-assignee" class="inline" style="margin-left:.25rem">
                            <input type="hidden" name="task_id" value="<?=$t['id']?>">
                            <input type="hidden" name="board_id" value="<?=$board['id']?>">
                            <select name="assignee_id" onchange="this.form.submit()">
                                <option value="">— Unassigned —</option>
                                <?php foreach (($members ?? []) as $m): ?>
                                    <option value="<?=$m['user_id']?>" <?=(int)$t['assignee_id']===$m['user_id']?'selected':''?>>
                                        <?=htmlspecialchars($m['full_name'])?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>

                    <div class="small" style="margin-top:.35rem">
                        <strong>Status:</strong>
                        <form method="post" action="/task/change-status" class="inline" style="margin-top:.35rem">
                            <input type="hidden" name="task_id" value="<?=$t['id']?>">
                            <input type="hidden" name="board_id" value="<?=$board['id']?>">
                            <select name="status_id" onchange="this.form.submit()">
                                <?php foreach ($cols as $c): ?>
                                    <option value="<?=$c['id']?>" <?=$c['id']==$t['status_id']?'selected':''?>><?=$c['name']?></option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

<form id="bulkForm" method="post" action="/task/bulk" class="bulk-actions">
    <input type="hidden" name="board_id" value="<?=$board['id']?>">
    <label class="inline"><input type="checkbox" onclick="toggle(this,'.bulk')"> Select all</label>

    <select name="action" id="bulkAction">
        <option value="status">Change status</option>
        <option value="delete">Delete selected</option>
    </select>

    <select name="new_status_id" id="bulkStatus">
        <?php foreach ($cols as $c): ?><option value="<?=$c['id']?>"><?=$c['name']?></option><?php endforeach; ?>
    </select>

    <button class="btn">Apply</button>
</form>

<script>
    document.getElementById('bulkAction')?.addEventListener('change', function(){
        const isStatus = this.value === 'status';
        const bulkStatus = document.getElementById('bulkStatus');
        bulkStatus.disabled = !isStatus;
        bulkStatus.style.opacity = isStatus ? '1' : '0.5';
    });
</script>
