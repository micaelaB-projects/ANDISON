<?php

declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/_layout.php';
require_once __DIR__ . '/../includes/supabase.php';

function andison_normalize_date(?string $raw): ?string
{
    $value = trim((string)$raw);
    if ($value === '') {
        return null;
    }

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
        return null;
    }

    $tz = new DateTimeZone('Asia/Manila');
    $dt = DateTimeImmutable::createFromFormat('Y-m-d', $value, $tz);
    if (!$dt || $dt->format('Y-m-d') !== $value) {
        return null;
    }

    return $value;
}

function andison_inquiry_datetime_manila(array $inq): ?DateTimeImmutable
{
    $created = $inq['created_at'] ?? $inq['submitted_at'] ?? null;
    if (!is_string($created) || trim($created) === '') {
        return null;
    }

    $value = trim($created);
    try {
        $dt = new DateTimeImmutable($value, new DateTimeZone('UTC'));
        return $dt->setTimezone(new DateTimeZone('Asia/Manila'));
    } catch (Exception $e) {
        try {
            return new DateTimeImmutable($value, new DateTimeZone('Asia/Manila'));
        } catch (Exception $e2) {
            return null;
        }
    }
}

function andison_inquiries_url(string $filter, ?string $dateFrom, ?string $dateTo): string
{
    $params = [];
    if ($filter !== 'all') {
        $params['filter'] = $filter;
    }
    if ($dateFrom !== null) {
        $params['date_from'] = $dateFrom;
    }
    if ($dateTo !== null) {
        $params['date_to'] = $dateTo;
    }

    return 'inquiries.php' . (!empty($params) ? '?' . http_build_query($params) : '');
}

// Handle status update via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    $returnFilter = isset($_POST['filter']) ? trim((string)$_POST['filter']) : 'all';
    if (!in_array($returnFilter, ['all', 'new', 'read', 'responded'], true)) {
        $returnFilter = 'all';
    }
    $returnDateFrom = andison_normalize_date($_POST['date_from'] ?? null);
    $returnDateTo   = andison_normalize_date($_POST['date_to'] ?? null);
    if ($returnDateFrom !== null && $returnDateTo !== null && $returnDateFrom > $returnDateTo) {
        $tmp = $returnDateFrom;
        $returnDateFrom = $returnDateTo;
        $returnDateTo = $tmp;
    }

    if ($action === 'mark_read' && $id > 0) {
        andison_sb_update('inquiries', ['status' => 'read'], 'id=eq.' . $id);
        andison_set_flash('success', 'Inquiry marked as read.');
    } elseif ($action === 'mark_responded' && $id > 0) {
        andison_sb_update('inquiries', ['status' => 'responded'], 'id=eq.' . $id);
        andison_set_flash('success', 'Inquiry marked as responded.');
    } elseif ($action === 'delete' && $id > 0) {
        andison_sb_delete('inquiries', 'id=eq.' . $id);
        andison_set_flash('success', 'Inquiry deleted.');
    }

    header('Location: ' . andison_inquiries_url($returnFilter, $returnDateFrom, $returnDateTo));
    exit;
}

// Fetch all inquiries, newest first
$filter = isset($_GET['filter']) ? trim((string)$_GET['filter']) : 'all';
if (!in_array($filter, ['all', 'new', 'read', 'responded'], true)) {
    $filter = 'all';
}

$dateFrom = andison_normalize_date($_GET['date_from'] ?? null);
$dateTo   = andison_normalize_date($_GET['date_to'] ?? null);
if ($dateFrom !== null && $dateTo !== null && $dateFrom > $dateTo) {
    $tmp = $dateFrom;
    $dateFrom = $dateTo;
    $dateTo = $tmp;
}

$manilaTz = new DateTimeZone('Asia/Manila');
$todayDate = (new DateTimeImmutable('now', $manilaTz))->format('Y-m-d');
$presetTodayFrom = $todayDate;
$presetTodayTo = $todayDate;
$presetLast7From = (new DateTimeImmutable($todayDate . ' 00:00:00', $manilaTz))->modify('-6 days')->format('Y-m-d');
$presetLast7To = $todayDate;
$presetMonthFrom = (new DateTimeImmutable($todayDate . ' 00:00:00', $manilaTz))->modify('first day of this month')->format('Y-m-d');
$presetMonthTo = $todayDate;

$isPresetToday = ($dateFrom === $presetTodayFrom && $dateTo === $presetTodayTo);
$isPresetLast7 = ($dateFrom === $presetLast7From && $dateTo === $presetLast7To);
$isPresetMonth = ($dateFrom === $presetMonthFrom && $dateTo === $presetMonthTo);

$query  = 'order=id.desc';
if ($filter === 'new')       $query .= '&status=eq.new';
elseif ($filter === 'read')  $query .= '&status=eq.read';
elseif ($filter === 'responded') $query .= '&status=eq.responded';

$inquiries = andison_sb_select('inquiries', $query);

$fromBoundary = $dateFrom !== null ? new DateTimeImmutable($dateFrom . ' 00:00:00', new DateTimeZone('Asia/Manila')) : null;
$toBoundary   = $dateTo !== null ? new DateTimeImmutable($dateTo . ' 23:59:59', new DateTimeZone('Asia/Manila')) : null;

if ($fromBoundary !== null || $toBoundary !== null) {
    $filteredInquiries = [];
    foreach ($inquiries as $inqRow) {
        if (!is_array($inqRow)) {
            continue;
        }

        $inqDt = andison_inquiry_datetime_manila($inqRow);
        if (!$inqDt) {
            continue;
        }

        if ($fromBoundary !== null && $inqDt < $fromBoundary) {
            continue;
        }
        if ($toBoundary !== null && $inqDt > $toBoundary) {
            continue;
        }

        $filteredInquiries[] = $inqRow;
    }
    $inquiries = $filteredInquiries;
}

// Count per status
$allCount       = count(andison_sb_select('inquiries', 'select=id'));
$newCount       = count(andison_sb_select('inquiries', 'status=eq.new&select=id'));
$readCount      = count(andison_sb_select('inquiries', 'status=eq.read&select=id'));
$respondedCount = count(andison_sb_select('inquiries', 'status=eq.responded&select=id'));

andison_admin_header('Inquiries', 'inquiries');
?>
<style>
    .filter-toolbar{display:flex;justify-content:space-between;align-items:flex-end;gap:12px;flex-wrap:wrap;margin-bottom:20px}
    .filter-tabs{display:flex;gap:8px;flex-wrap:wrap}
    .filter-tab{padding:8px 18px;border-radius:10px;font-size:13px;font-weight:700;text-decoration:none;border:2px solid var(--border);color:var(--muted);transition:all 0.2s ease;background:#fff}
    .filter-tab:hover{border-color:var(--accent);color:var(--accent)}
    .filter-tab.active{background:var(--accent);border-color:var(--accent);color:#fff}
    .filter-tab .cnt{display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:999px;font-size:11px;font-weight:900;background:rgba(255,255,255,0.25);margin-left:6px}
    .filter-tab:not(.active) .cnt{background:rgba(43,17,219,0.10);color:var(--accent)}
    .date-filter-form{display:flex;align-items:flex-end;gap:8px;flex-wrap:wrap;background:#fff;border:1px solid var(--border);border-radius:12px;padding:8px 10px}
    .date-filter-field{display:flex;flex-direction:column;gap:4px}
    .date-filter-field label{font-size:10px;font-weight:800;color:var(--muted);letter-spacing:0.5px;text-transform:uppercase}
    .date-filter-field input[type=date]{border:1px solid var(--border);border-radius:8px;padding:7px 10px;font-size:12px;color:var(--text);background:#fff;min-width:140px}
    .date-filter-field input[type=date]:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(43,17,219,0.12)}
    .date-filter-btn{padding:7px 12px;font-size:12px;border-radius:8px;font-weight:700;text-decoration:none}
    .date-filter-presets{display:flex;align-items:center;gap:6px;flex-wrap:wrap;margin-left:4px}
    .date-preset-chip{display:inline-flex;align-items:center;justify-content:center;padding:6px 10px;border-radius:999px;border:1px solid var(--border);text-decoration:none;font-size:11px;font-weight:800;color:var(--muted);background:#fff;transition:all 0.2s ease}
    .date-preset-chip:hover{border-color:var(--accent);color:var(--accent);background:rgba(43,17,219,0.06)}
    .date-preset-chip.active{border-color:var(--accent);background:var(--accent);color:#fff}
    @media (max-width: 740px){
        .filter-toolbar{align-items:stretch}
        .date-filter-form{width:100%}
        .date-filter-field{flex:1 1 140px}
        .date-filter-presets{width:100%;margin-left:0;padding-top:2px}
    }
    .inq-card{background:#fff;border:1px solid rgba(43,17,219,0.08);border-radius:16px;padding:20px 22px;margin-bottom:14px;transition:all 0.2s ease}
    .inq-card:hover{box-shadow:0 8px 28px rgba(43,17,219,0.09);transform:translateY(-1px)}
    .inq-card.status-new{border-left:4px solid #ef4444}
    .inq-card.status-read{border-left:4px solid #f59e0b}
    .inq-card.status-responded{border-left:4px solid #10b981}
    .inq-header{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap}
    .inq-name{font-weight:800;font-size:16px;color:var(--text)}
    .inq-company{font-size:13px;color:var(--muted);margin-top:2px}
    .inq-meta{display:flex;gap:14px;flex-wrap:wrap;margin-top:8px;font-size:13px;color:var(--muted)}
    .inq-meta span{display:flex;align-items:center;gap:5px}
    .inq-meta i{font-size:14px}
    .status-badge{padding:4px 12px;border-radius:999px;font-size:11px;font-weight:900;letter-spacing:0.3px}
    .status-new{background:rgba(239,68,68,0.12);color:#b91c1c}
    .status-read{background:rgba(245,158,11,0.12);color:#92400e}
    .status-responded{background:rgba(16,185,129,0.12);color:#065f46}
    .inq-date{font-size:12px;color:var(--muted);white-space:nowrap}
    .inq-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px;padding-top:14px;border-top:1px solid var(--border)}
    .inq-actions form{margin:0}
    .btn-sm{padding:7px 14px;font-size:12px;border-radius:10px;font-weight:700}
    .inq-items{margin-top:12px;background:#f9fafb;border-radius:10px;overflow:hidden}
    .inq-items-header{padding:8px 14px;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:0.5px;color:var(--muted);cursor:pointer;display:flex;align-items:center;gap:6px;user-select:none}
    .inq-items-header:hover{background:rgba(43,17,219,0.04)}
    .inq-items-body{display:none;padding:0 14px 12px}
    .inq-items-body.open{display:block}
    .inq-item-row{display:flex;align-items:center;gap:10px;padding:6px 0;border-bottom:1px solid var(--border);font-size:13px}
    .inq-item-row:last-child{border:none}
    .inq-item-name{font-weight:700;flex:1}
    .inq-item-brand{color:var(--muted);font-size:12px}
    .inq-item-qty{background:rgba(43,17,219,0.10);color:var(--accent);font-size:11px;font-weight:900;padding:3px 9px;border-radius:999px}
    .inq-message{margin-top:10px;padding:10px 14px;background:#f3f4f6;border-radius:10px;font-size:13px;color:#374151;line-height:1.5}
    .empty-state{text-align:center;padding:60px 20px;color:var(--muted)}
    .empty-state i{font-size:48px;opacity:0.3;display:block;margin-bottom:12px}
    .empty-state p{font-size:15px}
    
    /* Delete Modal Styles */
    .delete-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.4);display:none;align-items:center;justify-content:center;z-index:9999;backdrop-filter:blur(2px)}
    .delete-modal-overlay.open{display:flex}
    .delete-modal-box{background:#fff;border-radius:20px;padding:40px 32px;max-width:480px;width:90vw;box-shadow:0 20px 60px rgba(0,0,0,0.25);text-align:center}
    .delete-modal-icon{width:70px;height:70px;border:3px solid #4f46e5;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 20px;font-size:40px;color:#4f46e5}
    .delete-modal-title{font-size:24px;font-weight:800;color:#1f2937;margin-bottom:12px}
    .delete-modal-message{font-size:15px;color:#6b7280;line-height:1.6;margin-bottom:28px}
    .delete-modal-actions{display:flex;gap:12px;justify-content:center;margin-top:28px}
    .delete-modal-btn{padding:12px 32px;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer;transition:all 0.3s ease;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}
    .delete-modal-btn.ok{background:#06b6d4;color:#fff;box-shadow:0 4px 12px rgba(6,182,212,0.3)}
    .delete-modal-btn.ok:hover{background:#0891b2;transform:translateY(-2px);box-shadow:0 6px 16px rgba(6,182,212,0.4)}
    .delete-modal-btn.cancel{background:#fff;color:#4f46e5;border:2px solid #4f46e5;box-shadow:0 2px 8px rgba(0,0,0,0.08)}
    .delete-modal-btn.cancel:hover{background:#f3f4f6;transform:translateY(-2px)}
</style>

<div class="filter-toolbar">
    <div class="filter-tabs">
        <a href="<?php echo htmlspecialchars(andison_inquiries_url('all', $dateFrom, $dateTo)); ?>" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
            All <span class="cnt"><?php echo $allCount; ?></span>
        </a>
        <a href="<?php echo htmlspecialchars(andison_inquiries_url('new', $dateFrom, $dateTo)); ?>" class="filter-tab <?php echo $filter === 'new' ? 'active' : ''; ?>">
            New <span class="cnt"><?php echo $newCount; ?></span>
        </a>
        <a href="<?php echo htmlspecialchars(andison_inquiries_url('read', $dateFrom, $dateTo)); ?>" class="filter-tab <?php echo $filter === 'read' ? 'active' : ''; ?>">
            Read <span class="cnt"><?php echo $readCount; ?></span>
        </a>
        <a href="<?php echo htmlspecialchars(andison_inquiries_url('responded', $dateFrom, $dateTo)); ?>" class="filter-tab <?php echo $filter === 'responded' ? 'active' : ''; ?>">
            Responded <span class="cnt"><?php echo $respondedCount; ?></span>
        </a>
    </div>

    <form method="get" class="date-filter-form">
        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
        <div class="date-filter-field">
            <label for="inqDateFrom">From</label>
            <input id="inqDateFrom" type="date" name="date_from" value="<?php echo htmlspecialchars((string)($dateFrom ?? '')); ?>">
        </div>
        <div class="date-filter-field">
            <label for="inqDateTo">To</label>
            <input id="inqDateTo" type="date" name="date_to" value="<?php echo htmlspecialchars((string)($dateTo ?? '')); ?>">
        </div>
        <button type="submit" class="btn btn-outline date-filter-btn"><i class="bi bi-funnel"></i> Apply</button>
        <?php if ($dateFrom !== null || $dateTo !== null): ?>
            <a href="<?php echo htmlspecialchars(andison_inquiries_url($filter, null, null)); ?>" class="btn btn-danger date-filter-btn"><i class="bi bi-x-circle"></i> Clear</a>
        <?php endif; ?>
        <div class="date-filter-presets">
            <a href="<?php echo htmlspecialchars(andison_inquiries_url($filter, $presetTodayFrom, $presetTodayTo)); ?>" class="date-preset-chip <?php echo $isPresetToday ? 'active' : ''; ?>">Today</a>
            <a href="<?php echo htmlspecialchars(andison_inquiries_url($filter, $presetLast7From, $presetLast7To)); ?>" class="date-preset-chip <?php echo $isPresetLast7 ? 'active' : ''; ?>">Last 7 Days</a>
            <a href="<?php echo htmlspecialchars(andison_inquiries_url($filter, $presetMonthFrom, $presetMonthTo)); ?>" class="date-preset-chip <?php echo $isPresetMonth ? 'active' : ''; ?>">This Month</a>
        </div>
    </form>
</div>

<?php if (empty($inquiries)): ?>
<div class="empty-state">
    <i class="bi bi-inbox"></i>
    <p>No inquiries found<?php echo $filter !== 'all' ? ' for this filter' : ''; ?><?php echo ($dateFrom !== null || $dateTo !== null) ? ' in this date range' : ''; ?>.</p>
</div>
<?php else: ?>

<?php
    $filterHiddenValue = htmlspecialchars($filter);
    $dateFromHiddenValue = htmlspecialchars((string)($dateFrom ?? ''));
    $dateToHiddenValue = htmlspecialchars((string)($dateTo ?? ''));
?>

<?php foreach ($inquiries as $inq):
    $inqId     = (int)($inq['id'] ?? 0);
    $status    = htmlspecialchars($inq['status'] ?? 'new');
    $txnNo     = htmlspecialchars($inq['transaction_no'] ?? ('AIS-' . str_pad((string)$inqId, 4, '0', STR_PAD_LEFT)));
    $fullname  = htmlspecialchars($inq['fullname'] ?? '');
    $company   = htmlspecialchars($inq['company'] ?? '');
    $email     = htmlspecialchars($inq['email'] ?? '');
    $phone     = htmlspecialchars($inq['phone'] ?? '');
    $address   = htmlspecialchars($inq['address'] ?? '');
    $contact_m = htmlspecialchars($inq['contact_method'] ?? 'email');
    $msg       = htmlspecialchars($inq['message'] ?? '');
    $attachmentUrl  = trim((string)($inq['attachment_url'] ?? $inq['file_url'] ?? ''));
    $attachmentName = trim((string)($inq['attachment_name'] ?? $inq['attachment_filename'] ?? $inq['file_name'] ?? ''));
    $attachmentMime = trim((string)($inq['attachment_mime'] ?? $inq['file_mime'] ?? ''));

    if ($attachmentUrl === '' || $attachmentName === '') {
        $singleAttachment = $inq['attachment'] ?? null;
        if (is_string($singleAttachment)) {
            $decodedAttachment = json_decode($singleAttachment, true);
            if (is_array($decodedAttachment)) {
                if ($attachmentUrl === '') {
                    $attachmentUrl = trim((string)($decodedAttachment['url'] ?? ''));
                }
                if ($attachmentName === '') {
                    $attachmentName = trim((string)($decodedAttachment['name'] ?? $decodedAttachment['storage_name'] ?? ''));
                }
                if ($attachmentMime === '') {
                    $attachmentMime = trim((string)($decodedAttachment['mime'] ?? ''));
                }
            }
        }
    }

    if ($attachmentUrl === '' || $attachmentName === '') {
        $manyAttachments = $inq['attachments'] ?? null;
        if (is_string($manyAttachments)) {
            $decodedAttachments = json_decode($manyAttachments, true);
            if (is_array($decodedAttachments) && !empty($decodedAttachments[0]) && is_array($decodedAttachments[0])) {
                if ($attachmentUrl === '') {
                    $attachmentUrl = trim((string)($decodedAttachments[0]['url'] ?? ''));
                }
                if ($attachmentName === '') {
                    $attachmentName = trim((string)($decodedAttachments[0]['name'] ?? $decodedAttachments[0]['storage_name'] ?? ''));
                }
                if ($attachmentMime === '') {
                    $attachmentMime = trim((string)($decodedAttachments[0]['mime'] ?? ''));
                }
            }
        }
    }

    if ($attachmentName === '' && $attachmentUrl !== '') {
        $pathPart = parse_url($attachmentUrl, PHP_URL_PATH);
        $attachmentName = is_string($pathPart) ? basename($pathPart) : 'Attachment';
    }

    $attachmentUrlEsc  = htmlspecialchars($attachmentUrl);
    $attachmentNameEsc = htmlspecialchars($attachmentName);
    $inqDateTime = andison_inquiry_datetime_manila(is_array($inq) ? $inq : []);
    if ($inqDateTime) {
        $dateStr = $inqDateTime->format('M j, Y · g:i A');
    } else {
        $dateStr = 'Inquiry #' . $inqId;
    }

    // Items stored as JSON directly in the inquiries row
    $rawItems  = $inq['items'] ?? null;
    $items     = [];
    if (is_string($rawItems)) {
        $decoded = json_decode($rawItems, true);
        if (is_array($decoded)) $items = $decoded;
    } elseif (is_array($rawItems)) {
        $items = $rawItems;
    }
?>
<div class="inq-card status-<?php echo $status; ?>">
    <div class="inq-header">
        <div>
            <div class="inq-name"><?php echo $fullname; ?></div>
            <div style="font-size:12px;font-weight:900;color:var(--accent);letter-spacing:0.5px;margin-bottom:4px;"><?php echo $txnNo; ?></div>
            <?php if ($company): ?><div class="inq-company"><i class="bi bi-building" style="font-size:12px"></i> <?php echo $company; ?></div><?php endif; ?>
            <div class="inq-meta">
                <?php if ($email): ?><span><i class="bi bi-envelope"></i> <a href="mailto:<?php echo $email; ?>" style="color:inherit;text-decoration:none;"><?php echo $email; ?></a></span><?php endif; ?>
                <?php if ($phone): ?><span><i class="bi bi-telephone"></i> <?php echo $phone; ?></span><?php endif; ?>
                <span><i class="bi bi-chat-dots"></i> via <?php echo ucfirst($contact_m); ?></span>
            </div>
        </div>
        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px">
            <span class="status-badge status-<?php echo $status; ?>"><?php echo ucfirst($status); ?></span>
            <span class="inq-date"><i class="bi bi-clock" style="font-size:11px"></i> <?php echo $dateStr; ?></span>
        </div>
    </div>

    <?php if ($address): ?>
    <div style="margin-top:8px;font-size:13px;color:var(--muted)"><i class="bi bi-geo-alt" style="font-size:12px"></i> <?php echo $address; ?></div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
    <div class="inq-items">
        <div class="inq-items-header" onclick="this.nextElementSibling.classList.toggle('open');this.querySelector('.toggle-icon').classList.toggle('bi-chevron-down');this.querySelector('.toggle-icon').classList.toggle('bi-chevron-right')">
            <i class="bi bi-box-seam"></i>
            <?php echo count($items); ?> Product<?php echo count($items) !== 1 ? 's' : ''; ?> Requested
            <i class="bi bi-chevron-right toggle-icon" style="margin-left:auto;font-size:12px"></i>
        </div>
        <div class="inq-items-body">
            <?php foreach ($items as $item): ?>
            <div class="inq-item-row">
                <span class="inq-item-name"><?php echo htmlspecialchars($item['name'] ?? ''); ?></span>
                <?php if (!empty($item['brand'])): ?><span class="inq-item-brand"><?php echo htmlspecialchars($item['brand']); ?></span><?php endif; ?>
                <span class="inq-item-qty">Qty: <?php echo (int)($item['qty'] ?? 1); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($msg): ?>
    <div class="inq-message"><strong>Message:</strong> <?php echo nl2br($msg); ?></div>
    <?php endif; ?>

    <?php if ($attachmentUrl !== ''): ?>
    <div class="inq-message" style="background:#eff6ff;border:1px solid #dbeafe;">
        <strong>Attachment:</strong>
        <a href="<?php echo $attachmentUrlEsc; ?>" target="_blank" rel="noopener" style="color:#1d4ed8;font-weight:700;text-decoration:none;">
            <?php echo $attachmentNameEsc ?: 'Download attachment'; ?>
        </a>
        <?php if ($attachmentMime !== ''): ?>
            <span style="color:#64748b;font-size:12px;">(<?php echo htmlspecialchars($attachmentMime); ?>)</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="inq-actions">
        <?php if ($status === 'new'): ?>
        <form method="post">
            <input type="hidden" name="id" value="<?php echo $inqId; ?>">
            <input type="hidden" name="action" value="mark_read">
            <input type="hidden" name="filter" value="<?php echo $filterHiddenValue; ?>">
            <input type="hidden" name="date_from" value="<?php echo $dateFromHiddenValue; ?>">
            <input type="hidden" name="date_to" value="<?php echo $dateToHiddenValue; ?>">
            <button type="submit" class="btn btn-outline btn-sm"><i class="bi bi-eye"></i> Mark as Read</button>
        </form>
        <?php endif; ?>

        <?php if ($status !== 'responded'): ?>
        <form method="post">
            <input type="hidden" name="id" value="<?php echo $inqId; ?>">
            <input type="hidden" name="action" value="mark_responded">
            <input type="hidden" name="filter" value="<?php echo $filterHiddenValue; ?>">
            <input type="hidden" name="date_from" value="<?php echo $dateFromHiddenValue; ?>">
            <input type="hidden" name="date_to" value="<?php echo $dateToHiddenValue; ?>">
            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check2-circle"></i> Mark Responded</button>
        </form>
        <?php endif; ?>

        <?php if ($email): ?>
        <a href="mailto:<?php echo $email; ?>?subject=Re: Your Inquiry - ANDISON INDUSTRIAL" class="btn btn-outline btn-sm">
            <i class="bi bi-reply"></i> Reply via Email
        </a>
        <?php endif; ?>

        <form method="post" style="margin-left:auto" class="delete-form">
            <input type="hidden" name="id" value="<?php echo $inqId; ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="filter" value="<?php echo $filterHiddenValue; ?>">
            <input type="hidden" name="date_from" value="<?php echo $dateFromHiddenValue; ?>">
            <input type="hidden" name="date_to" value="<?php echo $dateToHiddenValue; ?>">
            <button type="button" class="btn btn-danger btn-sm" onclick="showDeleteModal(this.closest('form'))"><i class="bi bi-trash3"></i> Delete</button>
        </form>
    </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="delete-modal-overlay">
    <div class="delete-modal-box">
        <div class="delete-modal-icon">?</div>
        <div class="delete-modal-title">Confirm Action</div>
        <div class="delete-modal-message">Are you sure you want to delete this inquiry? This action cannot be undone.</div>
        <div class="delete-modal-actions">
            <button class="delete-modal-btn ok" onclick="confirmDelete()">Yes, Proceed</button>
            <button class="delete-modal-btn cancel" onclick="closeDeleteModal()">Cancel</button>
        </div>
    </div>
</div>

<script>
let deleteModalForm = null;

function showDeleteModal(form) {
    deleteModalForm = form;
    document.getElementById('deleteModal').classList.add('open');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('open');
    deleteModalForm = null;
}

function confirmDelete() {
    if (deleteModalForm) {
        deleteModalForm.submit();
    }
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('deleteModal').classList.contains('open')) {
        closeDeleteModal();
    }
});
</script>

<?php andison_admin_footer(); ?>
