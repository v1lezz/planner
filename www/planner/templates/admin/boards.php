<?php /** @var array $boards */ ?>
<h2>Boards</h2>

<form method="post" action="/admin/boards/create" class="form">
    <h3>Create board</h3>
    <div class="grid-3">
        <label>Name <input name="name" required></label>
        <label>Key <input name="board_key" maxlength="16" required></label>
    </div>
    <button class="btn">Create</button>
</form>

<table class="table table-center">
    <thead><tr><th>ID</th><th>Name</th><th>Key</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach ($boards as $b): ?>
        <tr>
            <td><?=$b['id']?></td>
            <td><?=htmlspecialchars($b['name'])?></td>
            <td><?=htmlspecialchars($b['board_key'])?></td>
            <td>
                <a class="btn small" href="/admin/boards/edit?id=<?=$b['id']?>">Edit</a>
                <form class="inline" method="post" action="/admin/boards/delete" onsubmit="return confirm('Delete board with all tasks/epics?')">
                    <input type="hidden" name="id" value="<?=$b['id']?>">
                    <button class="btn danger small">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
