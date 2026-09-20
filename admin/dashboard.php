<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_role('ADMIN');
$db = db();
$stats = [
    'Career Fairs' => $db->query('SELECT COUNT(*) FROM career_fair')->fetchColumn(),
    'Students' => $db->query('SELECT COUNT(*) FROM student')->fetchColumn(),
    'Companies' => $db->query('SELECT COUNT(*) FROM company')->fetchColumn(),
    'Applications' => $db->query('SELECT COUNT(*) FROM application')->fetchColumn(),
];
$recent = $db->query("SELECT * FROM (SELECT fair_id, fair_title, event_date, venue, registration_deadline FROM career_fair ORDER BY event_date) WHERE ROWNUM <= 6")->fetchAll();
$pendingCompanies = $db->query("SELECT COUNT(*) FROM company_participation WHERE approval_status='PENDING'")->fetchColumn();
$openJobs = $db->query("SELECT COUNT(*) FROM job_vacancy WHERE vacancy_status='OPEN'")->fetchColumn();
$pageTitle = 'Admin Dashboard';
include dirname(__DIR__) . '/includes/header.php';
?>
<div class="grid grid-4">
<?php foreach ($stats as $label => $value): ?><div class="card stat"><div class="stat-label"><?= e($label) ?></div><div class="stat-value"><?= e((string)$value) ?></div></div><?php endforeach; ?>
</div>
<div class="grid grid-2" style="margin-top:18px">
    <div class="card"><div class="stat-label">Pending Company Approvals</div><div class="stat-value"><?= e((string)$pendingCompanies) ?></div><a href="<?= e(url('admin/companies.php')) ?>">Review participation requests →</a></div>
    <div class="card"><div class="stat-label">Open Job Vacancies</div><div class="stat-value"><?= e((string)$openJobs) ?></div><a href="<?= e(url('admin/jobs.php')) ?>">View jobs →</a></div>
</div>
<div class="section-title"><h2>Career Fairs from Oracle</h2><a class="btn primary" href="<?= e(url('admin/fairs.php')) ?>">Manage Fairs</a></div>
<div class="table-wrap"><table><thead><tr><th>Fair</th><th>Date</th><th>Venue</th><th>Registration Deadline</th></tr></thead><tbody>
<?php foreach ($recent as $r): ?><tr><td><strong><?= e($r['fair_title']) ?></strong></td><td><?= e($r['event_date']) ?></td><td><?= e($r['venue']) ?></td><td><?= e($r['registration_deadline']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php include dirname(__DIR__) . '/includes/footer.php'; ?>
