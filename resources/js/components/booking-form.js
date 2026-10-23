/**
 * Form pemesanan: Rental Summary (estimasi biaya realtime + promo)
 * dan 4 langkah: Informasi Penyewa, Jadwal Sewa, Pembayaran, Konfirmasi.
 */
const PHONE_PATTERN = /^(\+62|62|0)8[0-9]{7,12}$/;
const MAX_DAYS = 30;

const toIso = (d) =>
    `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

const addDays = (iso, n) => {
    const d = new Date(`${iso}T00:00:00`);
    d.setDate(d.getDate() + n);
    return toIso(d);
};

export default (state) => ({
    ...state,
    stepError: '',
    promo: null,
    promoMessage: '',
    month: null,

    init() {
        const base = new Date(`${this.date || this.today}T00:00:00`);
        this.month = new Date(base.getFullYear(), base.getMonth(), 1);
        if (this.promoInput) this.applyPromo();
    },

    // Rental Summary
    get subtotal() {
        return this.price * this.duration;
    },
    get discount() {
        if (!this.promo) return 0;
        const amount = Math.floor((this.subtotal * this.promo.percent) / 100);
        return this.promo.max ? Math.min(amount, this.promo.max) : amount;
    },
    get total() {
        return this.subtotal - this.discount;
    },
    setDuration(value) {
        this.duration = Math.min(MAX_DAYS, Math.max(1, value));
    },
    async applyPromo() {
        if (!this.promoInput.trim()) {
            this.promo = null;
            this.promoMessage = '';
            return;
        }
        const { data } = await axios.get(this.promoUrl, { params: { code: this.promoInput } });
        this.promo = data.valid ? data : null;
        this.promoMessage = data.message;
    },

    // Kalender jadwal sewa
    get returnDate() {
        return this.date ? addDays(this.date, this.duration) : '';
    },
    isBooked(iso) {
        return this.booked.some((range) => iso >= range.from && iso < range.to);
    },
    get conflict() {
        if (!this.date) return false;
        return Array.from({ length: this.duration }, (_, i) => addDays(this.date, i)).some((iso) => this.isBooked(iso));
    },
    get monthLabel() {
        return this.month.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
    },
    get canGoBack() {
        return toIso(this.month) > `${this.today.slice(0, 8)}01`;
    },
    shiftMonth(n) {
        this.month = new Date(this.month.getFullYear(), this.month.getMonth() + n, 1);
    },
    get calendar() {
        const year = this.month.getFullYear();
        const month = this.month.getMonth();
        const blanks = Array.from({ length: new Date(year, month, 1).getDay() }, (_, i) => ({ key: `blank-${i}` }));
        const days = Array.from({ length: new Date(year, month + 1, 0).getDate() }, (_, i) => {
            const iso = toIso(new Date(year, month, i + 1));
            return {
                key: iso,
                day: i + 1,
                date: iso,
                disabled: iso < this.today || this.isBooked(iso),
                inRange: Boolean(this.date) && iso > this.date && iso < this.returnDate,
            };
        });
        return [...blanks, ...days];
    },
    formatDate(iso) {
        return iso
            ? new Date(`${iso}T00:00:00`).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
            : '-';
    },

    // Navigasi langkah
    stepIsValid() {
        if (this.step === 1) {
            if (!this.name.trim() || !this.address.trim() || !this.phone.trim()) return 'Lengkapi nama, alamat, dan nomor telepon.';
            if (!PHONE_PATTERN.test(this.phone.trim())) return 'Format nomor telepon tidak valid (contoh: 081234567890).';
        }
        if (this.step === 2) {
            if (!this.date) return 'Pilih tanggal pengambilan mobil.';
            if (this.conflict) return 'Tanggal yang dipilih bentrok dengan pesanan lain.';
        }
        if (this.step === 3 && !this.payment) return 'Pilih metode pembayaran.';
        return '';
    },
    next() {
        this.stepError = this.stepIsValid();
        if (this.stepError) return;
        this.step++;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
});
