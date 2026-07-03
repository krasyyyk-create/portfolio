@props(['active' => 'profile'])

<div class="flex flex-wrap gap-2 mb-8">
    <a
        href="{{ route('profile.edit') }}"
        @class([
            'font-mono text-sm border px-4 py-2 rounded-lg transition-all',
            'text-white border-white/20 bg-white/10' => $active === 'profile',
            'text-white/60 hover:text-white border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10' => $active !== 'profile',
        ])
    >
        &gt; profile
    </a>
    <a
        href="{{ route('account.edit') }}"
        @class([
            'font-mono text-sm border px-4 py-2 rounded-lg transition-all',
            'text-white border-white/20 bg-white/10' => $active === 'account',
            'text-white/60 hover:text-white border-white/10 hover:border-white/20 bg-white/5 hover:bg-white/10' => $active !== 'account',
        ])
    >
        &gt; account info
    </a>
</div>
