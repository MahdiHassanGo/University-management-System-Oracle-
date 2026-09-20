<?php
require_once dirname(__DIR__) . '/includes/auth.php'; require_role('ADMIN'); $pdo=db();
$rows=$pdo->query('SELECT a.*, s.student_name, s.email student_email, j.position_title, c.company_name FROM application a JOIN student s ON s.student_id=a.student_id JOIN job_vacancy j ON j.job_id=a.job_id JOIN company c ON c.company_id=j.company_id ORDER BY a.application_id DESC')->fetchAll();
$pageTitle='Applications'; include dirname(__DIR__).'/includes/header.php'; ?>
<div class="table-wrap"><table><thead><tr><th>Student</th><th>Position</th><th>Company</th><th>Date</th><th>Status</th><th>Cover Letter</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><strong><?= e($r['student_name']) ?></strong><br><span class="muted"><?= e($r['student_email']) ?></span></td><td><?= e($r['position_title']) ?></td><td><?= e($r['company_name']) ?></td><td><?= e($r['application_date']) ?></td><td><span class="badge <?= badge_class($r['application_status']) ?>"><?= e($r['application_status']) ?></span></td><td><?= e(mb_strimwidth($r['cover_letter']??'',0,70,'...')) ?></td></tr><?php endforeach;?></tbody></table></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
