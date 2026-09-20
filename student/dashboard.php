<?php
require_once dirname(__DIR__) . '/includes/auth.php'; require_role('STUDENT'); $db=db(); $sid=current_user()['related_id'];
$st=$db->prepare('SELECT * FROM student WHERE student_id=?');$st->execute([$sid]);$student=$st->fetch();
$stats=[];foreach([
'Fair Registrations'=>'SELECT COUNT(*) FROM fair_registration WHERE student_id=?',
'Applications'=>'SELECT COUNT(*) FROM application WHERE student_id=?',
'Sessions'=>'SELECT COUNT(*) FROM session_attendance WHERE student_id=?',
'Feedback'=>'SELECT COUNT(*) FROM feedback WHERE student_id=?'
] as $k=>$q){$x=$db->prepare($q);$x->execute([$sid]);$stats[$k]=$x->fetchColumn();}
$up=$db->prepare('SELECT * FROM (SELECT a.application_status,j.position_title,c.company_name FROM application a JOIN job_vacancy j ON j.job_id=a.job_id JOIN company c ON c.company_id=j.company_id WHERE a.student_id=? ORDER BY a.application_id DESC) WHERE ROWNUM <= 5');$up->execute([$sid]);$apps=$up->fetchAll();
$pageTitle='Student Dashboard';include dirname(__DIR__).'/includes/header.php';?>
<div class="card"><h2 style="margin:0 0 4px">Welcome, <?= e($student['student_name']) ?></h2><div class="muted"><?= e($student['department']) ?> · Graduation <?= e((string)$student['graduation_year']) ?></div></div>
<div class="grid grid-4" style="margin-top:18px"><?php foreach($stats as $k=>$v):?><div class="card stat"><div class="stat-label"><?= e($k) ?></div><div class="stat-value"><?= e((string)$v) ?></div></div><?php endforeach;?></div>
<div class="section-title"><h2>Recent Applications</h2><a href="<?= e(url('student/jobs.php')) ?>" class="btn primary">Browse Jobs</a></div><div class="table-wrap"><table><thead><tr><th>Position</th><th>Company</th><th>Status</th></tr></thead><tbody><?php foreach($apps as $a):?><tr><td><?= e($a['position_title']) ?></td><td><?= e($a['company_name']) ?></td><td><span class="badge <?= badge_class($a['application_status']) ?>"><?= e($a['application_status']) ?></span></td></tr><?php endforeach;?></tbody></table></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
