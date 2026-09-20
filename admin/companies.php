<?php
require_once dirname(__DIR__) . '/includes/auth.php';
require_role('ADMIN');
$db=db();
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $companyId=(int)($_POST['company_id']??0); $fairId=(int)($_POST['fair_id']??0); $status=post('approval_status');
    try {
        if(in_array($status,['PENDING','APPROVED','REJECTED'],true)){
            $db->prepare('UPDATE company_participation SET approval_status=? WHERE company_id=? AND fair_id=?')->execute([$status,$companyId,$fairId]);
            flash('success','Company participation status updated in Oracle.');
        }
    } catch(Throwable $e) { flash('error','Update failed: '.oracle_error_message($e)); }
    redirect('admin/companies.php');
}
$companies=$db->query('SELECT c.*, (SELECT COUNT(*) FROM company_representative cr WHERE cr.company_id=c.company_id) representatives, (SELECT COUNT(*) FROM job_vacancy j WHERE j.company_id=c.company_id) jobs FROM company c ORDER BY c.company_name')->fetchAll();
$parts=$db->query("SELECT cp.*, c.company_name, cf.fair_title FROM company_participation cp JOIN company c ON c.company_id=cp.company_id JOIN career_fair cf ON cf.fair_id=cp.fair_id ORDER BY CASE cp.approval_status WHEN 'PENDING' THEN 1 WHEN 'APPROVED' THEN 2 WHEN 'REJECTED' THEN 3 ELSE 4 END, cp.company_registration_date DESC")->fetchAll();
$pageTitle='Companies & Approvals'; include dirname(__DIR__).'/includes/header.php';
?>
<div class="section-title"><h2>Fair Participation Requests</h2></div>
<div class="table-wrap"><table><thead><tr><th>Company</th><th>Fair</th><th>Requested</th><th>Status</th><th>Change Status</th></tr></thead><tbody>
<?php foreach($parts as $p):?><tr><td><strong><?= e($p['company_name']) ?></strong></td><td><?= e($p['fair_title']) ?></td><td><?= e($p['company_registration_date']) ?></td><td><span class="badge <?= badge_class($p['approval_status']) ?>"><?= e($p['approval_status']) ?></span></td><td><form method="post" class="actions"><?= csrf_field() ?><input type="hidden" name="company_id" value="<?= e((string)$p['company_id']) ?>"><input type="hidden" name="fair_id" value="<?= e((string)$p['fair_id']) ?>"><select name="approval_status" style="width:auto"><option <?= $p['approval_status']==='PENDING'?'selected':'' ?>>PENDING</option><option <?= $p['approval_status']==='APPROVED'?'selected':'' ?>>APPROVED</option><option <?= $p['approval_status']==='REJECTED'?'selected':'' ?>>REJECTED</option></select><button class="btn small primary">Update</button></form></td></tr><?php endforeach;?>
</tbody></table></div>
<div class="section-title"><h2>Company Directory</h2></div><div class="table-wrap"><table><thead><tr><th>Company</th><th>Industry</th><th>Contact</th><th>Representatives</th><th>Jobs</th></tr></thead><tbody><?php foreach($companies as $c):?><tr><td><strong><?= e($c['company_name']) ?></strong><br><span class="muted"><?= e($c['website']) ?></span></td><td><?= e($c['industry_type']) ?></td><td><?= e($c['email']) ?><br><?= e($c['phone_number']) ?></td><td><?= e((string)$c['representatives']) ?></td><td><?= e((string)$c['jobs']) ?></td></tr><?php endforeach;?></tbody></table></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
