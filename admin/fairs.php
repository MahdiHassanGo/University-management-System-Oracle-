<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_role('ADMIN');
$db = db();
$adminId = current_user()['related_id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = post('action');
    try {
        if ($action === 'save') {
            $id = (int)($_POST['fair_id'] ?? 0);
            $title = post('fair_title');
            $eventDate = normalize_date(post('event_date'));
            $venue = post('venue');
            $deadline = normalize_date(post('registration_deadline'));
            $startTime = post('start_time');
            $endTime = post('end_time');
            $description = post('description');
            if (!$eventDate || !$deadline) throw new RuntimeException('Event date and registration deadline are required.');
            if (strtotime($deadline) > strtotime($eventDate)) throw new RuntimeException('Registration deadline must be on or before the event date.');
            if ($id > 0) {
                $sql = "UPDATE career_fair SET administrator_id=?, fair_title=?, event_date=TO_DATE(?,'YYYY-MM-DD'), venue=?, start_time=TO_DATE(? || ' ' || NVL(?, '09:00'),'YYYY-MM-DD HH24:MI'), end_time=TO_DATE(? || ' ' || NVL(?, '17:00'),'YYYY-MM-DD HH24:MI'), description=?, registration_deadline=TO_DATE(?,'YYYY-MM-DD') WHERE fair_id=?";
                $db->prepare($sql)->execute([$adminId,$title,$eventDate,$venue,$eventDate,$startTime,$eventDate,$endTime,$description,$deadline,$id]);
                flash('success', 'Career fair updated in Oracle.');
            } else {
                $sql = "INSERT INTO career_fair(fair_id,administrator_id,fair_title,event_date,venue,start_time,end_time,description,registration_deadline) VALUES(seq_career_fair.NEXTVAL,?,?,TO_DATE(?,'YYYY-MM-DD'),?,TO_DATE(? || ' ' || NVL(?, '09:00'),'YYYY-MM-DD HH24:MI'),TO_DATE(? || ' ' || NVL(?, '17:00'),'YYYY-MM-DD HH24:MI'),?,TO_DATE(?,'YYYY-MM-DD'))";
                $db->prepare($sql)->execute([$adminId,$title,$eventDate,$venue,$eventDate,$startTime,$eventDate,$endTime,$description,$deadline]);
                flash('success', 'Career fair created in Oracle.');
            }
        } elseif ($action === 'delete') {
            $db->prepare('DELETE FROM career_fair WHERE fair_id=?')->execute([(int)$_POST['fair_id']]);
            flash('success', 'Career fair deleted from Oracle.');
        }
    } catch (Throwable $e) { flash('error', 'Operation failed: ' . oracle_error_message($e)); }
    redirect('admin/fairs.php');
}
$edit = null;
if (!empty($_GET['edit'])) { $st=$db->prepare('SELECT * FROM career_fair WHERE fair_id=?');$st->execute([(int)$_GET['edit']]);$edit=$st->fetch(); }
$fairs = $db->query('SELECT cf.*, a.administrator_name, (SELECT COUNT(*) FROM fair_registration fr WHERE fr.fair_id=cf.fair_id) registrations FROM career_fair cf JOIN administrator a ON a.administrator_id=cf.administrator_id ORDER BY cf.event_date')->fetchAll();
$pageTitle='Career Fairs'; include dirname(__DIR__).'/includes/header.php';
?>
<div class="card form-card"><h2 style="margin-top:0"><?= $edit ? 'Edit Career Fair' : 'Add Career Fair' ?></h2>
<form method="post"><input type="hidden" name="action" value="save"><input type="hidden" name="fair_id" value="<?= e((string)($edit['fair_id']??'')) ?>"><?= csrf_field() ?>
<div class="form-grid">
<div class="field"><label>Fair Title</label><input name="fair_title" required value="<?= e($edit['fair_title']??'') ?>"></div>
<div class="field"><label>Event Date</label><input type="date" name="event_date" required value="<?= e(input_date($edit['event_date']??'')) ?>"></div>
<div class="field"><label>Venue</label><input name="venue" value="<?= e($edit['venue']??'') ?>"></div>
<div class="field"><label>Registration Deadline</label><input type="date" name="registration_deadline" required value="<?= e(input_date($edit['registration_deadline']??'')) ?>"></div>
<div class="field"><label>Start Time</label><input type="time" name="start_time" value="<?= e(isset($edit['start_time']) ? date('H:i', strtotime($edit['start_time'])) : '09:00') ?>"></div>
<div class="field"><label>End Time</label><input type="time" name="end_time" value="<?= e(isset($edit['end_time']) ? date('H:i', strtotime($edit['end_time'])) : '17:00') ?>"></div>
<div class="field full"><label>Description</label><textarea name="description"><?= e($edit['description']??'') ?></textarea></div>
</div><div class="actions" style="margin-top:14px"><button class="btn primary">Save Fair</button><?php if($edit):?><a class="btn ghost" href="<?= e(url('admin/fairs.php')) ?>">Cancel</a><?php endif;?></div></form></div>
<div class="section-title"><h2>All Career Fairs</h2></div><div class="table-wrap"><table><thead><tr><th>Fair</th><th>Date / Venue</th><th>Registrations</th><th>Deadline</th><th>Actions</th></tr></thead><tbody>
<?php foreach($fairs as $f):?><tr><td><strong><?= e($f['fair_title']) ?></strong><br><span class="muted">Managed by <?= e($f['administrator_name']) ?></span></td><td><?= e($f['event_date']) ?><br><?= e($f['venue']) ?></td><td><?= e((string)$f['registrations']) ?></td><td><?= e($f['registration_deadline']) ?></td><td><div class="actions"><a class="btn small" href="?edit=<?= e((string)$f['fair_id']) ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this fair? Related records may prevent deletion.');"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="fair_id" value="<?= e((string)$f['fair_id']) ?>"><button class="btn small danger">Delete</button></form></div></td></tr><?php endforeach;?>
</tbody></table></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
