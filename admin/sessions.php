<?php
require_once dirname(__DIR__) . '/includes/auth.php'; require_role('ADMIN'); $db=db();
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf(); $action=post('action');
    try{
        if($action==='save'){
            $id=(int)($_POST['session_id']??0); $dt=post('start_time');
            if($id){
                $db->prepare("UPDATE fair_session SET fair_id=?,session_title=?,speaker=?,venue=?,start_time=TO_DATE(?,'YYYY-MM-DD HH24:MI') WHERE session_id=?")->execute([(int)$_POST['fair_id'],post('session_title'),post('speaker'),post('venue'),str_replace('T',' ',$dt),$id]);
                flash('success','Session updated in Oracle.');
            } else {
                $db->prepare("INSERT INTO fair_session(session_id,fair_id,session_title,speaker,venue,start_time) VALUES(seq_session.NEXTVAL,?,?,?,?,TO_DATE(?,'YYYY-MM-DD HH24:MI'))")->execute([(int)$_POST['fair_id'],post('session_title'),post('speaker'),post('venue'),str_replace('T',' ',$dt)]);
                flash('success','Session created in Oracle.');
            }
        }elseif($action==='delete'){$db->prepare('DELETE FROM fair_session WHERE session_id=?')->execute([(int)$_POST['session_id']]);flash('success','Session deleted from Oracle.');}
    }catch(Throwable $e){flash('error','Session operation failed: '.oracle_error_message($e));} redirect('admin/sessions.php');
}
$edit=null;if(!empty($_GET['edit'])){$st=$db->prepare('SELECT * FROM fair_session WHERE session_id=?');$st->execute([(int)$_GET['edit']]);$edit=$st->fetch();}
$fairs=$db->query('SELECT fair_id,fair_title FROM career_fair ORDER BY event_date')->fetchAll();
$rows=$db->query('SELECT fs.*,cf.fair_title,(SELECT COUNT(*) FROM session_attendance sa WHERE sa.session_id=fs.session_id) attendees FROM fair_session fs JOIN career_fair cf ON cf.fair_id=fs.fair_id ORDER BY fs.start_time')->fetchAll();
$pageTitle='Career Fair Sessions';include dirname(__DIR__).'/includes/header.php';?>
<div class="card form-card"><h2 style="margin-top:0"><?= $edit?'Edit Session':'Add Session' ?></h2><form method="post"><input type="hidden" name="action" value="save"><input type="hidden" name="session_id" value="<?= e((string)($edit['session_id']??'')) ?>"><?= csrf_field() ?><div class="form-grid"><div class="field"><label>Career Fair</label><select name="fair_id" required><?php foreach($fairs as $f):?><option value="<?= e((string)$f['fair_id']) ?>" <?= (($edit['fair_id']??null)==$f['fair_id'])?'selected':'' ?>><?= e($f['fair_title']) ?></option><?php endforeach;?></select></div><div class="field"><label>Session Title</label><input name="session_title" required value="<?= e($edit['session_title']??'') ?>"></div><div class="field"><label>Speaker</label><input name="speaker" value="<?= e($edit['speaker']??'') ?>"></div><div class="field"><label>Venue</label><input name="venue" value="<?= e($edit['venue']??'') ?>"></div><div class="field"><label>Start Time</label><input type="datetime-local" name="start_time" required value="<?= e(input_datetime($edit['start_time']??'')) ?>"></div></div><div class="actions" style="margin-top:14px"><button class="btn primary">Save Session</button><?php if($edit):?><a class="btn ghost" href="<?= e(url('admin/sessions.php')) ?>">Cancel</a><?php endif;?></div></form></div>
<div class="section-title"><h2>Scheduled Sessions</h2></div><div class="table-wrap"><table><thead><tr><th>Session</th><th>Fair</th><th>Speaker / Venue</th><th>Start</th><th>Attendees</th><th>Actions</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><strong><?= e($r['session_title']) ?></strong></td><td><?= e($r['fair_title']) ?></td><td><?= e($r['speaker']) ?><br><span class="muted"><?= e($r['venue']) ?></span></td><td><?= e($r['start_time']) ?></td><td><?= e((string)$r['attendees']) ?></td><td><div class="actions"><a class="btn small" href="?edit=<?= e((string)$r['session_id']) ?>">Edit</a><form method="post" onsubmit="return confirm('Delete session?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="session_id" value="<?= e((string)$r['session_id']) ?>"><button class="btn small danger">Delete</button></form></div></td></tr><?php endforeach;?></tbody></table></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
