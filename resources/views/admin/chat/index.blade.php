<x-layouts.admin title="Admin — Live Chat" header="live_chat">
    <div
        x-data="adminChatRoom({
            messagesUrl: '{{ route('admin.chat.messages') }}',
            sendUrl: '{{ route('admin.chat.messages.store') }}',
            currentUserId: {{ auth()->id() }},
        })"
        x-init="init()"
        class="space-y-6"
    >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="font-sans text-2xl md:text-3xl font-bold text-white">Admin Chat</h1>
                <p class="font-sans text-sm text-white/50 mt-1">Private room for administrators — text and images</p>
            </div>

            <span class="font-mono text-[10px] text-emerald-400 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                LIVE
            </span>
        </div>

        <div
            class="glass-card rounded-xl overflow-hidden flex flex-col"
            style="height: calc(100vh - 14rem); min-height: 28rem;"
            @dragover.prevent="dragOver = true"
            @dragleave.prevent="dragOver = false"
            @drop.prevent="handleDrop($event)"
            :class="dragOver && 'ring-2 ring-indigo-400/50'"
        >
            <div
                x-ref="messageList"
                class="flex-1 overflow-y-auto p-4 space-y-4"
            >
                <template x-if="loading && messages.length === 0">
                    <p class="text-center font-mono text-sm text-white/40 py-12">Loading messages...</p>
                </template>

                <template x-if="!loading && messages.length === 0">
                    <p class="text-center font-mono text-sm text-white/40 py-12">No messages yet. Say something or drop an image.</p>
                </template>

                <template x-for="msg in messages" :key="msg.id">
                    <div :class="msg.is_mine ? 'flex justify-end' : 'flex justify-start'">
                        <div class="max-w-[75%] space-y-1">
                            <div class="flex items-center gap-2" :class="msg.is_mine ? 'justify-end' : 'justify-start'">
                                <template x-if="!msg.is_mine && msg.sender_avatar">
                                    <img :src="msg.sender_avatar" :alt="msg.sender_name" class="w-5 h-5 rounded-full object-cover" />
                                </template>
                                <p class="font-mono text-[10px] text-white/40" x-text="msg.is_mine ? 'you' : msg.sender_name"></p>
                            </div>

                            <div
                                :class="msg.is_mine ? 'bg-indigo-500/20 border-indigo-400/30' : 'bg-white/5 border-white/10'"
                                class="rounded-xl px-4 py-2.5 border space-y-2"
                            >
                                <template x-if="msg.body">
                                    <p class="font-sans text-sm text-white whitespace-pre-wrap break-words" x-text="msg.body"></p>
                                </template>
                                <template x-if="msg.image_url">
                                    <a :href="msg.image_url" target="_blank" rel="noopener noreferrer">
                                        <img
                                            :src="msg.image_url"
                                            alt="Shared image"
                                            class="rounded-lg max-w-full max-h-64 object-contain cursor-pointer hover:opacity-90 transition-opacity"
                                        />
                                    </a>
                                </template>
                                <p class="font-mono text-[10px] text-white/30" x-text="formatTime(msg.created_at)"></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <template x-if="imagePreview">
                <div class="px-4 py-2 border-t border-white/10 bg-white/5 flex items-center gap-3">
                    <img :src="imagePreview" alt="Preview" class="h-16 w-16 rounded-lg object-cover border border-white/10" />
                    <div class="flex-1 min-w-0">
                        <p class="font-mono text-xs text-white/60 truncate" x-text="imageFile?.name ?? 'Image'"></p>
                        <p class="font-mono text-[10px] text-white/40">Ready to send</p>
                    </div>
                    <button
                        type="button"
                        @click="clearImage()"
                        class="font-mono text-xs text-red-400 hover:text-red-300 cursor-pointer"
                    >
                        remove
                    </button>
                </div>
            </template>

            <form @submit.prevent="sendMessage()" class="p-4 border-t border-white/10 shrink-0">
                <div class="flex gap-2 items-end">
                    <label class="shrink-0 cursor-pointer text-white/50 hover:text-white transition-colors p-2.5 rounded-lg border border-white/10 hover:border-white/20 hover:bg-white/5">
                        <input type="file" accept="image/jpeg,image/png,image/gif,image/webp" class="hidden" @change="handleFileSelect($event)" />
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </label>

                    <input
                        type="text"
                        x-model="newMessage"
                        @paste="handlePaste($event)"
                        :disabled="sending"
                        placeholder="Type anything..."
                        class="flex-1 bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 font-sans text-sm text-white placeholder:text-white/30 focus:outline-none focus:border-indigo-400/50 focus:ring-1 focus:ring-indigo-400/30"
                    />

                    <button
                        type="submit"
                        :disabled="sending || (!newMessage.trim() && !imageFile)"
                        class="font-mono text-xs bg-indigo-500 hover:bg-indigo-400 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2.5 rounded-lg transition-colors cursor-pointer shrink-0"
                    >
                        send
                    </button>
                </div>
                <p class="font-mono text-[10px] text-white/30 mt-2">Drop images anywhere in the chat, or paste from clipboard</p>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('adminChatRoom', (config) => ({
                messagesUrl: config.messagesUrl,
                sendUrl: config.sendUrl,
                currentUserId: config.currentUserId,

                messages: [],
                newMessage: '',
                imageFile: null,
                imagePreview: null,
                loading: true,
                sending: false,
                dragOver: false,
                pollTimer: null,
                lastMessageId: 0,

                init() {
                    this.fetchMessages();
                    this.pollTimer = setInterval(() => this.poll(), 3000);
                },

                destroy() {
                    if (this.pollTimer) clearInterval(this.pollTimer);
                    if (this.imagePreview) URL.revokeObjectURL(this.imagePreview);
                },

                async poll() {
                    if (this.lastMessageId === 0) {
                        await this.fetchMessages(true);
                        return;
                    }

                    const res = await fetch(`${this.messagesUrl}?after=${this.lastMessageId}`, {
                        headers: { 'Accept': 'application/json' },
                    });

                    if (!res.ok) return;

                    const data = await res.json();
                    if (data.messages.length > 0) {
                        this.messages.push(...data.messages);
                        this.lastMessageId = data.messages[data.messages.length - 1].id;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                async fetchMessages(silent = false) {
                    if (!silent) this.loading = true;

                    try {
                        const res = await fetch(this.messagesUrl, {
                            headers: { 'Accept': 'application/json' },
                        });

                        if (res.ok) {
                            const data = await res.json();
                            const hadMessages = this.messages.length;
                            this.messages = data.messages;
                            this.lastMessageId = data.messages.length
                                ? data.messages[data.messages.length - 1].id
                                : 0;

                            if (!silent || data.messages.length !== hadMessages) {
                                this.$nextTick(() => this.scrollToBottom());
                            }
                        }
                    } finally {
                        this.loading = false;
                    }
                },

                async sendMessage() {
                    const body = this.newMessage.trim();
                    if (!body && !this.imageFile) return;

                    this.sending = true;

                    try {
                        const formData = new FormData();
                        if (body) formData.append('body', body);
                        if (this.imageFile) formData.append('image', this.imageFile);

                        const res = await fetch(this.sendUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                            },
                            body: formData,
                        });

                        if (res.ok) {
                            const data = await res.json();
                            this.messages.push(data.message);
                            this.lastMessageId = data.message.id;
                            this.newMessage = '';
                            this.clearImage();
                            this.$nextTick(() => this.scrollToBottom());
                        }
                    } finally {
                        this.sending = false;
                    }
                },

                handleFileSelect(event) {
                    const file = event.target.files?.[0];
                    if (file) this.setImageFile(file);
                    event.target.value = '';
                },

                handleDrop(event) {
                    this.dragOver = false;
                    const file = [...event.dataTransfer.files].find(f => f.type.startsWith('image/'));
                    if (file) this.setImageFile(file);
                },

                handlePaste(event) {
                    const file = [...event.clipboardData.items]
                        .find(item => item.type.startsWith('image/'))
                        ?.getAsFile();
                    if (file) {
                        event.preventDefault();
                        this.setImageFile(file);
                    }
                },

                setImageFile(file) {
                    if (this.imagePreview) URL.revokeObjectURL(this.imagePreview);
                    this.imageFile = file;
                    this.imagePreview = URL.createObjectURL(file);
                },

                clearImage() {
                    if (this.imagePreview) URL.revokeObjectURL(this.imagePreview);
                    this.imageFile = null;
                    this.imagePreview = null;
                },

                scrollToBottom() {
                    const el = this.$refs.messageList;
                    if (el) el.scrollTop = el.scrollHeight;
                },

                formatTime(iso) {
                    if (!iso) return '';
                    const d = new Date(iso);
                    return d.toLocaleString(undefined, { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
                },
            }));
        });
    </script>
</x-layouts.admin>
