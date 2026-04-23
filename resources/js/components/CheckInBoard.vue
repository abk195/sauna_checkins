<template>
    <div class="board">
        <header class="board__header">
            <h1 class="board__title">Sauna Bookings</h1>
            <p class="board__date">{{ todayLabel }}</p>
            <p v-if="manifestLabel" class="board__subtitle">{{ manifestLabel }}</p>
        </header>

        <p v-if="error" class="board__error" role="alert">{{ error }}</p>

        <div v-if="loading && !bookings.length" class="board__loading" aria-live="polite">
            <span class="board__loading-dot" />
            <span class="board__loading-dot" />
            <span class="board__loading-dot" />
            <span class="board__loading-text">Loading schedule…</span>
        </div>

        <div v-else-if="!bookings.length && !error" class="board__empty">
            <span class="board__empty-icon" aria-hidden="true">📋</span>
            <p>No upcoming bookings for today.</p>
        </div>

        <div v-else class="board__main">
            <div class="board__grid">
                <BookingCardButton
                    v-for="b in bookings"
                    :key="b.booking_id"
                    :booking="b"
                    :checking-id="checkingId"
                    variant="default"
                    @check="checkIn"
                />
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';
import axios from 'axios';
import BookingCardButton from './BookingCardButton.vue';

const bookings = ref([]);
const loading = ref(true);
const error = ref('');
const checkingId = ref(null);

const manifestId = ref(null);
/** Display name from DB when opened via /manifest/{id} */
const manifestName = ref(null);

const manifestLabel = computed(() => {
    if (manifestName.value) {
        return manifestName.value;
    }
    if (manifestId.value) {
        return `Manifest: ${manifestId.value}`;
    }
    return '';
});

/** Matches Laravel APP_TIMEZONE (add VITE_APP_TIMEZONE in .env to override in the browser). */
const appTimeZone = import.meta.env.VITE_APP_TIMEZONE || 'Europe/Oslo';

const todayLabel = computed(() => {
    try {
        return new Intl.DateTimeFormat('en-GB', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
            timeZone: appTimeZone,
        }).format(new Date());
    } catch {
        return new Date().toLocaleDateString(undefined, {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });
    }
});

/**
 * Manifest filter: meta (from /manifest/{id} Blade) > path /manifest/... > ?manifest_id=
 * Home /checkin has no meta — shows all bookings.
 */
function readManifestContext() {
    manifestName.value = null;

    const metaId = document.querySelector('meta[name="checkin-manifest-id"]')?.getAttribute('content')?.trim();
    const metaName = document.querySelector('meta[name="checkin-manifest-name"]')?.getAttribute('content')?.trim();

    if (metaId) {
        manifestId.value = metaId;
        manifestName.value = metaName || null;
        return;
    }

    const pathMatch = window.location.pathname.match(/^\/manifest\/([^/]+)\/?$/);
    if (pathMatch?.[1]) {
        manifestId.value = decodeURIComponent(pathMatch[1]);
        return;
    }

    const params = new URLSearchParams(window.location.search);
    const q = params.get('manifest_id');
    manifestId.value = q && q.length ? q : null;
}

async function fetchBookings(silent = false) {
    if (!silent) {
        loading.value = true;
    }
    error.value = '';
    try {
        const params = { upcoming_only: false };
        if (manifestId.value) {
            params.manifest_id = manifestId.value;
        }
        const { data } = await axios.get('/api/bookings/today', { params });
        bookings.value = Array.isArray(data) ? data : [];
    } catch (e) {
        error.value =
            e.response?.data?.message || e.message || 'Could not load bookings.';
        bookings.value = [];
    } finally {
        if (!silent) {
            loading.value = false;
        }
    }
}

async function checkIn(booking) {
    if (booking.checked_in || checkingId.value === booking.booking_id) {
        return;
    }
    checkingId.value = booking.booking_id;
    error.value = '';
    try {
        const { data } = await axios.post(`/api/bookings/${encodeURIComponent(booking.booking_id)}/check-in`);
        const idx = bookings.value.findIndex((x) => x.booking_id === data.booking_id);
        if (idx !== -1) {
            bookings.value[idx] = { ...bookings.value[idx], ...data };
        }
    } catch (e) {
        error.value =
            e.response?.data?.message || e.message || 'Check-in failed.';
    } finally {
        checkingId.value = null;
    }
}

let pollTimer = null;

onMounted(() => {
    readManifestContext();
    fetchBookings(false);
    pollTimer = window.setInterval(() => fetchBookings(true), 10000);
});

onUnmounted(() => {
    if (pollTimer) {
        clearInterval(pollTimer);
    }
});
</script>

<style scoped>
.board {
    --board-pad: clamp(0.75rem, 2vw, 1.5rem);
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
    width: 100%;
    max-width: none;
    min-height: 100vh;
    margin: 0;
    padding: 1.25rem var(--board-pad) clamp(2.5rem, 6vw, 4rem);
    background:
        radial-gradient(ellipse 100% 70% at 80% 0%, rgba(34, 211, 238, 0.12), transparent 45%),
        radial-gradient(ellipse 80% 50% at 10% 100%, rgba(99, 102, 241, 0.1), transparent 50%),
        linear-gradient(165deg, #0f172a 0%, #1e293b 40%, #172554 100%);
    color: #e2e8f0;
    font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
}

.board__header {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 0.5rem;
    margin-bottom: clamp(1.5rem, 4vw, 2.25rem);
}

.board__title {
    margin: 0;
    width: 100%;
    font-size: clamp(1.5rem, 4vw, 2rem);
    font-weight: 700;
    letter-spacing: -0.02em;
    text-align: center;
    color: #f8fafc;
    text-shadow: 0 2px 12px rgba(15, 23, 42, 0.45);
}

.board__date {
    margin: 0.35rem 0 0;
    width: 100%;
    text-align: center;
    font-size: clamp(1rem, 2.5vw, 1.2rem);
    font-weight: 600;
    color: #cbd5e1;
    letter-spacing: 0.02em;
}

.board__subtitle {
    margin: 0;
    width: 100%;
    font-size: 0.95rem;
    color: #94a3b8;
    text-align: center;
}

.board__error {
    padding: 0.75rem 1rem;
    margin: 0 0 clamp(1.25rem, 3vw, 1.75rem);
    border-radius: 0.75rem;
    background: rgba(248, 113, 113, 0.15);
    border: 1px solid rgba(252, 165, 165, 0.45);
    color: #fecaca;
    font-size: 1rem;
}

.board__loading {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    padding: 3rem 1rem;
    color: #94a3b8;
}

.board__loading-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--accent);
    animation: bounce 1.2s ease-in-out infinite;
}

.board__loading-dot:nth-child(2) {
    animation-delay: 0.15s;
}

.board__loading-dot:nth-child(3) {
    animation-delay: 0.3s;
}

.board__loading-text {
    width: 100%;
    text-align: center;
    font-size: 1.05rem;
}

@keyframes bounce {
    0%,
    80%,
    100% {
        transform: scale(0.65);
        opacity: 0.5;
    }
    40% {
        transform: scale(1);
        opacity: 1;
    }
}

.board__empty {
    text-align: center;
    padding: 3rem 1rem;
    margin: 0 0 clamp(1.5rem, 4vw, 2.5rem);
    font-size: 1.2rem;
    color: #94a3b8;
}

.board__empty-icon {
    display: block;
    font-size: 2.5rem;
    margin-bottom: 0.75rem;
    filter: saturate(0.85);
}

.board__main {
    width: 100%;
}

/* 12-column grid: each card spans 6 (two per row) from tablet up. */
.board__grid {
    display: grid;
    grid-template-columns: repeat(12, minmax(0, 1fr));
    gap: clamp(0.85rem, 2vw, 1.15rem);
}

.board__grid > * {
    grid-column: span 12;
    min-width: 0;
}

@media (min-width: 768px) {
    .board__grid > * {
        grid-column: span 6;
    }
}
</style>
