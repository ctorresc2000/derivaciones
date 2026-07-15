<?php

namespace App\Livewire\Configuracion;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

class ConsultaManualModal extends Component
{
    public $abierto = false;
    public $pregunta = '';
    public $respuesta = '';
    public $buscando = false;
    public $tipoConsulta = 'resumen';
    public $motor = 'gemini'; // LO NUEVO: Selector de IA por defecto

    #[On('abrir-modal-mc')]
    public function mostrar() {
        $this->abierto = true;
        $this->pregunta = '';
        $this->respuesta = '';
    }

    // Controlador principal que decide qué IA usar
    public function consultar()
    {
        if(empty($this->pregunta)) return;

        $this->buscando = true;
        $this->respuesta = '';

        if ($this->motor === 'groq') {
            $this->consultarGroq();
        } else {
            $this->consultarGemini();
        }
    }

    // ----------------------------------------------------
    // IA 1: GROQ (TU CÓDIGO ORIGINAL INTACTO)
    // ----------------------------------------------------
    private function consultarGroq()
    {
        $contexto = Storage::disk('public')->exists('manual.txt')
                    ? Storage::disk('public')->get('manual.txt')
                    : "No hay manual cargado.";

        $prompts = [
            'resumen' => "Eres un asistente informativo. Tu objetivo es explicar qué dice el Manual de Convivencia sobre el tema consultado. Resume los puntos clave de forma amigable y educativa. No te enfoques en castigos, sino en la normativa y el espíritu de la convivencia escolar.",
            'sancion' => "Eres un extractor de normativa disciplinaria. Tu ÚNICA función es identificar faltas y sanciones. Responde con este formato estricto: 1. TIPO DE FALTA (Leve/Grave/Gravísima), 2. DESCRIPCIÓN SEGÚN MANUAL, 3. SANCIÓN/MEDIDA APLICABLE, 4. PROCEDIMIENTO A SEGUIR."
        ];

        $instruccionIA = $prompts[$this->tipoConsulta];

        try {
            $response = Http::withoutVerifying()
                ->withHeaders(['Authorization' => 'Bearer ' . env('GROQ_API_KEY')])
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => 'llama-3.1-8b-instant',
                    'messages' => [
                        ['role' => 'system', 'content' => $instruccionIA . " Usa este texto como base: " . mb_substr($contexto, 0, 15000)],
                        ['role' => 'user', 'content' => $this->pregunta],
                    ],
                    'temperature' => 0.3,
                ]);

            $data = $response->json();
            $this->respuesta = $data['choices'][0]['message']['content'] ?? 'No se pudo generar una respuesta con Groq.';

        } catch (\Exception $e) {
            $this->respuesta = "Error de comunicación con Groq: " . $e->getMessage();
        } finally {
            $this->buscando = false;
        }
    }

    // ----------------------------------------------------
    // IA 2: GEMINI 1.5 PRO (LA VERSIÓN POTENTE)
    // ----------------------------------------------------
    // private function consultarGemini()
    // {
    //     $contexto = Storage::disk('public')->exists('manual.txt')
    //                 ? Storage::disk('public')->get('manual.txt')
    //                 : "No hay manual cargado.";

    //     $prompts = [
    //         'resumen' => "Eres un asistente informativo. Tu objetivo es explicar qué dice el Manual de Convivencia sobre el tema consultado. Resume los puntos clave de forma amigable y educativa. No te enfoques en castigos, sino en la normativa y el espíritu de la convivencia escolar.",
    //         'sancion' => "Eres un extractor de normativa disciplinaria. Tu ÚNICA función es identificar faltas y sanciones. Responde con este formato estricto: 1. TIPO DE FALTA (Leve/Grave/Gravísima), 2. DESCRIPCIÓN SEGÚN MANUAL, 3. SANCIÓN/MEDIDA APLICABLE, 4. PROCEDIMIENTO A SEGUIR."
    //     ];

    //     $promptFinal = $prompts[$this->tipoConsulta] . "\n\n" .
    //                    "MANUAL DE CONVIVENCIA:\n" . $contexto . "\n\n" .
    //                    "CONSULTA DEL USUARIO:\n" . $this->pregunta . "\n\n" .
    //                    "Responde estrictamente basándote en el manual.";

    //     try {
    //         $response = Http::withoutVerifying()
    //             ->withHeaders([
    //                 'Content-Type' => 'application/json',
    //             ])
    //             ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
    //                 'contents' => [
    //                     [
    //                         'parts' => [
    //                             ['text' => $promptFinal]
    //                         ]
    //                     ]
    //                 ],
    //                 'generationConfig' => [
    //                     'temperature' => 0.1,
    //                 ]
    //             ]);

    //         $data = $response->json();

    //         if ($response->successful() && isset($data['candidates'][0]['content']['parts'][0]['text'])) {
    //             $this->respuesta = $data['candidates'][0]['content']['parts'][0]['text'];
    //         } else {
    //             $errorMensaje = $data['error']['message'] ?? 'Error desconocido en Gemini';
    //             $this->respuesta = "Error de IA (Gemini): " . $errorMensaje;
    //             Log::error('Fallo Gemini: ' . $response->body());
    //         }

    //     } catch (\Exception $e) {
    //         $this->respuesta = "Error de comunicación con Gemini: " . $e->getMessage();
    //     } finally {
    //         $this->buscando = false;
    //     }
    // }

    private function consultarGemini()
    {
        $contexto = Storage::disk('public')->exists('manual.txt')
                    ? Storage::disk('public')->get('manual.txt')
                    : "No hay manual cargado.";

        $prompts = [
            'resumen' => "Eres un asistente informativo. Tu objetivo es explicar qué dice el Manual de Convivencia sobre el tema consultado. Resume los puntos clave de forma amigable y educativa. No te enfoques en castigos, sino en la normativa y el espíritu de la convivencia escolar.",
            'sancion' => "Eres un extractor de normativa disciplinaria. Tu ÚNICA función es identificar faltas y sanciones. Responde con este formato estricto: 1. TIPO DE FALTA (Leve/Grave/Gravísima), 2. DESCRIPCIÓN SEGÚN MANUAL, 3. SANCIÓN/MEDIDA APLICABLE, 4. PROCEDIMIENTO A SEGUIR."
        ];

        $promptFinal = $prompts[$this->tipoConsulta] . "\n\n" .
                       "MANUAL DE CONVIVENCIA:\n" . $contexto . "\n\n" .
                       "CONSULTA DEL USUARIO:\n" . $this->pregunta . "\n\n" .
                       "Responde estrictamente basándote en el manual.";

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                // ¡AQUÍ ESTÁ LA MAGIA! Usamos gemini-2.5-pro, que está en tu lista permitida
                ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $promptFinal]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                    ]
                ]);

            $data = $response->json();

            if ($response->successful() && isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                $this->respuesta = $data['candidates'][0]['content']['parts'][0]['text'];
            } else {
                $errorMensaje = $data['error']['message'] ?? 'Error desconocido en Gemini';
                $this->respuesta = "Error de IA (Gemini): " . $errorMensaje;
                Log::error('Fallo Gemini: ' . $response->body());
            }

        } catch (\Exception $e) {
            $this->respuesta = "Error de comunicación con Gemini: " . $e->getMessage();
        } finally {
            $this->buscando = false;
        }
    }

    public function render() {
        return view('livewire.configuracion.consulta-manual-modal');
    }
}
