<?php
require_once dirname(__DIR__) . '/includes/auth.php';require_role('STUDENT');$db=db();$sid=current_user()['related_id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$sessionId=(int)$_POST['session_id'];$action=post('action');
 try{
  if($action==='register'){
   $db->prepare("INSERT INTO session_attendance(student_id,session_id,attendance_status,check_in_time) VALUES(?,?,'REGISTERED',NULL)")->execute([$sid,$sessionId]);
   flash('success','Session registration saved in Oracle.');
  }elseif($action==='cancel'){
   $db->prepare('DELETE FROM session_attendance WHERE student_id=? AND session_id=?')->execute([$sid,$sessionId]);
   flash('success','Session registration cancelled in Oracle.');
  }
 }catch(Throwable $e){flash('error','Session action failed: '.oracle_error_message($e));}redirect('student/sessions.php');
}
$st=$db->prepare('SELECT fs.*,cf.fair_title,sa.attendance_status FROM fair_session fs JOIN career_fair cf ON cf.fair_id=fs.fair_id LEFT JOIN session_attendance sa ON sa.session_id=fs.session_id AND sa.student_id=? ORDER BY fs.start_time');$st->execute([$sid]);$rows=$st->fetchAll();
$pageTitle='Career Fair Sessions';include dirname(__DIR__).'/includes/header.php';?>
<div class="grid grid-2"><?php foreach($rows as $r):?><div class="card"><div class="split"><h2 style="margin:0"><?= e($r['session_title']) ?></h2><?php if($r['attendance_status']):?><span class="badge <?= badge_class($r['attendance_status']) ?>"><?= e($r['attendance_status']) ?></span><?php endif;?></div><p><strong><?= e($r['fair_title']) ?></strong><br><?= e($r['speaker']) ?> · <?= e($r['venue']) ?></p><p class="muted"><?= e(display_date($r['start_time'])) ?> at <?= e(display_time($r['start_time'])) ?></p><?php if($r['attendance_status']):?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="cancel"><input type="hidden" name="session_id" value="<?= e((string)$r['session_id']) ?>"><button class="btn small danger">Cancel</button></form><?php else:?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="register"><input type="hidden" name="session_id" value="<?= e((string)$r['session_id']) ?>"><button class="btn primary">Register</button></form><?php endif;?></div><?php endforeach;?></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
