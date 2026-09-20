<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_role('COMPANY_REP');
$db=db(); $rid=current_user()['related_id'];
$st=$db->prepare('SELECT company_id FROM company_representative WHERE representative_id=?');$st->execute([$rid]);$cid=(int)$st->fetchColumn();
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf(); $aid=(int)$_POST['application_id']; $status=post('application_status');
    $allowed=['SUBMITTED','UNDER_REVIEW','SHORTLISTED','REJECTED','SELECTED'];
    try {
        if(in_array($status,$allowed,true)){
            $q=$db->prepare('UPDATE application SET application_status=? WHERE application_id=? AND job_id IN (SELECT job_id FROM job_vacancy WHERE company_id=?)');
            $q->execute([$status,$aid,$cid]);
            flash('success','Application status updated in Oracle.');
        }
    } catch(Throwable $e) { flash('error','Update failed: '.oracle_error_message($e)); }
    redirect('company/applications.php');
}
$q=$db->prepare('SELECT a.*,s.student_name,s.email,s.department,s.graduation_year,j.position_title FROM application a JOIN student s ON s.student_id=a.student_id JOIN job_vacancy j ON j.job_id=a.job_id WHERE j.company_id=? ORDER BY a.application_id DESC');$q->execute([$cid]);$rows=$q->fetchAll();
$pageTitle='Review Applications';include dirname(__DIR__).'/includes/header.php';?>
<div class="table-wrap"><table><thead><tr><th>Student</th><th>Position</th><th>Date</th><th>Current Status</th><th>Update</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><strong><?= e($r['student_name']) ?></strong><br><span class="muted"><?= e($r['email']) ?><br><?= e($r['department']) ?> · <?= e((string)$r['graduation_year']) ?></span></td><td><?= e($r['position_title']) ?><br><span class="help"><?= e(mb_strimwidth($r['cover_letter']??'',0,65,'...')) ?></span></td><td><?= e($r['application_date']) ?></td><td><span class="badge <?= badge_class($r['application_status']) ?>"><?= e($r['application_status']) ?></span></td><td><form method="post" class="actions"><?= csrf_field() ?><input type="hidden" name="application_id" value="<?= e((string)$r['application_id']) ?>"><select name="application_status" style="width:auto"><option>SUBMITTED</option><option>UNDER_REVIEW</option><option>SHORTLISTED</option><option>REJECTED</option><option>SELECTED</option></select><button class="btn small primary">Update</button></form></td></tr><?php endforeach;?></tbody></table></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
