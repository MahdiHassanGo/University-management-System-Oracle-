<?php
require_once dirname(__DIR__) . '/includes/auth.php';require_role('COMPANY_REP');$db=db();$rid=current_user()['related_id'];
$st=$db->prepare('SELECT cr.*,c.* FROM company_representative cr JOIN company c ON c.company_id=cr.company_id WHERE cr.representative_id=?');$st->execute([$rid]);$rep=$st->fetch();$cid=(int)$rep['company_id'];
$stats=[];foreach([
'Job Vacancies'=>'SELECT COUNT(*) FROM job_vacancy WHERE company_id=?',
'Applications'=>'SELECT COUNT(*) FROM application a JOIN job_vacancy j ON j.job_id=a.job_id WHERE j.company_id=?',
'Interviews'=>'SELECT COUNT(*) FROM interview i JOIN application a ON a.application_id=i.application_id JOIN job_vacancy j ON j.job_id=a.job_id WHERE j.company_id=?',
'Fair Requests'=>'SELECT COUNT(*) FROM company_participation WHERE company_id=?'
] as $k=>$q){$x=$db->prepare($q);$x->execute([$cid]);$stats[$k]=$x->fetchColumn();}
$jobs=$db->prepare('SELECT * FROM (SELECT j.*,cf.fair_title,(SELECT COUNT(*) FROM application a WHERE a.job_id=j.job_id) apps FROM job_vacancy j JOIN career_fair cf ON cf.fair_id=j.fair_id WHERE j.company_id=? ORDER BY j.job_id DESC) WHERE ROWNUM <= 5');$jobs->execute([$cid]);$rows=$jobs->fetchAll();
$pageTitle='Company Dashboard';include dirname(__DIR__).'/includes/header.php';?>
<div class="card"><h2 style="margin:0 0 4px"><?= e($rep['company_name']) ?></h2><div class="muted"><?= e($rep['representative_name']) ?> · <?= e($rep['designation']) ?></div></div>
<div class="grid grid-4" style="margin-top:18px"><?php foreach($stats as $k=>$v):?><div class="card stat"><div class="stat-label"><?= e($k) ?></div><div class="stat-value"><?= e((string)$v) ?></div></div><?php endforeach;?></div>
<div class="section-title"><h2>Recent Job Vacancies</h2><a class="btn primary" href="<?= e(url('company/jobs.php')) ?>">Manage Jobs</a></div><div class="table-wrap"><table><thead><tr><th>Position</th><th>Fair</th><th>Status</th><th>Applications</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?= e($r['position_title']) ?></td><td><?= e($r['fair_title']) ?></td><td><span class="badge <?= badge_class($r['vacancy_status']) ?>"><?= e($r['vacancy_status']) ?></span></td><td><?= e((string)$r['apps']) ?></td></tr><?php endforeach;?></tbody></table></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
