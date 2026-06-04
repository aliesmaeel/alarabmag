function formatTime(sec) {
  if (!Number.isFinite(sec) || sec < 0) return '0:00';
  const m = Math.floor(sec / 60);
  const s = Math.floor(sec % 60);
  return `${m}:${String(s).padStart(2, '0')}`;
}

function initVideoPlayer(shareTitle) {
  const player = document.getElementById('videoPlayer');
  const video = document.getElementById('interviewVideo');
  if (!player || !video) return;

  const playBtn = document.getElementById('playBtn');
  const bigPlayBtn = document.getElementById('bigPlayBtn');
  const overlay = document.getElementById('videoOverlay');
  const muteBtn = document.getElementById('muteBtn');
  const fullscreenBtn = document.getElementById('fullscreenBtn');
  const speedSelect = document.getElementById('speedSelect');
  const progressRange = document.getElementById('progressRange');
  const progressFill = document.getElementById('progressFill');
  const timeDisplay = document.getElementById('timeDisplay');
  const shareBtn = document.getElementById('shareBtn');
  const shareMenu = document.getElementById('shareMenu');
  const videoError = document.getElementById('videoError');
  const pageUrl = location.href;
  const title = shareTitle || document.title;

  const updatePlayIcon = () => {
    const icon = video.paused ? '▶' : '⏸';
    if (playBtn) playBtn.textContent = icon;
    player.classList.toggle('is-playing', !video.paused);
    if (overlay) overlay.classList.toggle('is-hidden', !video.paused);
  };

  const togglePlay = () => {
    if (video.paused) {
      if (video.preload === 'none') video.preload = 'auto';
      video.play().catch(() => {});
    } else {
      video.pause();
    }
  };

  playBtn?.addEventListener('click', togglePlay);
  bigPlayBtn?.addEventListener('click', togglePlay);
  video.addEventListener('click', togglePlay);

  video.addEventListener('play', updatePlayIcon);
  video.addEventListener('pause', updatePlayIcon);

  muteBtn?.addEventListener('click', () => {
    video.muted = !video.muted;
    muteBtn.textContent = video.muted ? '🔇' : '🔊';
    muteBtn.title = video.muted ? 'إلغاء الكتم' : 'كتم الصوت';
  });

  speedSelect?.addEventListener('change', () => {
    video.playbackRate = parseFloat(speedSelect.value) || 1;
  });

  const updateProgress = () => {
    const pct = video.duration ? (video.currentTime / video.duration) * 100 : 0;
    if (progressRange) progressRange.value = pct;
    if (progressFill) progressFill.style.width = `${pct}%`;
    if (timeDisplay) {
      timeDisplay.textContent = `${formatTime(video.currentTime)} / ${formatTime(video.duration)}`;
    }
  };

  video.addEventListener('timeupdate', updateProgress);
  video.addEventListener('loadedmetadata', updateProgress);

  progressRange?.addEventListener('input', () => {
    if (video.duration) {
      video.currentTime = (parseFloat(progressRange.value) / 100) * video.duration;
    }
  });

  fullscreenBtn?.addEventListener('click', () => {
    if (document.fullscreenElement) {
      document.exitFullscreen?.();
    } else {
      player.requestFullscreen?.() || player.webkitRequestFullscreen?.();
    }
  });

  document.getElementById('shareTwitter')?.setAttribute(
    'href',
    `https://twitter.com/intent/tweet?url=${encodeURIComponent(pageUrl)}&text=${encodeURIComponent(title)}`
  );
  document.getElementById('shareFacebook')?.setAttribute(
    'href',
    `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(pageUrl)}`
  );
  document.getElementById('shareWhatsapp')?.setAttribute(
    'href',
    `https://wa.me/?text=${encodeURIComponent(title + ' — ' + pageUrl)}`
  );

  shareBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    if (navigator.share) {
      navigator.share({ title, url: pageUrl }).catch(() => {
        if (shareMenu) shareMenu.hidden = !shareMenu.hidden;
      });
      return;
    }
    if (shareMenu) shareMenu.hidden = !shareMenu.hidden;
  });

  document.getElementById('shareCopy')?.addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(pageUrl);
      const btn = document.getElementById('shareCopy');
      const label = btn?.querySelector('.vp-share-item__label');
      if (label) {
        const prev = label.textContent;
        label.textContent = 'تم النسخ ✓';
        setTimeout(() => { label.textContent = prev; }, 1600);
      }
    } catch {}
  });

  document.addEventListener('click', () => {
    if (shareMenu) shareMenu.hidden = true;
  });

  video.addEventListener('error', () => {
    if (videoError) videoError.hidden = false;
  });

  updatePlayIcon();
}

document.addEventListener('DOMContentLoaded', () => {
  const title = document.getElementById('videoPlayer')?.dataset?.shareTitle;
  initVideoPlayer(title);
});
