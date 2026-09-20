<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_role('ADMIN'); $db=db();
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf(); $action=post('action');
    try{
        if($action==='assign'){
            $boothId=(int)$_POST['booth_id']; $companyId=(int)$_POST['company_id'];
            $db->beginTransaction();
            $st=$db->prepare('SELECT fair_id, booth_status FROM booth WHERE booth_id=? FOR UPDATE'); $st->execute([$boothId]); $booth=$st->fetch();
            if(!$booth) throw new RuntimeException('Booth not found.');
            if(strtoupper((string)$booth['booth_status'])!=='AVAILABLE') throw new RuntimeException('Only an AVAILABLE booth can be assigned.');
            $approved=$db->prepare("SELECT COUNT(*) FROM company_participation WHERE company_id=? AND fair_id=? AND approval_status='APPROVED'");
            $approved->execute([$companyId,(int)$booth['fair_id']]);
            if(!(int)$approved->fetchColumn()) throw new RuntimeException('The company must be APPROVED for this booth\'s career fair.');
            $check=$db->prepare('SELECT COUNT(*) FROM booth_assignment WHERE booth_id=?'); $check->execute([$boothId]);
            if((int)$check->fetchColumn()>0) throw new RuntimeException('Booth is already assigned.');
            $db->prepare('INSERT INTO booth_assignment(booth_id,company_id,assignment_date) VALUES(?,?,TRUNC(SYSDATE))')->execute([$boothId,$companyId]);
            $db->prepare("UPDATE booth SET booth_status='ASSIGNED' WHERE booth_id=?")->execute([$boothId]);
            $db->commit(); flash('success','Booth assigned in Oracle using a transaction.');
        } elseif($action==='release'){
            $boothId=(int)$_POST['booth_id']; $db->beginTransaction();
            $db->prepare('DELETE FROM booth_assignment WHERE booth_id=?')->execute([$boothId]);
            $db->prepare("UPDATE booth SET booth_status='AVAILABLE' WHERE booth_id=?")->execute([$boothId]);
            $db->commit(); flash('success','Booth assignment released in Oracle.');
        } elseif($action==='create'){
            $db->prepare("INSERT INTO booth(booth_id,fair_id,booth_number,location,booth_status) VALUES(seq_booth.NEXTVAL,?,?,?,'AVAILABLE')")->execute([(int)$_POST['fair_id'],post('booth_number'),post('location')]); flash('success','Booth added to Oracle.');
        }
    }catch(Throwable $e){ if($db->inTransaction())$db->rollBack(); flash('error','Booth operation failed: '.oracle_error_message($e)); }
    redirect('admin/booths.php');
}
$fairs=$db->query('SELECT fair_id,fair_title FROM career_fair ORDER BY event_date')->fetchAll();
$companies=$db->query('SELECT company_id,company_name FROM company ORDER BY company_name')->fetchAll();
$rows=$db->query('SELECT b.*,cf.fair_title,ba.company_id,ba.assignment_date,c.company_name FROM booth b JOIN career_fair cf ON cf.fair_id=b.fair_id LEFT JOIN booth_assignment ba ON ba.booth_id=b.booth_id LEFT JOIN company c ON c.company_id=ba.company_id ORDER BY cf.event_date,b.booth_number')->fetchAll();
$pageTitle='Booth Management'; include dirname(__DIR__).'/includes/header.php'; ?>
<div class="card form-card"><h2 style="margin-top:0">Add Booth</h2><form method="post"><input type="hidden" name="action" value="create"><?= csrf_field() ?><div class="form-grid"><div class="field"><label>Career Fair</label><select name="fair_id" required><?php foreach($fairs as $f):?><option value="<?= e((string)$f['fair_id']) ?>"><?= e($f['fair_title']) ?></option><?php endforeach;?></select></div><div class="field"><label>Booth Number</label><input name="booth_number" required placeholder="A-01"></div><div class="field full"><label>Location</label><input name="location" required></div></div><button class="btn primary" style="margin-top:14px">Add Booth</button></form></div>
<div class="section-title"><h2>All Booths</h2></div><div class="table-wrap"><table><thead><tr><th>Booth</th><th>Career Fair</th><th>Location</th><th>Status</th><th>Company / Action</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><strong><?= e($r['booth_number']) ?></strong></td><td><?= e($r['fair_title']) ?></td><td><?= e($r['location']) ?></td><td><span class="badge <?= badge_class($r['booth_status']) ?>"><?= e($r['booth_status']) ?></span></td><td><?php if($r['company_id']):?><strong><?= e($r['company_name']) ?></strong><br><span class="muted">Assigned <?= e($r['assignment_date']) ?></span><form method="post" style="margin-top:7px" onsubmit="return confirm('Release this booth?')"><?= csrf_field() ?><input type="hidden" name="action" value="release"><input type="hidden" name="booth_id" value="<?= e((string)$r['booth_id']) ?>"><button class="btn small danger">Release</button></form><?php else:?><form method="post" class="actions"><?= csrf_field() ?><input type="hidden" name="action" value="assign"><input type="hidden" name="booth_id" value="<?= e((string)$r['booth_id']) ?>"><select name="company_id" required style="width:auto"><option value="">Select approved company</option><?php foreach($companies as $c):?><option value="<?= e((string)$c['company_id']) ?>"><?= e($c['company_name']) ?></option><?php endforeach;?></select><button class="btn small success">Assign</button></form><?php endif;?></td></tr><?php endforeach;?></tbody></table></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
