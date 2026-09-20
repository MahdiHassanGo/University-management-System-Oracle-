<?php
require_once dirname(__DIR__) . '/includes/auth.php'; require_role('ADMIN'); $pdo=db();
$rows=$pdo->query('SELECT f.*, s.student_name, cf.fair_title FROM feedback f JOIN student s ON s.student_id=f.student_id JOIN career_fair cf ON cf.fair_id=f.fair_id ORDER BY f.feedback_id DESC')->fetchAll();
$avg=$pdo->query('SELECT ROUND(AVG(rating),2) FROM feedback')->fetchColumn();
$pageTitle='Feedback'; include dirname(__DIR__).'/includes/header.php'; ?>
<div class="card" style="max-width:300px"><div class="stat-label">Overall Average Rating</div><div class="stat-value"><?= e((string)($avg?:0)) ?>/5</div></div>
<div class="section-title"><h2>Student Feedback</h2></div><div class="table-wrap"><table><thead><tr><th>Student</th><th>Career Fair</th><th>Date</th><th>Rating</th><th>Comments</th></tr></thead><tbody><?php foreach($rows as $r):?><tr><td><?= e($r['student_name']) ?></td><td><?= e($r['fair_title']) ?></td><td><?= e($r['submission_date']) ?></td><td><strong><?= e((string)$r['rating']) ?>/5</strong></td><td><?= e($r['comments']) ?></td></tr><?php endforeach;?></tbody></table></div>
<?php include dirname(__DIR__).'/includes/footer.php'; ?>
