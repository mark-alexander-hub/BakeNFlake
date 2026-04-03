<?php
$currentPage = 'reservations';
require_once __DIR__ . '/../includes/admin_header.php';
$conn = getDBConnection();
$reservations = getAllReservations($conn);
?>

<h1 class="admin-page-title">Reservations</h1>

<div class="admin-table-wrap">
    <?php if (empty($reservations)): ?>
        <p style="color:#888;padding:20px 0;">No reservations yet.</p>
    <?php else: ?>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Date</th>
                <th>Guests</th>
                <th>Message</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $r): ?>
            <tr>
                <td><?= sanitize($r['name']) ?></td>
                <td><?= sanitize($r['reservation_date'] ?? $r['date'] ?? '') ?></td>
                <td><?= (int)($r['guests'] ?? $r['party_size'] ?? 0) ?></td>
                <td><?= sanitize($r['message'] ?? $r['special_requests'] ?? '') ?></td>
                <td>
                    <select class="status-select reservation-status-select" data-id="<?= (int)$r['id'] ?>">
                        <?php foreach (['pending','confirmed','cancelled'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($r['status'] ?? 'pending') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><?= formatDate($r['created_at']) ?></td>
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
