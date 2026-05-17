<?php
function loggedIn(): bool
{
    return isset($_SESSION['uid']);
}
function isAdmin(): bool
{
    return ($_SESSION['role'] ?? '') === 'admin';
}
function mustLogin(string $back = ''): void
{
    if (!loggedIn()) {
        header('Location: ' . BASE . '/login.php' . ($back ? "?back=" . urlencode($back) : ''));
        exit;
    }
}
function mustAdmin(): void
{
    if (!isAdmin()) {
        header('Location: ' . BASE . '/admin/login.php');
        exit;
    }
}
function me(): array|false
{
    static $cached = null;
    if ($cached !== null) return $cached;
    if (!loggedIn())
        return $cached = false;
    return $cached = DB::one("SELECT * FROM users WHERE id=?", [$_SESSION['uid']]);
}

function clean(string $v): string
{
    return htmlspecialchars(trim($v), ENT_QUOTES, 'UTF-8');
}

// FIXED: date formatting helper
function fmtDate(string|null $d, string $fmt = 'd M Y'): string
{
    if (!$d || $d === '0000-00-00')
        return '—';
    try {
        return date($fmt, strtotime($d));
    } catch (Throwable) {
        return $d;
    }
}

function rupee(float $n): string
{
    return '₹' . number_format($n, 0, '.', ',');
}
function genRef(): string
{
    return 'TN' . strtoupper(bin2hex(random_bytes(5))) . rand(10, 99);
}
function genPNR(): string
{
    return 'PNR' . rand(1000000000, 9999999999);
}

function flash(string $t, string $m): void
{
    $_SESSION['flash'] = ['t' => $t, 'm' => $m];
}
function getFlash(): string
{
    if (empty($_SESSION['flash']))
        return '';
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    $bg = $f['t'] === 'ok' ? '#052e1c' : '#3b0012';
    $col = $f['t'] === 'ok' ? '#4ade80' : '#fb7185';
    return "<div class='flash " . ($f['t'] === 'ok' ? 'ok' : 'err') . "'>" . clean($f['m']) . "</div>";
}
function csrf(): string
{
    if (empty($_SESSION['csrf']))
        $_SESSION['csrf'] = bin2hex(random_bytes(24));
    return $_SESSION['csrf'];
}
function checkCsrf(): bool
{
    return isset($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']);
}

function tierBadge(string $t): string
{
    $m = [
        'Platinum' => ['bg' => '#faf5ff', 'color' => '#7c3aed', 'border' => '#ddd6fe'],
        'Gold' => ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'],
        'Silver' => ['bg' => '#f8fafc', 'color' => '#64748b', 'border' => '#e2e8f0'],
        'Bronze' => ['bg' => '#f0f7ff', 'color' => '#1e5fad', 'border' => '#bfdbfe'],
    ];
    $s = $m[$t] ?? ['bg' => '#f8fafc', 'color' => '#64748b', 'border' => '#e2e8f0'];
    return "<span style='background:{$s['bg']};color:{$s['color']};border:1px solid {$s['border']};padding:3px 12px;border-radius:20px;font-size:11px;font-weight:600'>$t</span>";
}
function statusBadge(string $s): string
{
    $green = ['bg' => '#f0fdf4', 'color' => '#16a34a', 'border' => '#bbf7d0'];
    $amber = ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a'];
    $red = ['bg' => '#fef2f2', 'color' => '#ef4444', 'border' => '#fecaca'];
    $blue = ['bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#bfdbfe'];
    $purple = ['bg' => '#faf5ff', 'color' => '#7c3aed', 'border' => '#ddd6fe'];
    $gray = ['bg' => '#f8fafc', 'color' => '#64748b', 'border' => '#e2e8f0'];
    $m = [
        'Confirmed' => $green,
        'Active' => $green,
        'Available' => $green,
        'Pending' => $amber,
        'Expiring' => $amber,
        'WL 4' => $amber,
        'WL 8' => $amber,
        'WL 12' => $amber,
        'RAC 3' => $amber,
        'RAC 5' => $amber,
        'Cancelled' => $red,
        'Suspended' => $red,
        'Completed' => $blue,
        'Scheduled' => $purple,
    ];
    $st = $m[$s] ?? $gray;
    return "<span style='background:{$st['bg']};color:{$st['color']};border:1px solid {$st['border']};padding:3px 12px;border-radius:20px;font-size:11px;font-weight:600'>$s</span>";
}
function pagLinks(int $cur, int $last, string $base): string
{
    if ($last <= 1)
        return '';
    $o = '<div class="pag">';
    if ($cur > 1)
        $o .= "<a class='pgb' href='{$base}&pg=" . ($cur - 1) . "'>‹ Prev</a>";
    for ($i = max(1, $cur - 2); $i <= min($last, $cur + 2); $i++)
        $o .= "<a class='pgb" . ($i === $cur ? ' on' : '') . "' href='{$base}&pg=$i'>$i</a>";
    if ($cur < $last)
        $o .= "<a class='pgb' href='{$base}&pg=" . ($cur + 1) . "'>Next ›</a>";
    return $o . '</div>';
}
function amenArr(string $s): array
{
    return array_filter(array_map('trim', explode(',', $s)));
}
function incArr(string $s): array
{
    return array_filter(array_map('trim', explode('|', $s)));
}
function jsonOut(mixed $d, int $c = 200): never
{
    http_response_code($c);
    header('Content-Type: application/json');
    echo json_encode($d);
    exit;
}
