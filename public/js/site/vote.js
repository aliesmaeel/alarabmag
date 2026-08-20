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

  const audio = root.querySelector('[data-vote-audio]');
  const player = root.querySelector('[data-vote-player]');
  const PLAY_ICON = '<path d="M8 5.14v14l11-7-11-7z"/>';
  const PAUSE_ICON = '<path d="M6 5h4v14H6zm8 0h4v14h-4z"/>';
  const SPEEDS = [0.75, 1, 1.25, 1.5, 2];
  const SPEED_KEY = 'alarab_vote_speed';
  const SEEK_MAX = 1000;
  let playingId = null;
  let seeking = false;

  const playerTitle = player?.querySelector('[data-player-title]');
  const playerArtist = player?.querySelector('[data-player-artist]');
  const playerCover = player?.querySelector('[data-player-cover]');
  const playerToggle = player?.querySelector('[data-player-toggle]');
  const playerToggleIcon = player?.querySelector('[data-player-toggle-icon]');
  const playerCurrent = player?.querySelector('[data-player-current]');
  const playerDuration = player?.querySelector('[data-player-duration]');
  const playerSeek = player?.querySelector('[data-player-seek]');
  const playerSeekFill = player?.querySelector('[data-player-seek-fill]');
  const playerSeekThumb = player?.querySelector('[data-player-seek-thumb]');
  const playerSpeed = player?.querySelector('[data-player-speed]');

  function formatTime(seconds) {
    if (!Number.isFinite(seconds) || seconds < 0) return '0:00';
    const total = Math.floor(seconds);
    const m = Math.floor(total / 60);
    const s = total % 60;
    return `${m}:${String(s).padStart(2, '0')}`;
  }

  function readSpeed() {
    try {
      const stored = Number(sessionStorage.getItem(SPEED_KEY));
      if (SPEEDS.includes(stored)) return stored;
    } catch {
      /* ignore */
    }
    return 1;
  }

  function writeSpeed(rate) {
    try { sessionStorage.setItem(SPEED_KEY, String(rate)); } catch { /* ignore */ }
  }

  function currentSpeed() {
    const rate = audio.playbackRate;
    return SPEEDS.find((s) => Math.abs(s - rate) < 0.01) || readSpeed();
  }

  function setSpeed(rate) {
    const next = SPEEDS.includes(rate) ? rate : 1;
    audio.playbackRate = next;
    const label = `${next}×`;
    if (playerSpeed) {
      playerSpeed.textContent = label;
      playerSpeed.setAttribute('aria-label', `سرعة التشغيل ${label}`);
    }
    writeSpeed(next);
  }

  function showPlayer() {
    if (!player) return;
    player.hidden = false;
    root.classList.add('is-player-on');
  }

  function fillPlayer(btn) {
    if (playerTitle) playerTitle.textContent = btn.dataset.playTitle || '';
    if (playerArtist) playerArtist.textContent = btn.dataset.playArtist || '';
    if (playerCover) {
      playerCover.src = btn.dataset.playCover || '';
      playerCover.alt = btn.dataset.playTitle || '';
    }
  }

  function setSeekUi(ratio) {
    const clamped = Math.min(1, Math.max(0, ratio));
    const pct = `${(clamped * 100).toFixed(2)}%`;
    if (playerSeekFill) playerSeekFill.style.width = pct;
    if (playerSeekThumb) playerSeekThumb.style.left = pct;
    if (playerSeek) {
      const now = Math.round(clamped * SEEK_MAX);
      playerSeek.setAttribute('aria-valuenow', String(now));
    }
  }

  function seekToRatio(ratio, { resume = false } = {}) {
    const duration = audio.duration;
    if (!Number.isFinite(duration) || duration <= 0) return;

    const next = Math.min(duration, Math.max(0, ratio * duration));
    const shouldPlay = resume && audio.paused;

    try {
      audio.currentTime = next;
    } catch {
      return;
    }

    setSeekUi(next / duration);
    if (playerCurrent) playerCurrent.textContent = formatTime(next);

    if (shouldPlay) {
      const start = () => {
        audio.play().then(() => markPlaying(playingId, true)).catch(() => {
          showToast('تعذّر تشغيل الأغنية.');
        });
      };

      if (audio.seeking) {
        audio.addEventListener('seeked', start, { once: true });
      } else {
        start();
      }
    }
  }

  function ratioFromPointer(event) {
    const rect = playerSeek.getBoundingClientRect();
    if (rect.width <= 0) return 0;
    return Math.min(1, Math.max(0, (event.clientX - rect.left) / rect.width));
  }

  function updateTimes() {
    const current = audio.currentTime || 0;
    const duration = audio.duration;
    if (playerCurrent) playerCurrent.textContent = formatTime(current);
    if (playerDuration) {
      playerDuration.textContent = Number.isFinite(duration) ? formatTime(duration) : '0:00';
    }
    if (!seeking && Number.isFinite(duration) && duration > 0) {
      setSeekUi(current / duration);
      if (playerSeek) {
        playerSeek.setAttribute('aria-valuetext', `${formatTime(current)} من ${formatTime(duration)}`);
      }
    }
  }

  function markPlaying(id, playing) {
    root.querySelectorAll('[data-play-id]').forEach((btn) => {
      const on = playing && Number(btn.dataset.playId) === id;
      btn.classList.toggle('is-playing', on);
      btn.setAttribute('aria-pressed', on ? 'true' : 'false');
      const label = btn.querySelector('[data-play-label]');
      if (label) label.textContent = on ? 'إيقاف' : 'اسمع';
      const icon = btn.querySelector('[data-play-icon]');
      if (icon) icon.innerHTML = on ? PAUSE_ICON : PLAY_ICON;
    });
    if (playerToggle) {
      playerToggle.setAttribute('aria-label', playing ? 'إيقاف' : 'تشغيل');
    }
    if (playerToggleIcon) {
      playerToggleIcon.innerHTML = playing ? PAUSE_ICON : PLAY_ICON;
    }
  }

  function playFromButton(btn) {
    const src = btn.dataset.playSrc;
    const id = Number(btn.dataset.playId);
    if (!src) return;

    if (playingId === id && !audio.paused) {
      audio.pause();
      return;
    }

    if (audio.getAttribute('src') !== src) {
      audio.src = src;
    }

    fillPlayer(btn);
    showPlayer();
    setSpeed(readSpeed());

    audio.play().then(() => {
      playingId = id;
      markPlaying(id, true);
      updateTimes();
    }).catch(() => {
      playingId = null;
      markPlaying(id, false);
      showToast('تعذّر تشغيل الأغنية.');
    });
  }

  if (audio) {
    root.querySelectorAll('[data-play-src]').forEach((btn) => {
      btn.addEventListener('click', () => playFromButton(btn));
    });

    playerToggle?.addEventListener('click', () => {
      if (!audio.getAttribute('src')) return;
      if (audio.paused) {
        audio.play().then(() => markPlaying(playingId, true)).catch(() => {
          showToast('تعذّر تشغيل الأغنية.');
        });
      } else {
        audio.pause();
      }
    });

    playerSpeed?.addEventListener('click', () => {
      const current = currentSpeed();
      const next = SPEEDS[(SPEEDS.indexOf(current) + 1) % SPEEDS.length];
      setSpeed(next);
    });

    playerSeek?.addEventListener('pointerdown', (event) => {
      if (event.button !== undefined && event.button !== 0) return;
      event.preventDefault();
      seeking = true;
      try {
        playerSeek.setPointerCapture(event.pointerId);
      } catch {
        /* ignore */
      }
      seekToRatio(ratioFromPointer(event), { resume: true });
    });

    playerSeek?.addEventListener('pointermove', (event) => {
      if (!seeking) return;
      seekToRatio(ratioFromPointer(event));
    });

    playerSeek?.addEventListener('pointerup', (event) => {
      if (seeking) {
        seekToRatio(ratioFromPointer(event), { resume: true });
      }
      seeking = false;
    });

    playerSeek?.addEventListener('pointercancel', () => {
      seeking = false;
    });

    playerSeek?.addEventListener('keydown', (event) => {
      const duration = audio.duration;
      if (!Number.isFinite(duration) || duration <= 0) return;
      const step = duration * 0.05;
      if (event.key === 'ArrowRight' || event.key === 'ArrowUp') {
        event.preventDefault();
        seekToRatio(Math.min(1, (audio.currentTime + step) / duration), { resume: true });
      }
      if (event.key === 'ArrowLeft' || event.key === 'ArrowDown') {
        event.preventDefault();
        seekToRatio(Math.max(0, (audio.currentTime - step) / duration), { resume: true });
      }
    });

    audio.addEventListener('loadedmetadata', updateTimes);
    audio.addEventListener('timeupdate', updateTimes);
    audio.addEventListener('durationchange', updateTimes);

    audio.addEventListener('pause', () => {
      markPlaying(playingId, false);
    });

    audio.addEventListener('play', () => {
      audio.playbackRate = currentSpeed();
      markPlaying(playingId, true);
    });

    audio.addEventListener('ended', () => {
      markPlaying(playingId, false);
      setSeekUi(1);
      if (playerCurrent && Number.isFinite(audio.duration)) {
        playerCurrent.textContent = formatTime(audio.duration);
      }
    });
  }
})();
