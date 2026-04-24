<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../helpers.php';

// AJAX upload handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    header('Content-Type: application/json');
    $uploaded = 0;
    $failed   = 0;
    $errors   = [];

    if (empty($_FILES['images']['name'][0])) {
        echo json_encode(['ok' => false, 'msg' => 'لم يتم استقبال أي ملفات']);
        exit;
    }

    $count = count($_FILES['images']['name']);
    for ($i = 0; $i < $count; $i++) {
        if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) {
            $failed++;
            $errors[] = $_FILES['images']['name'][$i] . ' (خطأ: ' . $_FILES['images']['error'][$i] . ')';
            continue;
        }
        $file = [
            'name'     => $_FILES['images']['name'][$i],
            'tmp_name' => $_FILES['images']['tmp_name'][$i],
            'error'    => $_FILES['images']['error'][$i],
            'size'     => $_FILES['images']['size'][$i],
        ];
        $path = uploadFile($file, 'uploads');
        if ($path) {
            $name = pathinfo($file['name'], PATHINFO_FILENAME);
            db()->prepare("INSERT INTO partners (name, image, sort_order, status) VALUES (?, ?, 0, 'active')")
               ->execute([$name, $path]);
            $uploaded++;
        } else {
            $failed++;
            $errors[] = $_FILES['images']['name'][$i] . ' (نوع غير مدعوم أو حجم كبير)';
        }
    }

    echo json_encode([
        'ok'       => $uploaded > 0,
        'uploaded' => $uploaded,
        'failed'   => $failed,
        'errors'   => $errors,
    ]);
    exit;
}

// Normal POST fallback (non-AJAX)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploaded = 0; $failed = 0;
    if (!empty($_FILES['images']['name'][0])) {
        $count = count($_FILES['images']['name']);
        for ($i = 0; $i < $count; $i++) {
            if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) { $failed++; continue; }
            $file = [
                'name'     => $_FILES['images']['name'][$i],
                'tmp_name' => $_FILES['images']['tmp_name'][$i],
                'error'    => $_FILES['images']['error'][$i],
                'size'     => $_FILES['images']['size'][$i],
            ];
            $path = uploadFile($file, 'uploads');
            if ($path) {
                $name = pathinfo($file['name'], PATHINFO_FILENAME);
                db()->prepare("INSERT INTO partners (name, image, sort_order, status) VALUES (?, ?, 0, 'active')")
                   ->execute([$name, $path]);
                $uploaded++;
            } else { $failed++; }
        }
    }
    $msg = "تم رفع $uploaded صورة بنجاح";
    if ($failed) $msg .= " | فشل $failed";
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $failed && !$uploaded ? 'error' : 'success'];
    header('Location: partners.php'); exit;
}

$pageTitle = 'رفع صور جماعي للشركاء';
require_once 'includes/header.php';
?>
<style>
.drop-zone{border:2px dashed #c8d0e0;border-radius:12px;padding:48px 24px;text-align:center;cursor:pointer;transition:.2s;background:#fafbff;}
.drop-zone:hover,.drop-zone.over{border-color:var(--teal);background:rgba(58,192,202,.06);}
.preview-grid{display:flex;flex-wrap:wrap;gap:10px;margin-top:18px;}
.preview-item{position:relative;width:110px;height:85px;border:1px solid #e0e4ed;border-radius:8px;overflow:hidden;background:#f8f9fc;}
.preview-item img{width:100%;height:100%;object-fit:contain;padding:4px;}
.preview-item .rm{position:absolute;top:3px;right:3px;background:rgba(220,53,69,.85);color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:11px;line-height:20px;text-align:center;cursor:pointer;padding:0;}
.progress-bar-wrap{height:8px;background:#e8eaf0;border-radius:10px;overflow:hidden;margin-top:14px;display:none;}
.progress-bar-inner{height:100%;background:linear-gradient(90deg,var(--teal),var(--brand));transition:width .3s;}
#resultBox{margin-top:16px;display:none;}
</style>

<div class="card" style="max-width:780px;">
    <div class="card-header d-flex align-items-center gap-2">
        <h5 class="mb-0"><i class="fas fa-cloud-upload-alt me-2" style="color:var(--teal);"></i>رفع صور جماعي للشركاء</h5>
    </div>
    <div class="card-body">
        <p class="text-muted mb-3" style="font-size:14px;">
            اختر عدة صور دفعة واحدة أو اسحبها للمنطقة أدناه. كل صورة ستنشئ شريكاً جديداً واسم الملف سيُستخدم كاسم الشريك.
        </p>

        <div class="drop-zone" id="dropZone">
            <i class="fas fa-images mb-2" style="font-size:40px;color:#b0b8c8;display:block;"></i>
            <div style="font-size:15px;color:#888;">اسحب وأفلت الصور هنا أو <span style="color:var(--teal);font-weight:600;">انقر للاختيار</span></div>
            <div style="font-size:12px;color:#aaa;margin-top:6px;">JPG, PNG, SVG, WEBP — بحد أقصى 5 MB للصورة</div>
        </div>

        <input type="file" name="images[]" id="fileInput" multiple accept="image/*" style="display:none;">

        <div class="preview-grid" id="previewGrid"></div>

        <div class="progress-bar-wrap" id="progressWrap">
            <div class="progress-bar-inner" id="progressBar" style="width:0%"></div>
        </div>

        <div id="resultBox"></div>

        <div class="d-flex gap-2 mt-4">
            <button type="button" class="btn btn-brand" id="submitBtn" disabled onclick="startUpload()">
                <i class="fas fa-upload me-1"></i> رفع الكل
            </button>
            <a href="partners.php" class="btn btn-secondary" id="cancelBtn">إلغاء</a>
            <span id="countBadge" class="align-self-center text-muted" style="font-size:13px;"></span>
        </div>
    </div>
</div>

<?php
$extraScript = <<<'JS'
<script>
const dropZone    = document.getElementById('dropZone');
const fileInput   = document.getElementById('fileInput');
const previewGrid = document.getElementById('previewGrid');
const submitBtn   = document.getElementById('submitBtn');
const countBadge  = document.getElementById('countBadge');

dropZone.addEventListener('click', () => fileInput.click());
dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('over');
    addFiles(e.dataTransfer.files);
});
fileInput.addEventListener('change', () => {
    addFiles(fileInput.files);
    fileInput.value = '';
});

let fileList = [];

function addFiles(files) {
    Array.from(files).forEach(f => {
        if (!f.type.startsWith('image/')) return;
        if (fileList.some(x => x.name === f.name && x.size === f.size)) return;
        const idx = fileList.length;
        fileList.push(f);
        const reader = new FileReader();
        reader.onload = ev => {
            const div = document.createElement('div');
            div.className = 'preview-item';
            div.dataset.idx = idx;
            div.innerHTML = `<img src="${ev.target.result}"><button type="button" class="rm" onclick="removeFile(this)">&times;</button>`;
            previewGrid.appendChild(div);
        };
        reader.readAsDataURL(f);
    });
    updateCount();
}

function removeFile(btn) {
    const div = btn.parentElement;
    const idx = parseInt(div.dataset.idx);
    fileList.splice(idx, 1);
    div.remove();
    Array.from(previewGrid.children).forEach((el, i) => el.dataset.idx = i);
    updateCount();
}

function updateCount() {
    const c = fileList.length;
    submitBtn.disabled = c === 0;
    countBadge.textContent = c > 0 ? `${c} صورة محددة` : '';
}

function startUpload() {
    if (!fileList.length) return;

    submitBtn.disabled = true;
    document.getElementById('cancelBtn').style.pointerEvents = 'none';
    document.getElementById('cancelBtn').style.opacity = '0.5';
    document.getElementById('progressWrap').style.display = 'block';

    const fd = new FormData();
    fileList.forEach(f => fd.append('images[]', f));

    const xhr = new XMLHttpRequest();
    xhr.open('POST', window.location.href, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            document.getElementById('progressBar').style.width = Math.round(e.loaded / e.total * 100) + '%';
        }
    };

    xhr.onload = function() {
        document.getElementById('progressBar').style.width = '100%';
        const box = document.getElementById('resultBox');
        box.style.display = 'block';
        try {
            const res = JSON.parse(xhr.responseText);
            if (res.ok) {
                box.innerHTML = `<div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>تم رفع <strong>${res.uploaded}</strong> صورة بنجاح${res.failed ? ` | فشل ${res.failed}` : ''}</div>`;
                setTimeout(() => window.location.href = 'partners.php', 1500);
            } else {
                const errList = (res.errors && res.errors.length) ? '<br><small>' + res.errors.join('<br>') + '</small>' : '';
                box.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>${res.msg || 'فشل الرفع'}${errList}</div>`;
                submitBtn.disabled = false;
                document.getElementById('cancelBtn').style.pointerEvents = '';
                document.getElementById('cancelBtn').style.opacity = '';
            }
        } catch(e) {
            box.innerHTML = '<div class="alert alert-danger">خطأ غير متوقع — يرجى المحاولة مرة أخرى</div>';
            submitBtn.disabled = false;
        }
    };

    xhr.onerror = function() {
        document.getElementById('resultBox').style.display = 'block';
        document.getElementById('resultBox').innerHTML = '<div class="alert alert-danger">فشل الاتصال بالسيرفر</div>';
        submitBtn.disabled = false;
    };

    xhr.send(fd);
}
</script>
JS;
require_once 'includes/footer.php';
?>
