<?php
require 'db.php';
$sql = "SELECT m.match_id, m.match_name, m.opponent, m.match_date, m.venue,
               c.club_name, r.club_score, r.opponent_score, r.winner, r.remarks
        FROM matches m
        LEFT JOIN sports_clubs c ON c.club_id = m.club_id
        LEFT JOIN match_results r ON r.match_id = m.match_id
        ORDER BY m.match_date DESC, m.match_id DESC";
$results = $pdo->query($sql)->fetchAll();
function h($value) { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>SportsHub | Match Results</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
  <div class="brand"><span class="logo">SH</span><div><strong>NSBM SportsHub</strong><small>Sports Club Management System</small></div></div>
  <nav><a href="results.php" class="active">Results</a><a href="admin_results.php">Admin</a></nav>
</header>
<main class="container">
  <section class="hero">
    <p class="eyebrow">NSBM GREEN UNIVERSITY</p>
    <h1>Match <span>Results</span></h1>
    <p>Check the latest scores, winners, and match details.</p>
  </section>
  <div class="section-heading"><div><h2>Recent Results</h2><p>Scores are updated by the SportsHub administrator.</p></div><span class="count"><?= count($results) ?> matches</span></div>
  <?php if (!$results): ?>
    <div class="empty">No matches have been added yet.</div>
  <?php else: ?>
  <div class="results-grid">
    <?php foreach ($results as $r): ?>
      <article class="result-card">
        <div class="card-top"><span class="sport"><?= h($r['club_name'] ?: 'Sports Club') ?></span><span class="date"><?= h(date('d M Y', strtotime($r['match_date']))) ?></span></div>
        <h3><?= h($r['match_name']) ?></h3>
        <div class="scoreboard">
          <div class="team"><span class="team-icon">NS</span><strong><?= h($r['club_name'] ?: 'NSBM') ?></strong></div>
          <div class="score"><?= $r['club_score'] === null ? '—' : (int)$r['club_score'] ?></div>
          <span class="vs">:</span>
          <div class="score"><?= $r['opponent_score'] === null ? '—' : (int)$r['opponent_score'] ?></div>
          <div class="team opponent"><span class="team-icon alt">OP</span><strong><?= h($r['opponent']) ?></strong></div>
        </div>
        <div class="card-bottom">
          <span>📍 <?= h($r['venue']) ?></span>
          <?php if ($r['winner']): ?><span class="winner">Winner: <?= h($r['winner']) ?></span><?php else: ?><span class="pending">Result pending</span><?php endif; ?>
        </div>
        <?php if ($r['remarks']): ?><p class="remarks"><?= h($r['remarks']) ?></p><?php endif; ?>
      </article>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</main>
<footer>© <?= date('Y') ?> NSBM SportsHub · Sports Results Module</footer>
</body>
</html>


