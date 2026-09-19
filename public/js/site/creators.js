(() => {
  const root = document.querySelector('[data-creators-root]');
  if (!root) return;

  const form = document.getElementById('creatorForm');
  if (!form) return;

  const url = root.dataset.creatorsUrl;
  const csrf = root.dataset.csrf;
  const msgEl = root.querySelector('[data-form-msg]');
  const socialRows = form.querySelector('[data-social-rows]');
  const socialAddBtn = form.querySelector('[data-social-add]');
  const submitBtn = form.querySelector('[data-submit]');
  const submitLabel = form.querySelector('[data-submit-label]');
  const defaultLabel = submitLabel ? submitLabel.textContent : '';

  const MESSAGES = {
    name: 'أدخل اسمك الكامل.',
    email: 'أدخل بريداً إلكترونياً صالحاً.',
    country: 'اختر الدولة.',
    phone: 'أدخل رقم التواصل.',
    niche: 'اختر مجال المحتوى.',
    social_links: 'أضف رابطاً صالحاً واحداً على الأقل يبدأ بـ https://',
    bio: 'اكتب نبذة عنك وعن محتواك (20 حرفاً على الأقل).',
  };

  function showMessage(text, isError) {
    if (!msgEl) return;
    msgEl.hidden = false;
    msgEl.textContent = text;
    msgEl.classList.toggle('is-error', Boolean(isError));
    msgEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  const MAX_SOCIAL_LINKS = 8;

  // Resolves a validation key ("bio", "social_links.2") to its input.
  function fieldFor(name) {
    const [base, index] = String(name).split('.');

    return base === 'social_links'
      ? socialInputs()[Number(index) || 0]
      : form.querySelector(`[name="${base}"]`);
  }

  function fieldError(name, message) {
    const base = String(name).split('.')[0];
    const field = fieldFor(name);
    const errorEl = form.querySelector(`[data-error-for="${base}"]`);

    if (field) {
      field.classList.toggle('is-invalid', Boolean(message));
      if (message) field.setAttribute('aria-invalid', 'true');
      else field.removeAttribute('aria-invalid');
    }
    if (errorEl) {
      errorEl.hidden = !message;
      errorEl.textContent = message || '';
    }
  }

  function clearErrors() {
    form.querySelectorAll('[data-error-for]').forEach((el) => {
      el.hidden = true;
      el.textContent = '';
    });
    form.querySelectorAll('.is-invalid').forEach((el) => {
      el.classList.remove('is-invalid');
      el.removeAttribute('aria-invalid');
    });
  }

  // ── Dynamic social links ──────────────────────────────
  function socialInputs() {
    return socialRows ? Array.from(socialRows.querySelectorAll('input[name="social_links[]"]')) : [];
  }

  function syncSocialRows() {
    const inputs = socialInputs();
    inputs.forEach((input, index) => {
      input.id = `cf-social-${index}`;
      if (index === 0) input.setAttribute('required', 'required');
      else input.removeAttribute('required');
      const removeBtn = input.parentElement.querySelector('[data-social-remove]');
      if (removeBtn) removeBtn.hidden = inputs.length === 1;
    });
    if (socialAddBtn) socialAddBtn.hidden = inputs.length >= MAX_SOCIAL_LINKS;
  }

  function addSocialRow() {
    const inputs = socialInputs();
    if (!socialRows || inputs.length >= MAX_SOCIAL_LINKS) return;

    const row = inputs[0].parentElement.cloneNode(true);
    const input = row.querySelector('input');
    input.value = '';
    input.classList.remove('is-invalid');
    input.removeAttribute('aria-invalid');
    socialRows.appendChild(row);
    syncSocialRows();
    input.focus();
  }

  if (socialRows) {
    socialRows.addEventListener('click', (event) => {
      const btn = event.target.closest('[data-social-remove]');
      if (!btn) return;
      if (socialInputs().length <= 1) return;
      btn.closest('[data-social-row]').remove();
      fieldError('social_links', '');
      syncSocialRows();
    });
    syncSocialRows();
  }

  if (socialAddBtn) {
    socialAddBtn.addEventListener('click', addSocialRow);
  }

  function validateSocialLinks() {
    const inputs = socialInputs();
    const filled = inputs.filter((input) => input.value.trim() !== '');

    if (filled.length === 0) {
      fieldError('social_links', MESSAGES.social_links);
      return inputs[0] || null;
    }

    const invalid = filled.find((input) => !input.checkValidity());
    if (invalid) {
      fieldError('social_links', 'أدخل روابط صالحة تبدأ بـ https://');
      invalid.classList.add('is-invalid');
      return invalid;
    }

    // Drop empty extra rows so they are not submitted.
    inputs.forEach((input) => {
      if (input.value.trim() === '' && socialInputs().length > 1) {
        input.closest('[data-social-row]').remove();
      }
    });
    syncSocialRows();

    return null;
  }

  function validate() {
    clearErrors();
    let firstInvalid = null;

    form.querySelectorAll('[required]').forEach((el) => {
      if (el.name === 'social_links[]') return;

      let message = '';
      if (!el.checkValidity()) {
        message = MESSAGES[el.name] || 'هذا الحقل مطلوب.';
      } else if (el.name === 'bio' && el.value.trim().length < 20) {
        message = MESSAGES.bio;
      }

      if (message) {
        fieldError(el.name, message);
        if (!firstInvalid) firstInvalid = el;
      }
    });

    const invalidSocial = validateSocialLinks();
    if (invalidSocial && !firstInvalid) firstInvalid = invalidSocial;

    return firstInvalid;
  }

  function setBusy(busy) {
    if (submitBtn) submitBtn.disabled = busy;
    if (submitLabel) submitLabel.textContent = busy ? 'جارٍ الإرسال…' : defaultLabel;
  }

  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const firstInvalid = validate();
    if (firstInvalid) {
      showMessage('يرجى تعبئة الحقول المطلوبة بشكل صحيح.', true);
      firstInvalid.focus();
      return;
    }

    setBusy(true);

    try {
      const response = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
          Accept: 'application/json',
        },
        body: new FormData(form),
      });

      const payload = await response.json().catch(() => ({}));

      if (response.ok && payload.ok) {
        showMessage(payload.message || 'تم إرسال طلبك بنجاح.', false);
        form.reset();
        clearErrors();
        socialInputs().forEach((input, index) => {
          if (index > 0) input.closest('[data-social-row]').remove();
        });
        syncSocialRows();
        return;
      }

      if (payload.errors) {
        Object.keys(payload.errors).forEach((key) => {
          fieldError(key, payload.errors[key][0]);
        });
        const firstField = fieldFor(Object.keys(payload.errors)[0]);
        if (firstField) firstField.focus();
      }

      showMessage(payload.message || 'تعذّر إرسال الطلب. يرجى المحاولة مرة أخرى.', true);
    } catch (error) {
      showMessage('تعذّر الاتصال بالخادم. يرجى المحاولة مرة أخرى.', true);
    } finally {
      setBusy(false);
    }
  });

  // Server-side flash (non-JS fallback submissions)
  const flash = root.dataset.flash;
  if (flash) {
    showMessage(flash, root.dataset.flashOk !== '1');
  }
})();
