@props(['active' => false])

<a {{ $attributes->merge(['class' => 'flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors ' . ($active ? 'bg-indigo-800 text-white' : 'text-indigo-200 hover:bg-indigo-900 hover:text-white')]) }}>
    {{ $slot }}
</a>
