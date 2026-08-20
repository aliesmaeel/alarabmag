@if ($isOpen && ! $votedEntryId)
    <dialog class="vote-dialog" data-vote-dialog aria-labelledby="vote-dialog-title">
        <form class="vote-dialog-card" data-vote-email-form novalidate>
            <button type="button" class="vote-dialog-close" data-dialog-close aria-label="إغلاق">
                <svg viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M18 6L6 18M6 6l12 12"/>
                </svg>
            </button>
            <p class="vote-dialog-kicker">صوت واحد</p>
            <h2 id="vote-dialog-title" class="vote-dialog-title">أدخل بريدك لإكمال الصوت</h2>
            <p class="vote-dialog-song" data-dialog-song></p>
            <label class="vote-dialog-label" for="voter-email">البريد الإلكتروني</label>
            <input
                class="vote-dialog-input"
                type="email"
                id="voter-email"
                name="email"
                autocomplete="email"
                inputmode="email"
                required
                maxlength="255"
                placeholder="name@email.com"
                aria-describedby="voter-email-hint"
                data-voter-email
            >
            <p class="vote-dialog-error" id="voter-email-error" data-email-error role="alert" hidden></p>
            <p class="vote-dialog-hint" id="voter-email-hint">للتأكيد فقط — لن نشارك بريدك، ونستخدمه لإعلان النتيجة.</p>
            <button type="submit" class="vote-btn vote-dialog-submit" data-dialog-submit>
                <svg class="vote-btn-icon" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12l5 5L20 7"/>
                </svg>
                <span>تأكيد الصوت</span>
            </button>
        </form>
    </dialog>
@endif
