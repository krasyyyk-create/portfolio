@props(['comment', 'post'])

@php
    $replyLabel = '<span class="font-mono uppercase tracking-wider text-white/50">Reply to '.e($comment->author->name).'</span>';
@endphp

<li
    id="comment-{{ $comment->id }}"
    class="{{ $comment->isReply() ? '' : 'glass-card border border-white/10 rounded-xl p-5' }}"
    x-data="{ showReply: {{ old('parent_id') == $comment->id ? 'true' : 'false' }}, previewUrl: null }"
>
    <div class="flex items-start gap-4 {{ $comment->isReply() ? 'pt-4' : '' }}">
        <a href="{{ route('users.show', $comment->author) }}" class="shrink-0 group/avatar">
            @if ($comment->author->avatar_url)
                <img
                    src="{{ $comment->author->avatar_url }}"
                    alt="{{ $comment->author->name }}"
                    class="w-10 h-10 rounded-full object-cover border border-white/10 group-hover/avatar:border-indigo-400/40 transition-colors"
                />
            @else
                <div class="w-10 h-10 rounded-full bg-indigo-500/30 border border-indigo-400/30 group-hover/avatar:border-indigo-400/50 flex items-center justify-center font-sans text-sm font-bold text-indigo-300 transition-colors">
                    {{ strtoupper(substr($comment->author->name, 0, 1)) }}
                </div>
            @endif
        </a>

        <div class="flex-grow min-w-0 space-y-2">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <a href="{{ route('users.show', $comment->author) }}" class="font-sans text-sm font-semibold text-white hover:text-indigo-300 transition-colors">
                        {{ $comment->author->name }}
                    </a>
                    <time class="font-mono text-[10px] text-white/40" datetime="{{ $comment->created_at->toIso8601String() }}">
                        {{ $comment->created_at->diffForHumans() }}
                    </time>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    @can('delete', $comment)
                        <form
                            action="{{ route('posts.comments.destroy', [$post, $comment]) }}"
                            method="POST"
                            onsubmit="return confirm('Delete this comment?')"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="font-mono text-[10px] text-white/30 hover:text-red-400 transition-colors"
                            >
                                delete
                            </button>
                        </form>
                    @endcan

                    @auth
                        @if (auth()->id() !== $comment->user_id)
                            <x-report-button
                                :action="route('posts.comments.report', [$post, $comment])"
                                label="Report this comment"
                                heading="Report comment"
                                description="Tell us why this comment should be reviewed. Reports are visible to admins only."
                            />
                        @endif
                    @endauth
                </div>
            </div>

            @if ($comment->body)
                <div class="wysiwyg-content font-sans text-sm text-white/80 leading-relaxed">
                    {!! $comment->body !!}
                </div>
            @endif

            @if ($comment->image_url)
                <figure class="{{ $comment->body ? 'mt-2' : '' }}">
                    @if ($comment->imageNeedsCropDisplay())
                        <div class="relative w-[min(480px,100%)] aspect-[3/2] overflow-hidden rounded-lg border border-white/10">
                            <img
                                src="{{ $comment->image_url }}"
                                alt="Comment attachment"
                                class="absolute inset-0 w-full h-full object-cover"
                                loading="lazy"
                            />
                        </div>
                    @else
                        <img
                            src="{{ $comment->image_url }}"
                            alt="Comment attachment"
                            width="{{ $comment->image_width }}"
                            height="{{ $comment->image_height }}"
                            class="rounded-lg border border-white/10 max-w-full h-auto"
                            loading="lazy"
                        />
                    @endif
                </figure>
            @endif

            @auth
                <div class="pt-1">
                    <button
                        type="button"
                        @click="showReply = !showReply"
                        class="font-mono text-[11px] text-white/40 hover:text-indigo-300 transition-colors"
                        x-text="showReply ? 'Cancel' : 'Reply'"
                    ></button>

                    <form
                        x-show="showReply"
                        x-cloak
                        x-transition
                        action="{{ route('posts.comments.store', $post) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="mt-4 space-y-4 rounded-lg border border-white/10 bg-white/[0.02] p-4"
                    >
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}" />

                        <x-wysiwyg-editor
                            name="body"
                            :id="'comment-reply-'.$comment->id"
                            :value="old('parent_id') == $comment->id ? old('body', '') : ''"
                            :label="$replyLabel"
                            hint="Use the toolbar to format your reply."
                            min-height="80px"
                            max-height="200px"
                            placeholder="Write a reply..."
                            :maxlength="2000"
                        />

                        <div class="space-y-2">
                            <label for="comment-reply-image-{{ $comment->id }}" class="font-mono text-xs text-white/50 uppercase tracking-wider">
                                Attach image or GIF
                            </label>
                            <input
                                id="comment-reply-image-{{ $comment->id }}"
                                type="file"
                                name="image"
                                accept="image/jpeg,image/png,image/webp,image/gif"
                                class="block w-full text-sm text-white/70 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border file:border-white/10 file:text-sm file:font-mono file:bg-white/5 file:text-white/80 hover:file:bg-white/10 file:cursor-pointer cursor-pointer"
                                @change="
                                    previewUrl = $event.target.files[0]
                                        ? URL.createObjectURL($event.target.files[0])
                                        : null
                                "
                            />
                            <p class="font-mono text-[10px] text-white/30">JPEG, PNG, WebP, or GIF — max 4 MB.</p>

                            <div x-show="previewUrl" x-cloak class="pt-1">
                                <p class="font-mono text-[10px] text-white/40 mb-2">Preview</p>
                                <img
                                    :src="previewUrl"
                                    alt="Attachment preview"
                                    class="rounded-lg border border-white/10 object-cover max-w-[480px] max-h-[320px] w-auto h-auto"
                                />
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center bg-indigo-500/85 hover:bg-indigo-500 border border-white/10 text-white font-sans text-sm font-semibold px-4 py-1.5 rounded-lg active:scale-[0.98] transition-all"
                            >
                                Post reply
                            </button>
                        </div>
                    </form>
                </div>
            @endauth
        </div>
    </div>

    @if ($comment->replies->isNotEmpty())
        <ul class="mt-2 ml-6 space-y-0 border-l border-white/10 pl-4">
            @foreach ($comment->replies as $reply)
                <x-posts.comment :comment="$reply" :post="$post" />
            @endforeach
        </ul>
    @endif
</li>
