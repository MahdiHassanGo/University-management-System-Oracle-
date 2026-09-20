<?php
require_once dirname(__DIR__) . '/includes/auth.php'; require_role('STUDENT'); $db=db(); $sid=current_user()['related_id'];
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();$fid=(int)$_POST['fair_id'];$action=post('action');
    try{
        if($action==='register'){
            $check=$db->prepare('SELECT registration_deadline FROM career_fair WHERE fair_id=?');$check->execute([$fid]);$deadline=$check->fetchColumn();
            if($deadline && strtotime((string)$deadline) < strtotime(date('Y-m-d'))) throw new RuntimeException('Registration deadline has passed.');
            $db->prepare("INSERT INTO fair_registration(student_id,fair_id,registration_date,registration_status) VALUES(?,?,TRUNC(SYSDATE),'REGISTERED')")->execute([$sid,$fid]);
            flash('success','Career fair registration saved in Oracle.');
        }elseif($action==='cancel'){
            $db->prepare('DELETE FROM fair_registration WHERE student_id=? AND fair_id=?')->execute([$sid,$fid]);
            flash('success','Registration cancelled in Oracle.');
        }
    }catch(Throwable $e){flash('error','Registration failed: '.oracle_error_message($e));}redirect('student/fairs.php');
}
$st=$db->prepare('SELECT cf.*,fr.registration_status,fr.registration_date FROM career_fair cf LEFT JOIN fair_registration fr ON fr.fair_id=cf.fair_id AND fr.student_id=? ORDER BY cf.event_date');$st->execute([$sid]);$rows=$st->fetchAll();
$pageTitle='Career Fairs';include dirname(__DIR__).'/includes/header.php';?>
<div class="grid grid-2"><?php foreach($rows as $r):?><div class="card"><div class="split"><h2 style="margin:0"><?= e($r['fair_title']) ?></h2><?php if($r['registration_status']):?><span class="badge <?= badge_class($r['registration_status']) ?>"><?= e($r['registration_status']) ?></span><?php endif;?></div><p class="muted"><?= e(display_date($r['event_date'])) ?> · <?= e($r['venue']) ?> · <?= e(display_time($r['start_time'])) ?>-<?= e(display_time($r['end_time'])) ?></p><p><?= e($r['description']) ?></p><p class="help">Registration deadline: <?= e(display_date($r['registration_deadline'])) ?></p><?php if($r['registration_status']):?><form method="post" onsubmit="return confirm('Cancel your registration?')"><?= csrf_field() ?><input type="hidden" name="action" value="cancel"><input type="hidden" name="fair_id" value="<?= e((string)$r['fair_id']) ?>"><button class="btn danger small">Cancel Registration</button></form><?php else:?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="register"><input type="hidden" name="fair_id" value="<?= e((string)$r['fair_id']) ?>"><button class="btn primary">Register for Fair</button></form><?php endif;?></div><?php endforeach;?></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
