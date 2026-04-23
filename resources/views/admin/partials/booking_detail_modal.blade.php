<div id="booking-modal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="booking-modal-title" aria-hidden="true">
    <div class="modal-panel">
        <div class="modal-panel__head">
            <h2 id="booking-modal-title">Booking detail</h2>
            <button type="button" class="modal-panel__close" id="booking-modal-close" aria-label="Close">&times;</button>
        </div>
        <div class="modal-panel__body" id="booking-modal-body">
            <p style="color: var(--muted); margin: 0;">Loading…</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('booking-modal');
    const bodyEl = document.getElementById('booking-modal-body');
    const closeBtn = document.getElementById('booking-modal-close');
    if (!modal || !bodyEl || !closeBtn) return;

    function openModal() {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    function appendRow(dl, label, value) {
        const dt = document.createElement('dt');
        dt.textContent = label;
        const dd = document.createElement('dd');
        if (value === null || value === undefined || value === '') {
            dd.textContent = '—';
        } else {
            dd.textContent = String(value);
        }
        dl.appendChild(dt);
        dl.appendChild(dd);
    }

    function renderDetail(d) {
        const dl = document.createElement('dl');
        dl.className = 'detail-dl';

        appendRow(dl, 'Database ID', d.id);
        appendRow(dl, 'Booking ID (Periode)', d.booking_id);
        appendRow(dl, 'Manifest ID', d.manifest_id);

        if (d.manifest_from_db) {
            appendRow(dl, 'Manifest (from database)', d.manifest_from_db.name + ' — ' + d.manifest_from_db.manifest_id);
        } else {
            appendRow(dl, 'Manifest (from database)', 'No matching row');
        }
        appendRow(dl, 'Manifest name on booking', d.manifest_name_on_booking);
        appendRow(dl, 'Merchant ID', d.merchant_id);
        appendRow(dl, 'Customer name', d.customer_name);
        appendRow(dl, 'Customer email', d.customer_email);
        appendRow(dl, 'Customer phone', d.customer_phone);
        appendRow(dl, 'Booking date', d.booking_date);
        appendRow(dl, 'Booking time (raw)', d.booking_time_raw);
        appendRow(dl, 'Booking time (display)', d.booking_time_display);
        appendRow(dl, 'Duration (minutes)', d.duration);
        appendRow(dl, 'Price', d.price_formatted ?? d.price);
        appendRow(dl, 'Currency', d.currency);
        appendRow(dl, 'Payment type', d.payment_type);
        appendRow(dl, 'State', d.state);
        appendRow(dl, 'Checked in', d.checked_in ? 'Yes' : 'No');
        appendRow(dl, 'Checked in at', d.checked_in_at_local || d.checked_in_at);
        appendRow(dl, 'Synced at', d.synced_at);
        appendRow(dl, 'Created at', d.created_at);
        appendRow(dl, 'Updated at', d.updated_at);

        const rawDt = document.createElement('dt');
        rawDt.textContent = 'Raw payload (API)';
        const rawDd = document.createElement('dd');
        const pre = document.createElement('pre');
        pre.textContent = d.raw_payload !== null && d.raw_payload !== undefined
            ? JSON.stringify(d.raw_payload, null, 2)
            : '—';
        rawDd.appendChild(pre);
        dl.appendChild(rawDt);
        dl.appendChild(rawDd);

        bodyEl.innerHTML = '';
        bodyEl.appendChild(dl);
    }

    document.querySelectorAll('.js-booking-detail').forEach(function (btn) {
        btn.addEventListener('click', async function () {
            const url = btn.getAttribute('data-detail-url');
            bodyEl.innerHTML = '<p style="color: var(--muted); margin: 0;">Loading…</p>';
            openModal();
            try {
                const res = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                if (!res.ok) {
                    bodyEl.innerHTML = '<p style="color: #fecaca;">Could not load booking.</p>';
                    return;
                }
                const json = await res.json();
                renderDetail(json.data);
            } catch (e) {
                bodyEl.innerHTML = '<p style="color: #fecaca;">Network error.</p>';
            }
        });
    });

    closeBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });
})();
</script>
@endpush
