<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Lavandería' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-indigo-950 via-indigo-900 to-indigo-800 min-h-screen flex items-center justify-center px-4">
    @php
        $logoPath   = \App\Models\Configuracion::obtener('logo_path', '');
        $negocioNom = \App\Models\Configuracion::obtener('negocio_nombre', 'Lavandería');
    @endphp

    <div class="w-full max-w-sm">
        {{-- Logo / nombre --}}
        <div class="text-center mb-8">
            @if($logoPath)
                <img src="{{ Storage::url($logoPath) }}" alt="Logo"
                     class="h-16 w-16 object-contain mx-auto rounded-2xl bg-white p-1.5 shadow-lg mb-3" />
            @else
                <div class="w-14 h-14 bg-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V5a2 2 0 012-2h4a2 2 0 012 2v2"/>
                    </svg>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-white">{{ $negocioNom }}</h1>
            <p class="text-indigo-300 text-sm mt-1">Sistema de gestión</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            {{ $slot }}
        </div>

        <p class="text-center text-indigo-400 text-xs mt-6">{{ now()->format('Y') }} · {{ $negocioNom }}</p>
    </div>

    @livewireScripts
</body>
</html>
