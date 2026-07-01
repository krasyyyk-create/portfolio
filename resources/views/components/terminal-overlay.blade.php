<div
    x-show="isOpen"
    x-cloak
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div @click="close()" class="absolute inset-0 bg-slate-950/65 backdrop-blur-md"></div>

    <div
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative w-full max-w-3xl glass-card-heavy border border-white/15 rounded-2xl overflow-hidden shadow-2xl flex flex-col h-[70vh]"
    >
        <div class="bg-white/5 p-4 flex justify-between items-center border-b border-white/10 select-none">
            <div class="flex gap-1.5">
                <button @click="close()" type="button" class="w-3 h-3 rounded-full bg-red-500/60 cursor-pointer hover:bg-red-500 transition-colors" title="Close Terminal"></button>
                <span class="w-3 h-3 rounded-full bg-yellow-500/30"></span>
                <span class="w-3 h-3 rounded-full bg-green-500/30"></span>
            </div>
            <div class="font-mono text-xs text-white/40 flex items-center gap-1.5">
                <svg class="w-4 h-4 text-indigo-400 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>root@dev_architect:~/gateway_node</span>
            </div>
        </div>

        <div
            x-ref="logContainer"
            class="flex-grow p-6 overflow-y-auto font-mono text-xs text-indigo-200 space-y-2 bg-slate-950/40 backdrop-blur-md selection:bg-indigo-500/30 selection:text-white select-text"
        >
            <template x-for="(log, index) in logs" :key="index">
                <p
                    class="leading-relaxed whitespace-pre-wrap select-text"
                    :class="{
                        'text-white font-bold': log.startsWith('guest_node $'),
                        'text-indigo-300 font-medium': log.startsWith('User:') || log.startsWith('Role:') || log.startsWith('SLA'),
                        'text-red-400': log.startsWith('Command not found'),
                    }"
                    x-text="log"
                ></p>
            </template>
        </div>

        <form @submit="submitCommand($event)" class="p-4 bg-white/5 border-t border-white/10 flex items-center gap-3">
            <span class="font-mono text-xs text-indigo-400 font-bold select-none">guest_node $</span>
            <input
                type="text"
                x-model="inputVal"
                x-ref="terminalInput"
                x-init="$watch('isOpen', value => { if (value) $nextTick(() => $refs.terminalInput?.focus()) })"
                placeholder="Type systems command (e.g., 'help', 'skills', 'projects')..."
                class="flex-grow bg-transparent border-none text-white font-mono text-xs focus:outline-none placeholder:text-white/20"
            />
            <span class="font-mono text-[10px] text-white/40 select-none">SOCKET_CONNECTED</span>
        </form>
    </div>
</div>
