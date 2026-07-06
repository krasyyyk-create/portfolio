export default (initialContent = '') => ({
    mode: 'visual',
    htmlSource: initialContent || '',

    init() {
        if (initialContent) {
            this.$refs.editor.innerHTML = initialContent;
        }

        this.sync();

        const form = this.$el.closest('form');
        if (form) {
            form.addEventListener('submit', () => this.sync());
        }
    },

    normalizeHtml(html) {
        const trimmed = html.trim();

        if (trimmed === '<br>' || trimmed === '<div><br></div>' || trimmed === '<p><br></p>') {
            return '';
        }

        return trimmed;
    },

    sync() {
        const html = this.mode === 'html'
            ? this.normalizeHtml(this.htmlSource)
            : this.normalizeHtml(this.$refs.editor.innerHTML);

        if (this.mode === 'visual') {
            this.htmlSource = html;
        }

        this.$refs.textarea.value = html;
    },

    toggleMode() {
        if (this.mode === 'visual') {
            this.htmlSource = this.normalizeHtml(this.$refs.editor.innerHTML);
            this.mode = 'html';
            this.sync();
            this.$nextTick(() => this.$refs.htmlEditor?.focus());
        } else {
            this.$refs.editor.innerHTML = this.htmlSource;
            this.mode = 'visual';
            this.sync();
            this.$nextTick(() => this.$refs.editor?.focus());
        }
    },

    exec(command, value = null) {
        this.$refs.editor.focus();
        document.execCommand(command, false, value);
        this.sync();
    },

    formatBlock(tag) {
        this.exec('formatBlock', tag);
    },

    insertLink() {
        const url = window.prompt('Enter URL:');
        if (url) {
            this.exec('createLink', url.trim());
        }
    },
});
