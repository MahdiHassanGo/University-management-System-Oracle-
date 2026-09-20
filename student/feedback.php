<?php
require_once dirname(__DIR__) . '/includes/auth.php';require_role('STUDENT');$db=db();$sid=current_user()['related_id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$fid=(int)$_POST['fair_id'];$rating=(int)$_POST['rating'];
 try{
  if($rating<1||$rating>5)throw new RuntimeException('Rating must be between 1 and 5.');
  $db->prepare('INSERT INTO feedback(feedback_id,student_id,fair_id,submission_date,rating,comments) VALUES(seq_feedback.NEXTVAL,?,?,TRUNC(SYSDATE),?,?)')->execute([$sid,$fid,$rating,post('comments')]);
  flash('success','Feedback saved in Oracle.');
 }catch(Throwable $e){flash('error','Feedback failed: '.oracle_error_message($e));}redirect('student/feedback.php');
}
$fairs=$db->prepare('SELECT cf.fair_id,cf.fair_title FROM career_fair cf JOIN fair_registration fr ON fr.fair_id=cf.fair_id WHERE fr.student_id=? ORDER BY cf.event_date DESC');$fairs->execute([$sid]);$fairRows=$fairs->fetchAll();
$hist=$db->prepare('SELECT f.*,cf.fair_title FROM feedback f JOIN career_fair cf ON cf.fair_id=f.fair_id WHERE f.student_id=? ORDER BY f.feedback_id DESC');$hist->execute([$sid]);$rows=$hist->fetchAll();
$pageTitle='Feedback';include dirname(__DIR__).'/includes/header.php';?>
<div class="card form-card"><h2 style="margin-top:0">Submit Feedback</h2><form method="post"><?= csrf_field() ?><div class="form-grid"><div class="field"><label>Career Fair</label><select name="fair_id" required><?php foreach($fairRows as $f):?><option value="<?= e((string)$f['fair_id']) ?>"><?= e($f['fair_title']) ?></option><?php endforeach;?></select></div><div class="field"><label>Rating</label><select name="rating"><option value="5">5 - Excellent</option><option value="4">4 - Good</option><option value="3">3 - Average</option><option value="2">2 - Fair</option><option value="1">1 - Poor</option></select></div><div class="field full"><label>Comments</label><textarea name="comments" required></textarea></div></div><button class="btn primary" style="margin-top:14px">Submit Feedback</button></form></div>
<div class="section-title"><h2>My Previous Feedback</h2></div><div class="table-wrap"><table><thead><tr><th>Fair</th><th>Date</th><th>Rating</th><th>Comments</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?= e($r['fair_title']) ?></td><td><?= e(display_date($r['submission_date'])) ?></td><td><?= e((string)$r['rating']) ?>/5</td><td><?= e($r['comments']) ?></td></tr><?php endforeach;?></tbody></table></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
