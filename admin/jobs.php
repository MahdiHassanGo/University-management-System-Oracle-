<?php
require_once dirname(__DIR__) . '/includes/auth.php'; require_role('ADMIN'); $pdo=db();
$jobs=$pdo->query('SELECT j.*, c.company_name, cf.fair_title, (SELECT COUNT(*) FROM application a WHERE a.job_id=j.job_id) total_applications FROM job_vacancy j JOIN company c ON c.company_id=j.company_id JOIN career_fair cf ON cf.fair_id=j.fair_id ORDER BY j.job_id DESC')->fetchAll();
$pageTitle='Job Vacancies'; include dirname(__DIR__).'/includes/header.php'; ?>
<div class="table-wrap"><table><thead><tr><th>Position</th><th>Company</th><th>Career Fair</th><th>Type</th><th>Deadline</th><th>Status</th><th>Applications</th></tr></thead><tbody>
<?php foreach($jobs as $j):?><tr><td><strong><?= e($j['position_title']) ?></strong></td><td><?= e($j['company_name']) ?></td><td><?= e($j['fair_title']) ?></td><td><?= e($j['employment_type']) ?></td><td><?= e($j['application_deadline']) ?></td><td><span class="badge <?= badge_class($j['vacancy_status']) ?>"><?= e($j['vacancy_status']) ?></span></td><td><?= e((string)$j['total_applications']) ?></td></tr><?php endforeach;?>
</tbody></table></div><?php include dirname(__DIR__).'/includes/footer.php'; ?>
