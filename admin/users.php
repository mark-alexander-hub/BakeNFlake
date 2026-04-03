<?php
$currentPage = 'users';
require_once __DIR__ . '/../includes/admin_header.php';
$conn = getDBConnection();
$users = getAllUsers($conn);
?>

<h1 class="admin-page-title">Users</h1>

<div class="admin-table-wrap">
    <?php if (empty($users)): ?>
        <p style="color:#888;padding:20px 0;">No users found.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Joined</th>
                <th>Orders</th>
                <th>Loyalty Points</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= sanitize($u['name']) ?></td>
                <td><?= sanitize($u['email']) ?></td>
                <td><?= formatDate($u['created_at']) ?></td>
                <td><?= (int)$u['order_count'] ?></td>
                <td><?= (int)($u['loyalty_points'] ?? 0) ?></td>
                <td>
                    <?php if ($u['is_admin']): ?>
                        <span class="badge badge-admin">Admin</span>
                    <?php else: ?>
                        <span style="color:#888;">Customer</span>
                    <?php endif; ?>
                </td>
                <td>
                    <button class="admin-btn admin-btn-sm <?= $u['is_admin'] ? 'admin-btn-danger' : 'admin-btn-primary' ?> toggle-admin-btn"
                        data-id="<?= (int)$u['id'] ?>">
                        <?= $u['is_admin'] ? 'Remove Admin' : 'Make Admin' ?>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php
$conn->close();
require_once __DIR__ . '/../includes/admin_footer.php';
?>
