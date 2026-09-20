<?php
require_once dirname(__DIR__) . '/includes/auth.php';require_role('STUDENT');$pdo=db();$sid=current_user()['related_id'];
$st=$pdo->prepare('SELECT a.*,j.position_title,c.company_name,cf.fair_title FROM application a JOIN job_vacancy j ON j.job_id=a.job_id JOIN company c ON c.company_id=j.company_id JOIN career_fair cf ON cf.fair_id=j.fair_id WHERE a.student_id=? ORDER BY a.application_id DESC');$st->execute([$sid]);$rows=$st->fetchAll();
$pageTitle='My Applications';include dirname(__DIR__).'/includes/header.php';?>
<div class="table-wrap"><table><thead><tr><th>Position</th><th>Company</th><th>Fair</th><th>Applied</th><th>Status</th><th>Resume</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><strong><?= e($r['position_title']) ?></strong></td><td><?= e($r['company_name']) ?></td><td><?= e($r['fair_title']) ?></td><td><?= e($r['application_date']) ?></td><td><span class="badge <?= badge_class($r['application_status']) ?>"><?= e($r['application_status']) ?></span></td><td><?= e($r['submitted_resume']) ?></td></tr><?php endforeach;?></tbody></table></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
