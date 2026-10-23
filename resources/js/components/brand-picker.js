/**
 * Pilih merek di Explore: bisa lebih dari satu sekaligus.
 * Tanpa pilihan (atau "Semua") = tampilkan semua mobil. Kartu yang disembunyikan
 * diberi atribut data-brand-hidden agar tetap rukun dengan filter pencarian lain.
 */
export default ({ selected, names }) => ({
    selected,
    names,
    visible: 0,

    init() {
        this.apply();
        this.$watch('selected', () => this.apply());
    },

    isActive(id) {
        return this.selected.includes(id);
    },

    toggle(id) {
        this.selected = this.isActive(id) ? this.selected.filter((s) => s !== id) : [...this.selected, id];
    },

    selectAll() {
        this.selected = [];
    },

    get title() {
        const all = !this.selected.length || this.selected.length === Object.keys(this.names).length;
        return all ? 'Semua Mobil' : this.selected.map((id) => this.names[id]).join(', ');
    },

    apply() {
        const cards = [...document.querySelectorAll('[data-car]')];
        cards.forEach((card) => {
            const brand = JSON.parse(card.dataset.car).brand;
            card.toggleAttribute('data-brand-hidden', this.selected.length > 0 && !this.selected.includes(brand));
        });
        this.visible = cards.filter((c) => !c.hasAttribute('data-brand-hidden') && c.style.display !== 'none').length;

        // Simpan pilihan di URL agar bisa dibagikan / dimuat ulang.
        const params = new URLSearchParams(location.search);
        params.delete('brand');
        params.delete('brand[]');
        this.selected.forEach((id) => params.append('brand[]', id));
        const query = params.toString();
        history.replaceState(null, '', query ? `${location.pathname}?${query}` : location.pathname);
    },
});
