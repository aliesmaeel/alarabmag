(() => {
  const root = document.querySelector('[data-vote-root]');
  if (!root) return;

  const url = root.dataset.voteUrl;
  const csrf = root.dataset.csrf;
  const toastEl = root.querySelector('[data-vote-toast]');
  const statusEl = root.querySelector('[data-vote-status]');
  const totalEl = root.querySelector('[data-total-votes]');
  const dialog = root.querySelector('[data-vote-dialog]');
  const emailForm = root.querySelector('[data-vote-email-form]');
  const emailInput = root.querySelector('[data-voter-email]');
  const emailError = root.querySelector('[data-email-error]');
  const songEl = root.querySelector('[data-dialog-song]');
  const submitBtn = root.querySelector('[data-dialog-submit]');
  const isOpen = root.dataset.open === '1';
  let votedId = root.dataset.voted ? Number(root.dataset.voted) : null;
  let pendingEntryId = null;
  let openerBtn = null;
  const EMAIL_KEY = 'alarab_vote_email';

  const fmt = (n) => Number(n).toLocaleString('en-US');

  function showToast(message) {
    if (!toastEl) return;
    toastEl.hidden = false;
    toastEl.textContent = message;
    toastEl.classList.add('is-on');
    window.setTimeout(() => toastEl.classList.remove('is-on'), 4200);
  }

  function setEmailError(message) {
    if (!emailError || !emailInput) return;
    if (!message) {
      emailError.hidden = true;
      emailError.textContent = '';
      emailInput.classList.remove('is-invalid');
      emailInput.removeAttribute('aria-invalid');
      emailInput.setAttribute('aria-describedby', 'voter-email-hint');
      return;
    }
    emailError.hidden = false;
    emailError.textContent = message;
    emailInput.classList.add('is-invalid');
    emailInput.setAttribute('aria-invalid', 'true');
    emailInput.setAttribute('aria-describedby', 'voter-email-error voter-email-hint');
    emailInput.focus();
  }

  function setButtonIcon(btn, voted) {
    const icon = btn.querySelector('.vote-btn-icon');
    if (!icon) return;
    icon.innerHTML = voted
      ? '<path d="M5 12l5 5L20 7"/>'
      : '<path d="M12 5v14M5 12h14"/>';
  }

  function lockButtons(votedEntryId) {
    root.querySelectorAll('[data-vote-btn]').forEach((btn) => {
      const id = Number(btn.dataset.voteBtn);
      const isVoted = id === votedEntryId;
      btn.disabled = true;
      btn.setAttribute('aria-pressed', isVoted ? 'true' : 'false');
      btn.querySelector('span').textContent = isVoted ? 'صوتك' : 'صوّت';
      setButtonIcon(btn, isVoted);
      const card = btn.closest('.vote-card');
      if (!card) return;
      card.classList.toggle('is-voted', isVoted);
      card.classList.toggle('is-locked', !isVoted);
    });
    if (statusEl) statusEl.textContent = 'تم تسجيل صوتك';
    root.dataset.voted = String(votedEntryId);
    votedId = votedEntryId;
  }

  function applyResults(payload) {
    if (typeof payload.total_votes === 'number' && totalEl) {
      totalEl.textContent = fmt(payload.total_votes);
    }

    (payload.entries || []).forEach((row) => {
      root.querySelectorAll(`[data-votes="${row.id}"]`).forEach((el) => {
        el.textContent = fmt(row.votes);
      });
      root.querySelectorAll(`[data-share="${row.id}"]`).forEach((el) => {
        el.textContent = `${row.share}%`;
      });
      root.querySelectorAll(`[data-bar="${row.id}"]`).forEach((el) => {
        el.style.width = `${row.share}%`;
        const bar = el.closest('.vote-bar');
        if (bar) bar.setAttribute('aria-label', `نسبة الأصوات ${row.share} بالمئة`);
      });
      root.querySelectorAll(`[data-rank="${row.id}"]`).forEach((el) => {
        el.textContent = String(row.rank).padStart(2, '0');
      });
    });
  }

  function closeDialog() {
    if (dialog?.open) dialog.close();
    pendingEntryId = null;
    if (openerBtn && typeof openerBtn.focus === 'function') openerBtn.focus();
    openerBtn = null;
  }

  function openEmailPopup(entryId, btn) {
    const card = btn.closest('.vote-card');
    pendingEntryId = entryId;
    openerBtn = btn;
    if (songEl && card) {
      songEl.textContent = `تصوّت لـ «${card.dataset.title}» — ${card.dataset.artist}`;
    }
    setEmailError('');
    if (emailInput) {
      try {
        emailInput.value = sessionStorage.getItem(EMAIL_KEY) || emailInput.value || '';
      } catch {
        /* ignore */
      }
    }
    if (typeof dialog.showModal === 'function') {
      dialog.showModal();
    } else {
      dialog.setAttribute('open', '');
    }
    window.requestAnimationFrame(() => emailInput?.focus());
  }

  async function submitVote(entryId, email) {
    if (!isOpen || votedId || !submitBtn) return;
    submitBtn.classList.add('is-loading');
    submitBtn.disabled = true;

    try {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ entry_id: entryId, email }),
      });

      const data = await res.json().catch(() => ({}));

      if (res.ok && data.ok) {
        try { sessionStorage.setItem(EMAIL_KEY, email); } catch { /* ignore */ }
        applyResults(data);
        lockButtons(Number(data.voted_entry_id || entryId));
        closeDialog();
        showToast(data.message || 'شكراً — صوتك وصل.');
        return;
      }

      if (res.status === 409) {
        applyResults(data);
        lockButtons(Number(data.voted_entry_id || entryId));
        closeDialog();
        showToast(data.message || 'لقد صوّت من قبل.');
        return;
      }

      const fieldError = data.errors?.email?.[0] || data.message || 'أدخل بريداً صالحاً.';
      setEmailError(fieldError);
    } catch {
      setEmailError('تعذّر الاتصال. حاول مرة أخرى.');
    } finally {
      submitBtn.classList.remove('is-loading');
      submitBtn.disabled = false;
    }
  }

  root.querySelectorAll('[data-vote-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      event.preventDefault();
      const btn = form.querySelector('[data-vote-btn]');
      if (!btn || btn.disabled || votedId) return;
      if (!dialog || !emailInput) {
        showToast('أدخل بريدك لإكمال التصويت.');
        return;
      }
      openEmailPopup(Number(btn.dataset.voteBtn), btn);
    });
  });

  emailForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const email = (emailInput?.value || '').trim();
    if (!email || !emailInput.checkValidity()) {
      setEmailError('أدخل بريداً إلكترونياً صالحاً.');
      return;
    }
    if (!pendingEntryId) return;
    submitVote(pendingEntryId, email);
  });

  root.querySelector('[data-dialog-close]')?.addEventListener('click', closeDialog);

  dialog?.addEventListener('click', (event) => {
    if (event.target === dialog) closeDialog();
  });

  dialog?.addEventListener('cancel', (event) => {
    event.preventDefault();
    closeDialog();
  });

  const flash = root.dataset.flash;
  if (flash) showToast(flash);
})();
