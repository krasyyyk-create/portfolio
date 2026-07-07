@props(['user'])

<div x-data="avatarCropper" class="space-y-4">
    <input type="file" name="avatar" x-ref="avatarInput" class="hidden" />

    <div class="flex items-start gap-6">
        <div class="shrink-0">
            <template x-if="previewUrl">
                <img
                    :src="previewUrl"
                    alt="Profile preview"
                    class="w-20 h-20 rounded-full object-cover border-2 border-indigo-400/40"
                />
            </template>
            <template x-if="!previewUrl">
                <div>
                    @if ($user->avatar_url)
                        <img
                            src="{{ $user->avatar_url }}"
                            alt="{{ $user->name }}"
                            class="w-20 h-20 rounded-full object-cover border-2 border-white/10"
                        />
                    @else
                        <div class="w-20 h-20 rounded-full bg-indigo-500/30 border-2 border-indigo-400/30 flex items-center justify-center font-sans text-2xl font-bold text-indigo-300">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>
            </template>
        </div>

        <div class="flex-1 space-y-2">
            <label class="font-sans font-medium text-xs text-white/60">PROFILE PHOTO</label>

            <input
                type="file"
                x-ref="filePicker"
                accept="image/jpeg,image/png,image/webp,image/gif"
                class="hidden"
                @change="onFileSelect($event)"
            />

            <button
                type="button"
                @click="openPicker()"
                class="font-mono text-sm text-white/70 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg transition-all cursor-pointer"
            >
                &gt; choose photo
            </button>

            <p class="font-mono text-[10px] text-white/30">JPEG, PNG, WebP, or GIF — max 2 MB. Drag to position your crop.</p>

            <p x-show="hasCroppedFile" x-cloak class="font-mono text-[11px] text-emerald-400">
                &gt; crop selected — save profile to apply
            </p>

            @error('avatar')
                <p class="text-red-400 font-mono text-[11px]">&gt; {{ $message }}</p>
            @enderror

            @if ($user->avatar_url)
                <label class="flex items-center gap-3 cursor-pointer mt-2">
                    <input
                        type="checkbox"
                        name="remove_avatar"
                        value="1"
                        x-ref="removeAvatar"
                        @checked(old('remove_avatar'))
                        class="w-4 h-4 rounded border-white/20 bg-slate-950/20 text-indigo-500 focus:ring-indigo-400/50"
                    />
                    <span class="font-sans text-sm text-white/70">Remove current photo</span>
                </label>
            @endif
        </div>
    </div>

    <div
        x-show="showModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
        @keydown.escape.window="closeModal()"
    >
        <div
            class="w-full max-w-lg bg-slate-900 border border-white/10 rounded-2xl shadow-2xl overflow-hidden"
            @click.outside="closeModal()"
        >
            <div class="px-5 py-4 border-b border-white/10 flex items-center justify-between">
                <div>
                    <h3 class="font-mono text-sm font-bold text-white">CROP PROFILE PHOTO</h3>
                    <p class="font-sans text-xs text-white/50 mt-1">Drag and zoom to choose the visible area.</p>
                </div>
                <button
                    type="button"
                    @click="closeModal()"
                    class="text-white/40 hover:text-white transition-colors cursor-pointer"
                    aria-label="Close crop dialog"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-4 bg-slate-950/50">
                <div class="max-h-[min(60vh,420px)] overflow-hidden rounded-lg border border-white/10 bg-slate-950">
                    <img x-ref="cropImage" alt="Crop preview" class="block max-w-full" />
                </div>
            </div>

            <div class="px-5 py-4 border-t border-white/10 flex justify-end gap-3">
                <button
                    type="button"
                    @click="closeModal()"
                    class="font-mono text-sm text-white/60 hover:text-white border border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg transition-all cursor-pointer"
                >
                    cancel
                </button>
                <button
                    type="button"
                    @click="applyCrop()"
                    class="font-mono text-sm text-white bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 px-4 py-2 rounded-lg transition-all cursor-pointer"
                >
                    apply crop
                </button>
            </div>
        </div>
    </div>
</div>
