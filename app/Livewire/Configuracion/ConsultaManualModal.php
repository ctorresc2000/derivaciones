<?php

namespace App\Livewire\Configuracion;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class ConsultaManualModal extends Component
{
    public $abierto = false;
    public $pregunta = '';
    public $respuesta = '';
    public $buscando = false;
    public $tipoConsulta = 'resumen';

    #[On('abrir-modal-mc')]
    public function mostrar() {
        $this->abierto = true;
        $this->pregunta = '';
        $this->respuesta = '';
    }

    // public function consultar()
    // {
    //     if(empty($this->pregunta)) return;
    //     $this->buscando = true;
    //     $this->respuesta = '';

    //     $contexto = Storage::disk('public')->exists('manual.txt')
    //                 ? Storage::disk('public')->get('manual.txt')
    //                 : "No hay manual cargado.";

    //     try {
    //         $response = Http::withoutVerifying() // Evita problemas de SSL en Laragon
    //             ->withHeaders([
    //                 'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
    //             ])
    //             ->post('https://api.groq.com/openai/v1/chat/completions', [
    //                 'model' => 'llama-3.1-8b-instant', // En lugar de llama-3.3-70b
    //                 'messages' => [
    //                     [
    //                         'role' => 'system',
    //                         'content' => "Eres un experto en el Manual de Convivencia Escolar chileno. Tu tarea es responder dudas de forma clara, formal y estrictamente en idioma ESPAÑOL. Utiliza únicamente este fragmento para responder: " . $contexto
    //                     ],
    //                     [
    //                         'role' => 'user',
    //                         'content' => "Basado en el manual, responde la siguiente consulta (asegúrate de que la última frase sea correcta en español): " . $this->pregunta
    //                     ],
    //                 ],
    //             ]);

    //         $data = $response->json();

    //         // SI HAY UN ERROR EN LA API (Clave mal, modelo mal, etc)
    //         if ($response->failed() || !isset($data['choices'])) {
    //             $errorMensaje = $data['error']['message'] ?? 'Error desconocido en la API';
    //             $this->respuesta = "La IA dice: " . $errorMensaje;
    //             $this->buscando = false;
    //             return;
    //         }

    //         $this->respuesta = $data['choices'][0]['message']['content'];

    //     } catch (\Exception $e) {
    //         $this->respuesta = "Error de conexión: " . $e->getMessage();
    //     }

    //     $this->buscando = false;
    // }

    // public function consultar()
    // {
    //     if(empty($this->pregunta)) return;
    //     $this->buscando = true;
    //     $this->respuesta = '';

    //     $contexto = Storage::disk('public')->exists('manual.txt')
    //                 ? Storage::disk('public')->get('manual.txt')
    //                 : "No hay manual cargado.";

    //     // RECORTE CRÍTICO: Groq Free tiene un límite de 6000 tokens por minuto.
    //     // Recortamos a los primeros 12,000 caracteres para asegurar que entre.
    //     $contextoReducido = mb_substr($contexto, 0, 12000);

    //     try {
    //         $response = Http::withoutVerifying()
    //             ->withHeaders([
    //                 'Authorization' => 'Bearer ' . env('GROQ_API_KEY'),
    //             ])
    //             ->post('https://api.groq.com/openai/v1/chat/completions', [
    //                 'model' => 'llama-3.1-8b-instant',
    //                 'messages' => [
    //                     [
    //                         'role' => 'system',
    //                         'content' => "Eres experto en el Manual de Convivencia del Liceo Técnico San Miguel. Responde en español de forma breve usando este fragmento: " . $contextoReducido
    //                     ],
    //                     [
    //                         'role' => 'user',
    //                         'content' => $this->pregunta
    //                     ],
    //                 ],
    //                 'temperature' => 0.5,
    //                 'max_tokens' => 500,
    //             ]);

    //         $data = $response->json();

    //         if ($response->successful() && isset($data['choices'][0]['message']['content'])) {
    //             $this->respuesta = $data['choices'][0]['message']['content'];
    //         } else {
    //             $errorMensaje = $data['error']['message'] ?? 'Error desconocido';
    //             $this->respuesta = "Groq dice: " . $errorMensaje;
    //         }

    //     } catch (\Exception $e) {
    //         $this->respuesta = "Error: " . $e->getMessage();
    //     } finally {
    //         $this->buscando = false; // Esto garantiza que el botón se libere siempre
    //     }

    //     $this->buscando = false;
    // }

    public function consultar()
    {
        if(empty($this->pregunta)) return;
        $this->buscando = true;
        $this->respuesta = '';

        $contexto = Storage::disk('public')->exists('manual.txt')
                    ? Storage::disk('public')->get('manual.txt')
                    : "No hay manual cargado.";

        // Definimos los comportamientos según la opción elegida
        $prompts = [
            'resumen' => "Eres un asistente informativo. Tu objetivo es explicar qué dice el Manual de Convivencia sobre el tema consultado. Resume los puntos clave de forma amigable y educativa. No te enfoques en castigos, sino en la normativa y el espíritu de la convivencia escolar.",
            'sancion' => "Eres un extractor de normativa disciplinaria. Tu ÚNICA función es identificar faltas y sanciones. Responde con este formato estricto: 1. TIPO DE FALTA (Leve/Grave/Gravísima), 2. DESCRIPCIÓN SEGÚN MANUAL, 3. SANCIÓN/MEDIDA APLICABLE, 4. PROCEDIMIENTO A SEGUIR."
        ];

        $instruccionIA = $prompts[$this->tipoConsulta];

        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Authorization' => 'Bearer ' . env('GROQ_API_KEY')]) // Usamos Groq como definiste en tu .env
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.1-8b-instant',
                    'messages' => [
                        ['role' => 'system', 'content' => $instruccionIA . " Usa este texto como base: " . mb_substr($contexto, 0, 15000)],
                        ['role' => 'user', 'content' => $this->pregunta],
                    ],
                    'temperature' => 0.3,
                ]);

            $data = $response->json();
            $this->respuesta = $data['choices'][0]['message']['content'] ?? 'No se pudo generar una respuesta.';

        } catch (\Exception $e) {
            $this->respuesta = "Error de comunicación: " . $e->getMessage();
        } finally {
            $this->buscando = false;
        }
    }

    public function render() {
        return view('livewire.configuracion.consulta-manual-modal');
    }
}
