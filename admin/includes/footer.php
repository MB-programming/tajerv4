</div><!-- /main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Image preview on file change
document.querySelectorAll('input[type=file]').forEach(inp => {
  inp.addEventListener('change', function(){
    const prev = this.closest('.form-group,div')?.querySelector('img.img-preview');
    if(prev && this.files && this.files[0]){
      const r = new FileReader();
      r.onload = e => { prev.src=e.target.result; prev.style.display='block'; };
      r.readAsDataURL(this.files[0]);
    }
  });
});

// Confirm delete
document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
  btn.addEventListener('click', e => {
    if(!confirm('هل أنت متأكد من الحذف؟ لا يمكن التراجع.')) e.preventDefault();
  });
});

// ── Bulk Actions ──────────────────────────────────────────────────────────────
(function(){
  const bar       = document.getElementById('bulkBar');
  const form      = document.getElementById('bulkForm');
  const selAll    = document.getElementById('selectAll');
  const actionInp = document.getElementById('bulkActionInput');
  if (!bar || !form) return;

  function getChecked() { return [...document.querySelectorAll('.row-check:checked')]; }
  function getAllCbs()   { return [...document.querySelectorAll('.row-check')]; }

  function updateBar() {
    const n = getChecked().length;
    bar.classList.toggle('show', n > 0);
    const badge = bar.querySelector('.bulk-count');
    if (badge) badge.textContent = n + ' محدد';
    if (selAll) {
      const total = getAllCbs().length;
      selAll.checked       = n > 0 && n === total;
      selAll.indeterminate = n > 0 && n < total;
    }
  }

  document.addEventListener('change', function(e) {
    if (e.target.id === 'selectAll') {
      getAllCbs().forEach(cb => cb.checked = e.target.checked);
    }
    if (e.target.matches('.row-check') || e.target.id === 'selectAll') updateBar();
  });

  document.getElementById('applyBulk')?.addEventListener('click', function() {
    const action = document.getElementById('bulkAction')?.value;
    if (!action) { alert('اختر إجراءً أولاً'); return; }
    const checked = getChecked();
    if (!checked.length) { alert('لم تحدد أي عناصر'); return; }
    if (action === 'delete' && !confirm('هل أنت متأكد من حذف ' + checked.length + ' عنصر؟')) return;
    if (actionInp) actionInp.value = action;
    form.submit();
  });

  document.getElementById('cancelBulk')?.addEventListener('click', function() {
    getAllCbs().forEach(cb => cb.checked = false);
    if (selAll) { selAll.checked = false; selAll.indeterminate = false; }
    updateBar();
  });
})();
</script>
<?php if (isset($extraScript)) echo $extraScript; ?>
</body>
</html>
