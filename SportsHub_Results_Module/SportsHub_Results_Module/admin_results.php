<?php
session_start();
require 'db.php';

/* Standalone demo access. Replace this with your group's real admin login. */
$demoPassword = 'admin123';
if (isset($_POST['admin_password'])) {
    if (hash_equals($demoPassword, (string)$_POST['admin_password'])) {
        $_SESSION['results_admin'] = true;
    } else {
        $loginError = 'Incorrect password.';
    }
}
if (isset($_GET['logout'])) {
    unset($_SESSION['results_admin']);
    header('Location: admin_results.php');
    exit;
}
$isAdmin = !empty($_SESSION['results_admin']);
function h($value) { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8'); }

if ($isAdmin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_result'])) {
    $matchId = (int)($_POST['match_id'] ?? 0);
    $clubScore = filter_var($_POST['club_score'] ?? null, FILTER_VALIDATE_INT);
    $opponentScore = filter_var($_POST['opponent_score'] ?? null, FILTER_VALIDATE_INT);
    $winner = trim($_POST['winner'] ?? '');
    $remarks = trim($_POST['remarks'] ?? '');
    if ($matchId > 0 && $clubScore !== false && $clubScore >= 0 && $opponentScore !== false && $opponentScore >= 0 && $winner !== '') {
        $stmt = $pdo->prepare("INSERT INTO match_results (match_id, club_score, opponent_score, winner, remarks)
            VALUES (?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE club_score=VALUES(club_score), opponent_score=VALUES(opponent_score),
            winner=VALUES(winner), remarks=VALUES(remarks)");
        $stmt->execute([$matchId, $clubScore, $opponentScore, $winner, $remarks ?: null]);
        header('Location: admin_results.php?saved=1');
        exit;
    } else {
        $formError = 'Please enter a match, non-negative scores, and a winner.';
    }
}
if ($isAdmin && isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM match_results WHERE result_id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: admin_results.php?deleted=1');
    exit;
}
$matches = $pdo->query("SELECT m.match_id, m.match_name, m.opponent, c.club_name
    FROM matches m LEFT JOIN sports_clubs c ON c.club_id=m.club_id ORDER BY m.match_date DESC")->fetchAll();
$allResults = $pdo->query("SELECT r.result_id, r.match_id, m.match_name, m.opponent, c.club_name,
    r.club_score, r.opponent_score, r.winner, r.remarks
    FROM match_results r JOIN matches m ON m.match_id=r.match_id
    LEFT JOIN sports_clubs c ON c.club_id=m.club_id ORDER BY r.updated_at DESC")->fetchAll();
?>
<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin | SportsHub Results</title><link rel="stylesheet" href="style.css"></head>
<body>
<header class="topbar"><div class="brand"><span class="logo">SH</span><div><strong>NSBM SportsHub</strong><small>Results administration</small></div></div><nav><a href="results.php">View Results</a><a href="admin_results.php" class="active">Admin</a></nav></header>
<main class="container admin-container">
<section class="hero compact"><p class="eyebrow">ADMIN PANEL</p><h1>Manage <span>Results</span></h1><p>Add or update the scores after a match.</p></section>
<?php if (!$isAdmin): ?>
  <section class="panel login-panel"><h2>Administrator login</h2><p class="muted">Demo access for testing this module.</p><?php if (!empty($loginError)): ?><p class="error"><?= h($loginError) ?></p><?php endif; ?><form method="post"><label>Demo password<input type="password" name="admin_password" required></label><button class="btn" type="submit">Login</button></form><p class="hint">Demo password: <code>admin123</code></p></section>
<?php else: ?>
  <?php if (isset($_GET['saved'])): ?><p class="success">Result saved successfully.</p><?php endif; ?>
  <?php if (isset($_GET['deleted'])): ?><p class="success">Result deleted.</p><?php endif; ?>
  <?php if (!empty($formError)): ?><p class="error"><?= h($formError) ?></p><?php endif; ?>
  <section class="panel"><div class="panel-heading"><h2>Add / Update a Result</h2><a class="text-link" href="admin_results.php?logout=1">Log out</a></div>
  <?php if (!$matches): ?><p>No matches found. Add matches first using your group's Fixtures/Matches module.</p><?php else: ?>
  <form method="post" class="form-grid">
    <label class="full">Match<select name="match_id" required><option value="">Select a match</option><?php foreach ($matches as $m): ?><option value="<?= (int)$m['match_id'] ?>"><?= h(($m['match_name'] ?: 'Match').' — '.($m['club_name'] ?: 'NSBM').' vs '.$m['opponent']) ?></option><?php endforeach; ?></select></label>
    <label>NSBM score<input type="number" name="club_score" min="0" required></label>
    <label>Opponent score<input type="number" name="opponent_score" min="0" required></label>
    <label class="full">Winner<input type="text" name="winner" placeholder="e.g. Football Club / NSBM / Draw" required maxlength="100"></label>
    <label class="full">Remarks (optional)<textarea name="remarks" rows="2" maxlength="255" placeholder="Short match summary"></textarea></label>
    <div class="full"><button class="btn" type="submit" name="save_result" value="1">Save Result</button></div>
  </form><?php endif; ?></section>
  <section class="panel"><h2>Saved Results</h2><?php if (!$allResults): ?><p class="muted">No results saved yet.</p><?php else: ?><div class="table-wrap"><table><thead><tr><th>Match</th><th>Score</th><th>Winner</th><th>Action</th></tr></thead><tbody><?php foreach ($allResults as $r): ?><tr><td><?= h($r['match_name'].' — '.($r['club_name'] ?: 'NSBM').' vs '.$r['opponent']) ?></td><td><?= (int)$r['club_score'] ?> : <?= (int)$r['opponent_score'] ?></td><td><?= h($r['winner']) ?></td><td><a class="delete-link" href="admin_results.php?delete=<?= (int)$r['result_id'] ?>" onclick="return confirm('Delete this result?')">Delete</a></td></tr><?php endforeach; ?></tbody></table></div><?php endif; ?></section>
<?php endif; ?>
</main><footer>© <?= date('Y') ?> NSBM SportsHub · Admin Results</footer>
</body></html>
