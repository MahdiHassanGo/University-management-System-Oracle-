<?php
require_once dirname(__DIR__) . '/includes/auth.php'; require_role('ADMIN'); $pdo=db();
$students=$pdo->query('SELECT s.*, (SELECT COUNT(*) FROM fair_registration fr WHERE fr.student_id=s.student_id) fairs, (SELECT COUNT(*) FROM application a WHERE a.student_id=s.student_id) applications FROM student s ORDER BY s.student_name')->fetchAll();
$pageTitle='Students'; include dirname(__DIR__).'/includes/header.php'; ?>
<div class="card"><p class="muted" style="margin:0">Student data comes directly from the normalized STUDENT table. Fair registrations and applications are counted from their relationship tables.</p></div>
<div class="section-title"><h2>Registered Students</h2></div><div class="table-wrap"><table><thead><tr><th>Student</th><th>Department</th><th>Graduation</th><th>Phone</th><th>Fairs</th><th>Applications</th></tr></thead><tbody>
<?php foreach($students as $s):?><tr><td><strong><?= e($s['student_name']) ?></strong><br><span class="muted"><?= e($s['email']) ?></span></td><td><?= e($s['department']) ?></td><td><?= e((string)$s['graduation_year']) ?></td><td><?= e($s['phone_number']) ?></td><td><?= e((string)$s['fairs']) ?></td><td><?= e((string)$s['applications']) ?></td></tr><?php endforeach;?>
</tbody></table></div><?php include dirname(__DIR__).'/includes/footer.php'; ?>
