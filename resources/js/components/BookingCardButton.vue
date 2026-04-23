<template>
    <div
        class="booking-card"
        :class="{
            'booking-card--hero': variant === 'hero',
            'booking-card--compact': variant === 'compact',
            'booking-card--done': booking.checked_in,
            'booking-card--busy': checkingId === booking.booking_id,
        }"
    >
        <button
            type="button"
            class="booking-card__tap"
            :disabled="booking.checked_in || checkingId === booking.booking_id"
            :aria-busy="checkingId === booking.booking_id"
            :aria-pressed="booking.checked_in"
            @click="$emit('check', booking)"
        >
            <span class="booking-card__name">{{ displayFirstName(booking.customer_name) }}</span>
            <span class="booking-card__meta">
                <span class="booking-card__time-wrap">
                    <svg class="booking-card__icon booking-card__icon--time" viewBox="0 0 24 24" aria-hidden="true">
                        <path
                            fill="currentColor"
                            d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7V7z"
                        />
                    </svg>
                    <span class="booking-card__time">{{ booking.booking_time }}</span>
                </span>
                <span v-if="booking.manifest_name" class="booking-card__manifest">{{ booking.manifest_name }}</span>
            </span>
            <span v-if="booking.checked_in" class="booking-card__badge">
                <svg class="booking-card__icon booking-card__icon--check" viewBox="0 0 24 24" aria-hidden="true">
                    <path
                        fill="currentColor"
                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"
                    />
                </svg>
                Checked in
            </span>
            <span v-else-if="checkingId === booking.booking_id" class="booking-card__hint booking-card__hint--busy">
                <span class="booking-card__spinner" aria-hidden="true" />
                Saving…
            </span>
            <span v-else class="booking-card__hint">
                <svg class="booking-card__icon booking-card__icon--tap" viewBox="0 0 24 24" aria-hidden="true">
                    <path
                        fill="currentColor"
                        d="M9 11.24V7.5a2.5 2.5 0 0 1 5 0v3.74c1.21-.81 2-2.18 2-3.74C16 5.01 13.99 3 11.5 3S7 5.01 7 7.5c0 1.56.79 2.93 2 3.74zm9.84 4.63l-4.54-2.26c-.17-.07-.35-.11-.54-.11H13v-6c0-.83-.67-1.5-1.5-1.5S10 6.67 10 7.5v10.74l-3.43-.72c-.08-.01-.15-.03-.24-.03-.31 0-.59.13-.79.33l-.79.8 4.94 4.94c.27.27.65.44 1.06.44h6.79c.75 0 1.33-.55 1.44-1.28l.75-5.27c.01-.07.02-.15.02-.22 0-.39-.23-.74-.56-.9z"
                    />
                </svg>
                Tap to check in
            </span>
        </button>
    </div>
</template>

<script setup>
defineProps({
    booking: {
        type: Object,
        required: true,
    },
    checkingId: {
        type: [String, null],
        default: null,
    },
    variant: {
        type: String,
        default: 'default',
        validator: (v) => ['default', 'hero', 'compact'].includes(v),
    },
});

defineEmits(['check']);

function displayFirstName(full) {
    const s = String(full ?? '').trim();
    if (!s) {
        return 'Guest';
    }
    return s.split(/\s+/)[0];
}
</script>

<style scoped>
/* Styles mirror CheckInBoard — scoped here for card only */
.booking-card {
    --accent: #22d3ee;
    --accent-strong: #06b6d4;
    --accent-muted: #67e8f9;
    --surface: #f8fafc;
    --surface-2: #f1f5f9;
    --text: #0f172a;
    --text-muted: #475569;
    --border: #e2e8f0;
    --success: #059669;
    --success-bg: #ecfdf5;
    --success-border: #6ee7b7;
}

.booking-card__tap {
    width: 100%;
    min-height: clamp(150px, 22vw, 200px);
    padding: clamp(1.2rem, 2.8vw, 1.75rem) clamp(1rem, 2.5vw, 1.5rem);
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 0.6rem;
    text-align: left;
    border: 2px solid var(--border);
    border-radius: 1rem;
    background: linear-gradient(180deg, var(--surface) 0%, var(--surface-2) 100%);
    color: var(--text);
    cursor: pointer;
    box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.12), 0 2px 4px -2px rgba(15, 23, 42, 0.08);
    transition:
        transform 0.18s ease,
        border-color 0.18s ease,
        box-shadow 0.18s ease,
        background 0.18s ease;
    touch-action: manipulation;
    -webkit-tap-highlight-color: transparent;
}

.booking-card--hero .booking-card__tap {
    min-height: clamp(180px, 28vw, 260px);
    padding: clamp(1.5rem, 3.2vw, 2.25rem) clamp(1.25rem, 3vw, 2rem);
    border-radius: 1.15rem;
    border-width: 3px;
    border-color: rgba(34, 211, 238, 0.45);
    box-shadow:
        0 4px 6px -1px rgba(15, 23, 42, 0.12),
        0 0 0 1px rgba(34, 211, 238, 0.15),
        0 12px 40px -12px rgba(34, 211, 238, 0.25);
}

.booking-card--hero .booking-card__tap:hover:not(:disabled) {
    border-color: #0891b2;
    box-shadow:
        0 10px 40px -8px rgba(34, 211, 238, 0.4),
        0 4px 10px -4px rgba(15, 23, 42, 0.15);
    background: linear-gradient(165deg, #ecfeff 0%, #a5f3fc 55%, #67e8f9 100%);
}

.booking-card--compact .booking-card__tap {
    min-height: 112px;
    padding: 1rem 1.1rem;
    border-radius: 0.75rem;
    flex-direction: row;
    flex-wrap: wrap;
    align-items: center;
    align-content: center;
    gap: 0.75rem 1rem;
}

.booking-card--compact .booking-card__name {
    font-size: 1rem;
    flex: 1 1 auto;
    min-width: 0;
}

.booking-card--compact .booking-card__meta {
    font-size: 0.85rem;
    margin-left: auto;
    flex: 0 0 auto;
}

.booking-card--compact .booking-card__badge {
    margin-top: 0;
    width: 100%;
    font-size: 0.85rem;
}

.booking-card--compact .booking-card__hint,
.booking-card--compact .booking-card__hint--busy {
    margin-top: 0;
    width: 100%;
    font-size: 0.8rem;
}

.booking-card__tap:hover:not(:disabled) {
    border-color: #0891b2;
    box-shadow:
        0 10px 28px -6px rgba(34, 211, 238, 0.35),
        0 4px 12px -4px rgba(8, 145, 178, 0.2);
    background: linear-gradient(165deg, #f0fdfa 0%, #ccfbf1 45%, #99f6e4 100%);
}

.booking-card--compact .booking-card__tap:hover:not(:disabled) {
    background: linear-gradient(165deg, #ecfeff 0%, #cffafe 50%, #a5f3fc 100%);
}

.booking-card__tap:focus {
    outline: none;
}

.booking-card__tap:focus-visible {
    outline: 3px solid var(--accent);
    outline-offset: 3px;
}

.booking-card__tap:active:not(:disabled) {
    transform: scale(0.985);
}

.booking-card__tap:disabled {
    cursor: default;
}

.booking-card--done .booking-card__tap {
    border-color: var(--success-border);
    background: linear-gradient(180deg, var(--success-bg) 0%, #d1fae5 100%);
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.15);
}

.booking-card--busy .booking-card__tap {
    pointer-events: none;
    border-color: var(--accent-muted);
    animation: card-pulse 1s ease-in-out infinite;
}

@keyframes card-pulse {
    0%,
    100% {
        box-shadow: 0 4px 6px rgba(15, 23, 42, 0.1);
    }
    50% {
        box-shadow: 0 8px 24px rgba(34, 211, 238, 0.25);
    }
}

.booking-card--hero .booking-card__name {
    font-size: clamp(1.5rem, 4vw, 2.1rem);
}

.booking-card__name {
    font-size: clamp(1.25rem, 3.2vw, 1.75rem);
    font-weight: 700;
    line-height: 1.25;
    word-break: break-word;
    color: var(--text);
}

.booking-card__meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem 1rem;
    font-size: 1.05rem;
    color: var(--text-muted);
}

.booking-card__time-wrap {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
}

.booking-card__time {
    font-variant-numeric: tabular-nums;
    font-weight: 700;
    font-size: 1.1rem;
    letter-spacing: 0.02em;
    color: var(--text);
}

.booking-card__icon {
    width: 1.15em;
    height: 1.15em;
    flex-shrink: 0;
    opacity: 0.95;
}

.booking-card__icon--time {
    color: var(--accent-strong);
}

.booking-card__icon--tap {
    color: #0e7490;
}

.booking-card__icon--check {
    color: var(--success);
}

.booking-card__manifest {
    font-size: 0.95rem;
    color: #64748b;
}

.booking-card__badge {
    margin-top: auto;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    font-size: 1rem;
    font-weight: 700;
    color: var(--success);
}

.booking-card__hint {
    margin-top: auto;
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.95rem;
    font-weight: 600;
    color: #0e7490;
}

.booking-card__hint--busy {
    color: #155e75;
}

.booking-card__spinner {
    width: 1em;
    height: 1em;
    border: 2px solid #cbd5e1;
    border-top-color: var(--accent-strong);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.booking-card--done .booking-card__name {
    color: #065f46;
}

.booking-card--done .booking-card__meta {
    color: #047857;
}
</style>
