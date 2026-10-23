/**
 * Demo statis Velocity RentCar (GitHub Pages).
 *
 * Halaman HTML diekspor dari aplikasi Laravel (php artisan demo:export). Karena tidak ada
 * server, skrip ini mensimulasikan bagian yang biasanya butuh backend: login, pencarian &
 * filter, simpan favorit, kode promo, pemesanan, dan chat CS. Data tidak disimpan ke server.
 */
const demo = window.VELOCITY_DEMO;
const STORAGE_FAVORITES = 'velocity-demo-favorites';
const STORAGE_BOOKING = 'velocity-demo-booking';

/* ---------- UI kecil: pita info & toast ---------- */

function ribbon() {
    const bar = document.createElement('div');
    bar.style.cssText = 'position:relative;z-index:70;background:#121212;color:#fff;font:500 12px Poppins,sans-serif;text-align:center;padding:7px 12px';
    bar.innerHTML = `Demo statis &middot; data tidak disimpan &middot; <a href="${demo.repo}" target="_blank" rel="noopener" style="color:#B9DCFF;text-decoration:underline">Lihat kode lengkap (Laravel) di GitHub</a>`;
    document.body.prepend(bar);
}

let toastTimer;
function toast(message) {
    let el = document.getElementById('demo-toast');
    if (!el) {
        el = document.createElement('div');
        el.id = 'demo-toast';
        el.style.cssText = 'position:fixed;left:50%;bottom:110px;transform:translateX(-50%);z-index:80;max-width:min(92vw,420px);background:#121212;color:#fff;font:500 13px Poppins,sans-serif;padding:12px 16px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.25);text-align:center;transition:opacity .2s';
        document.body.appendChild(el);
    }
    el.textContent = message;
    el.style.opacity = '1';
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => (el.style.opacity = '0'), 2800);
}

const disabled = () => toast('Fitur ini hanya tersedia di versi lengkap (Laravel). Kode lengkap ada di GitHub.');

/* ---------- Pengganti server untuk axios ---------- */

const favorites = () => JSON.parse(localStorage.getItem(STORAGE_FAVORITES) || '{}');
const chatQueue = [];

function chatMessage(sender, body) {
    const now = new Date();
    return {
        id: `demo-${now.getTime()}-${Math.random().toString(36).slice(2, 7)}`,
        sender,
        body,
        time: now.toTimeString().slice(0, 5),
        at: now.toISOString(),
    };
}

function mockApi(config) {
    const url = new URL(config.url, location.href);
    const path = url.pathname;
    const body = typeof config.data === 'string' ? JSON.parse(config.data || '{}') : config.data || {};

    if (path.endsWith('/promo/check')) {
        const code = String(config.params?.code || '').trim().toUpperCase();
        const promo = demo.promos.find((p) => p.code === code);
        return promo
            ? { valid: true, code: promo.code, percent: promo.percent, max: promo.max, message: `Promo ${promo.code} dipakai: ${promo.summary}` }
            : { valid: false, message: 'Kode promo tidak valid atau sudah kedaluwarsa.' };
    }

    const saved = path.match(/\/saved\/([0-9a-f-]{36})$/);
    if (saved) {
        const all = favorites();
        all[saved[1]] = !all[saved[1]];
        localStorage.setItem(STORAGE_FAVORITES, JSON.stringify(all));
        toast(all[saved[1]] ? 'Mobil disimpan ke Saved (demo).' : 'Mobil dihapus dari Saved (demo).');
        return { saved: all[saved[1]] };
    }

    if (path.endsWith('/messages')) {
        const due = chatQueue.filter((m) => m.dueAt <= Date.now());
        due.forEach((m) => chatQueue.splice(chatQueue.indexOf(m), 1));
        return due.map((m) => m.message);
    }

    if (config.method === 'post' && /\/chat$|\/admin\/chats\/[0-9a-f-]{36}$/.test(path)) {
        const fromAdmin = path.includes('/admin/');
        if (!fromAdmin) {
            chatQueue.push({
                dueAt: Date.now() + 1500,
                message: chatMessage('admin', 'Terima kasih sudah menghubungi kami! Ini demo statis, jadi balasan ini otomatis. Di versi lengkap, admin membalas langsung dari panel admin.'),
            });
        }
        return chatMessage(fromAdmin ? 'admin' : 'user', body.body);
    }

    return null;
}

function installAxiosMock() {
    if (!window.axios) return;
    window.axios.defaults.adapter = (config) => {
        const data = mockApi(config);
        return data === null
            ? Promise.reject(new Error('Tidak ada server di demo statis'))
            : Promise.resolve({ data, status: 200, statusText: 'OK', headers: {}, config });
    };
}

/* ---------- Tombol hati (Saved) memakai status dari localStorage ---------- */

function restoreFavorites() {
    if (!window.Alpine) return;
    const all = favorites();
    document.querySelectorAll('button[aria-label="Simpan mobil"]').forEach((button) => {
        const id = [...button.attributes].map((a) => a.value).join(' ').match(/saved\/([0-9a-f-]{36})/)?.[1];
        if (!id) return;
        // Status awal mengikuti halaman; perubahan dari klik sebelumnya disimpan di localStorage.
        if (id in all) window.Alpine.$data(button).saved = all[id];
        else all[id] = window.Alpine.$data(button).saved;
    });
    localStorage.setItem(STORAGE_FAVORITES, JSON.stringify(all));
}

/* ---------- Pencarian & filter di Home / Explore tanpa server ---------- */

function applyFilters(params) {
    const cards = [...document.querySelectorAll('[data-car]')];
    if (!cards.length) return;

    const q = (params.get('q') || '').trim().toLowerCase();
    const list = (key) => params.getAll(`${key}[]`).filter(Boolean);
    const [categories, transmissions, fuels] = [list('category'), list('transmission'), list('fuel')];
    const seats = Number(params.get('seats') || 0);
    const searching = Boolean(q || categories.length || transmissions.length || fuels.length || seats);

    let shown = 0;
    cards.forEach((card) => {
        const car = JSON.parse(card.dataset.car);
        const match = (!q || car.text.includes(q))
            && (!categories.length || categories.includes(car.category))
            && (!transmissions.length || transmissions.includes(car.transmission))
            && (!fuels.length || fuels.includes(car.fuel))
            && (!seats || car.seats >= seats);
        card.style.display = match ? '' : 'none';
        shown += match ? 1 : 0;
    });

    document.querySelectorAll('[data-poster]').forEach((el) => el.style.setProperty('display', searching ? 'none' : ''));

    const title = [...document.querySelectorAll('h2')].find((h) => /Mobil Populer|Hasil Pencarian/.test(h.textContent));
    if (title) if (title.dataset.original === undefined) title.dataset.original = title.firstChild.textContent;
    title.firstChild.textContent = searching ? `Hasil Pencarian (${shown}) ` : title.dataset.original;

    let empty = document.getElementById('demo-empty');
    if (!empty) {
        empty = document.createElement('p');
        empty.id = 'demo-empty';
        empty.style.cssText = 'padding:40px 16px;text-align:center;color:#888689;font:500 14px Poppins,sans-serif';
        empty.textContent = 'Mobil tidak ditemukan. Coba kata kunci atau filter lain.';
        cards[0].parentElement.appendChild(empty);
    }
    empty.style.display = shown ? 'none' : '';

    // Samakan isi form dengan filter yang aktif.
    const form = document.getElementById('car-filter');
    if (form) {
        form.querySelector('[name="q"]').value = params.get('q') || '';
        document.querySelectorAll('input[form="car-filter"]').forEach((input) => {
            input.checked = input.type === 'radio'
                ? input.value === (params.get('seats') || '')
                : params.getAll(input.name).includes(input.value);
        });
    }
}

/* ---------- Simulasi aksi form ---------- */

// Tanggal lokal (WIB), bukan UTC.
const todayIso = () => { const d = new Date(); return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`; };
const formatDate = (iso) => new Date(`${iso}T00:00:00`).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

function saveBooking(form, action) {
    const d = window.Alpine.$data(form);
    const carId = action.match(/cars\/([0-9a-f-]{36})/)[1];
    sessionStorage.setItem(STORAGE_BOOKING, JSON.stringify({
        code: `VRC-${todayIso().slice(2).replace(/-/g, '')}-${Math.random().toString(36).slice(2, 7).toUpperCase()}`,
        model: demo.cars[carId],
        name: d.name,
        pickup: formatDate(d.date),
        returns: formatDate(d.returnDate),
        location: d.locationNames[d.location],
        duration: `${d.duration} Hari`,
        payment: d.paymentNames[d.payment],
        discount: d.discount,
        total: d.total,
    }));
}

function handleSubmit(event) {
    const form = event.target;
    const action = form.dataset.demoAction;
    if (!action) return;
    event.preventDefault();
    event.stopImmediatePropagation();

    const go = (file) => (location.href = file);

    if (action === '/login') return go(/admin/i.test(form.querySelector('[name="login"]')?.value) ? 'admin.html' : 'home.html');
    if (action === '/register') return go('home.html');
    if (action === '/demo-login/user') return go('home.html');
    if (action === '/demo-login/admin') return go('admin.html');
    if (action === '/logout') return go('index.html');
    if (action === '/location') return toast('Lokasi diubah (demo).');

    if (action === '/home' || action === '/explore') {
        const params = new URLSearchParams(new FormData(form));
        history.replaceState(null, '', `${location.pathname}?${params}`);
        applyFilters(params);
        const header = form.closest('header');
        if (header && window.Alpine) window.Alpine.$data(header).filterOpen = false;
        return;
    }

    if (/^\/cars\/[0-9a-f-]{36}\/booking$/.test(action)) {
        saveBooking(form, action);
        return go('info-bayar.html');
    }

    disabled();
}

/* ---------- Info Bayar memakai data pesanan dari form booking ---------- */

function fillReceipt() {
    const booking = JSON.parse(sessionStorage.getItem(STORAGE_BOOKING) || 'null');
    if (!booking || !location.pathname.endsWith('info-bayar.html')) return;

    const rupiah = (n) => `Rp ${Math.round(n).toLocaleString('id-ID')},-`;
    const values = {
        Kendaraan: booking.model,
        Peminjam: booking.name,
        'Tanggal Pesan': formatDate(todayIso()),
        Pengambilan: booking.pickup,
        Pengembalian: booking.returns,
        Lokasi: booking.location,
        'Durasi Peminjaman': booking.duration,
        Pembayaran: booking.payment,
        'Total Harga': rupiah(booking.total),
    };

    document.querySelectorAll('dt').forEach((dt) => {
        const value = values[dt.textContent.trim()];
        if (value && dt.nextElementSibling) dt.nextElementSibling.textContent = value;
    });

    if (booking.discount > 0) {
        const total = [...document.querySelectorAll('dt')].find((dt) => dt.textContent.trim() === 'Total Harga');
        total?.parentElement.insertAdjacentHTML('beforebegin', `<div class="flex justify-between gap-4"><dt class="font-medium">Diskon</dt><dd class="whitespace-nowrap text-emerald-600">- ${rupiah(booking.discount)}</dd></div>`);
    }

    document.querySelectorAll('p').forEach((p) => {
        if (p.textContent.includes(demo.sampleCode)) p.textContent = p.textContent.replace(demo.sampleCode, booking.code);
    });
}

/* ---------- Mulai ---------- */

ribbon();
installAxiosMock();
fillReceipt();
document.addEventListener('submit', handleSubmit, true);
document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href="#demo-off"]');
    if (link) {
        event.preventDefault();
        disabled();
    }
});

const params = new URLSearchParams(location.search);
if ([...params.keys()].length) applyFilters(params);

// Alpine sudah aktif setelah app.js; tunggu sebentar agar komponennya siap.
setTimeout(restoreFavorites, 50);
