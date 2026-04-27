<?php

namespace App\Livewire\Configuracion;

use App\Models\Configuracion;
use App\Services\WhatsappService;
use Livewire\Component;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    // EvoAPI
    public string $evoUrl      = '';
    public string $evoInstancia= '';
    public string $evoApikey   = '';

    // Negocio
    public string $negocioNombre   = '';
    public string $negocioTelefono = '';
    public string $negocioDireccion= '';

    // Logo
    public $logoArchivo  = null;   // archivo temporal Livewire
    public string $logoPath = '';  // ruta guardada en config

    public string $mensajeExito  = '';
    public string $mensajeError  = '';
    public string $mensajePrueba = '';

    public function mount(): void
    {
        $this->evoUrl        = Configuracion::obtener('evo_url', '');
        $this->evoInstancia  = Configuracion::obtener('evo_instancia', '');
        $this->evoApikey     = Configuracion::obtener('evo_apikey', '');
        $this->negocioNombre    = Configuracion::obtener('negocio_nombre', 'Lavandería');
        $this->negocioTelefono  = Configuracion::obtener('negocio_telefono', '');
        $this->negocioDireccion = Configuracion::obtener('negocio_direccion', '');
        $this->logoPath      = Configuracion::obtener('logo_path', '');
    }

    public function guardar(): void
    {
        $this->validate([
            'evoUrl'        => 'nullable|url',
            'negocioNombre' => 'required|min:2|max:100',
            'logoArchivo'   => 'nullable|image|max:2048',
        ], [
            'evoUrl.url'             => 'La URL de EvoAPI no tiene formato válido.',
            'negocioNombre.required' => 'El nombre del negocio es obligatorio.',
            'logoArchivo.image'      => 'El archivo debe ser una imagen.',
            'logoArchivo.max'        => 'El logo no debe pesar más de 2 MB.',
        ]);

        // Guardar logo si se subió uno
        if ($this->logoArchivo) {
            // Eliminar logo anterior
            if ($this->logoPath && file_exists(storage_path('app/public/' . $this->logoPath))) {
                unlink(storage_path('app/public/' . $this->logoPath));
            }
            $ruta = $this->logoArchivo->store('logos', 'public');
            $this->logoPath = $ruta;
            Configuracion::establecer('logo_path', $ruta);
            $this->logoArchivo = null;
        }

        Configuracion::establecer('evo_url',       $this->evoUrl);
        Configuracion::establecer('evo_instancia', $this->evoInstancia);
        Configuracion::establecer('evo_apikey',    $this->evoApikey);
        Configuracion::establecer('negocio_nombre',    $this->negocioNombre);
        Configuracion::establecer('negocio_telefono',  $this->negocioTelefono);
        Configuracion::establecer('negocio_direccion', $this->negocioDireccion);

        $this->mensajeExito = 'Configuración guardada correctamente.';
        $this->mensajeError = '';
    }

    public function eliminarLogo(): void
    {
        if ($this->logoPath && file_exists(storage_path('app/public/' . $this->logoPath))) {
            unlink(storage_path('app/public/' . $this->logoPath));
        }
        $this->logoPath = '';
        Configuracion::establecer('logo_path', '');
        $this->mensajeExito = 'Logo eliminado.';
    }

    public function probarWhatsapp(): void
    {
        $this->mensajePrueba = '';
        $service = new WhatsappService();

        if (!$service->estaConfigurado()) {
            $this->mensajePrueba = '❌ Faltan datos (URL, instancia o API Key). Guarda primero.';
            return;
        }

        $tel = $this->negocioTelefono ?: Configuracion::obtener('negocio_telefono', '');
        if (!$tel) {
            $this->mensajePrueba = '⚠️ Agrega el teléfono del negocio para enviar la prueba.';
            return;
        }

        try {
            $ok = $service->enviarMensaje($tel, "✅ Prueba de conexión EvoAPI — {$this->negocioNombre}\n" . now()->format('d/m/Y H:i'));
            $this->mensajePrueba = $ok
                ? '✅ Mensaje de prueba enviado correctamente.'
                : '❌ ' . $service->ultimoError;
        } catch (\Exception $e) {
            $this->mensajePrueba = '❌ Excepción: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.configuracion.index')
            ->layout('layouts.app', ['title' => 'Configuración']);
    }
}

