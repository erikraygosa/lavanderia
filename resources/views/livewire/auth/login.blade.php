<div>
    <h2 class="text-xl font-bold text-gray-900 mb-1">Iniciar sesión</h2>
    <p class="text-sm text-gray-500 mb-6">Ingresa tus credenciales para continuar</p>

    <form wire:submit="login" class="space-y-4">
        <div>
            <label class="label-field">Correo electrónico</label>
            <input wire:model="email"
                   type="email"
                   autocomplete="email"
                   autofocus
                   class="input-field @error('email') border-red-400 @enderror"
                   placeholder="admin@ejemplo.com" />
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="label-field">Contraseña</label>
            <input wire:model="password"
                   type="password"
                   autocomplete="current-password"
                   class="input-field @error('password') border-red-400 @enderror"
                   placeholder="••••••••" />
            @error('password')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-2">
            <input wire:model="remember" type="checkbox" id="remember"
                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-400 w-4 h-4" />
            <label for="remember" class="text-sm text-gray-600 select-none cursor-pointer">Recordarme</label>
        </div>

        <button type="submit"
                wire:loading.attr="disabled"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-60 flex items-center justify-center gap-2">
            <svg wire:loading wire:target="login" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span wire:loading.remove wire:target="login">Entrar</span>
            <span wire:loading wire:target="login">Verificando...</span>
        </button>
    </form>
</div>
