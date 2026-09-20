<?php
require_once dirname(__DIR__) . '/includes/auth.php'; require_role('STUDENT');$db=db();$sid=current_user()['related_id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();$jobId=(int)$_POST['job_id'];
    try{
        $check=$db->prepare('SELECT COUNT(*) FROM application WHERE student_id=? AND job_id=?');$check->execute([$sid,$jobId]);if((int)$check->fetchColumn()>0)throw new RuntimeException('You already applied for this job.');
        $resume=upload_resume('resume'); if(!$resume){$s=$db->prepare('SELECT resume_info FROM student WHERE student_id=?');$s->execute([$sid]);$resume=$s->fetchColumn();}
        $db->prepare("INSERT INTO application(application_id,student_id,job_id,application_date,submitted_resume,cover_letter,application_status) VALUES(seq_application.NEXTVAL,?,?,TRUNC(SYSDATE),?,?,'SUBMITTED')")->execute([$sid,$jobId,$resume,post('cover_letter')]);
        flash('success','Application submitted to Oracle successfully.');
    }catch(Throwable $e){flash('error','Application failed: '.oracle_error_message($e));}redirect('student/jobs.php');
}
$st=$db->prepare("SELECT j.*,c.company_name,cf.fair_title,cf.event_date,(SELECT COUNT(*) FROM application a WHERE a.student_id=? AND a.job_id=j.job_id) already_applied FROM job_vacancy j JOIN company c ON c.company_id=j.company_id JOIN career_fair cf ON cf.fair_id=j.fair_id WHERE j.vacancy_status='OPEN' ORDER BY j.application_deadline");$st->execute([$sid]);$rows=$st->fetchAll();
$skillStmt=$db->prepare('SELECT s.skill_name FROM job_required_skill jrs JOIN skill s ON s.skill_id=jrs.skill_id WHERE jrs.job_id=? ORDER BY s.skill_name');
foreach($rows as &$row){$skillStmt->execute([(int)$row['job_id']]);$names=[];foreach($skillStmt->fetchAll() as $sr)$names[]=$sr['skill_name'];$row['skills']=implode(', ',$names);}unset($row);
$pageTitle='Browse Jobs';include dirname(__DIR__).'/includes/header.php';?>
<div class="grid grid-2"><?php foreach($rows as $j):?><div class="card"><div class="split"><h2 style="margin:0"><?= e($j['position_title']) ?></h2><span class="badge success"><?= e($j['vacancy_status']) ?></span></div><p><strong><?= e($j['company_name']) ?></strong><br><span class="muted"><?= e($j['fair_title']) ?> · <?= e($j['employment_type']) ?></span></p><p><?= e($j['job_description']) ?></p><p class="help"><strong>Required skills:</strong> <?= e($j['skills']?:'Not specified') ?><br><strong>Deadline:</strong> <?= e(display_date($j['application_deadline'])) ?></p><?php if((int)$j['already_applied']>0):?><span class="badge info">Already Applied</span><?php else:?><details><summary class="btn primary" style="display:inline-flex">Apply Now</summary><form method="post" enctype="multipart/form-data" style="margin-top:14px"><?= csrf_field() ?><input type="hidden" name="job_id" value="<?= e((string)$j['job_id']) ?>"><div class="field"><label>Cover Letter</label><textarea name="cover_letter" required></textarea></div><div class="field" style="margin-top:10px"><label>Resume (optional)</label><input type="file" name="resume" accept=".pdf,.doc,.docx"><div class="help">If omitted, your saved Resume_Info value is used.</div></div><button class="btn success" style="margin-top:12px">Submit Application</button></form></details><?php endif;?></div><?php endforeach;?></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
