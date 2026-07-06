export default (initialContent = '') => ({
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

    sync() {
        const editor = this.$refs.editor;
        let html = editor.innerHTML.trim();

        if (html === '<br>' || html === '<div><br></div>' || html === '<p><br></p>') {
            html = '';
        }

        this.$refs.textarea.value = html;
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
