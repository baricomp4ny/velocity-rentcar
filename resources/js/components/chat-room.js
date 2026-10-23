/**
 * Ruang chat Customer Service, dipakai di sisi pelanggan dan admin.
 * Pesan baru diambil dengan polling berdasarkan waktu pesan terakhir.
 */
export default ({ messages, pollUrl, sendUrl, draft = '' }) => ({
    messages,
    draft,
    sending: false,

    init() {
        this.scroll();
        setInterval(() => this.poll(), 4000);
    },

    add(list) {
        const known = new Set(this.messages.map((m) => m.id));
        const fresh = list.filter((m) => !known.has(m.id));
        if (fresh.length) {
            this.messages.push(...fresh);
            this.scroll();
        }
    },

    async poll() {
        const since = this.messages.at(-1)?.at;
        const { data } = await axios.get(pollUrl, { params: { since } });
        this.add(data);
    },

    async send() {
        const body = this.draft.trim();
        if (!body || this.sending) return;

        this.sending = true;
        try {
            const { data } = await axios.post(sendUrl, { body });
            this.add([data]);
            this.draft = '';
        } finally {
            this.sending = false;
        }
    },

    scroll() {
        this.$nextTick(() => (this.$refs.list.scrollTop = this.$refs.list.scrollHeight));
    },
});
