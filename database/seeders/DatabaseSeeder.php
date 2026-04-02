<?php

namespace Database\Seeders;

use App\Models\Curso;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Christian Torres',
            'email' => 'mail@mail.com',
            'rol'=>'Administrador',
            'password' => bcrypt('12345678'),
        ]);

        $cursos = [
            ['curso' => '1° A', 'descripcion' => 'HC','estado' => 'Activo'],
            ['curso' => '1° B', 'descripcion' => 'HC','estado' => 'Activo'],
            ['curso' => '1° C', 'descripcion' => 'HC','estado' => 'Activo'],
            ['curso' => '1° D', 'descripcion' => 'HC','estado' => 'Activo'],
            ['curso' => '2° A', 'descripcion' => 'HC','estado' => 'Activo'],
            ['curso' => '2° B', 'descripcion' => 'HC','estado' => 'Activo'],
            ['curso' => '2° C', 'descripcion' => 'HC','estado' => 'Activo'],
            ['curso' => '2° D', 'descripcion' => 'HC','estado' => 'Activo'],
            ['curso' => '3° A', 'descripcion' => 'Atención de la Párvulos','estado' => 'Activo'],
            ['curso' => '3° B', 'descripcion' => 'Atención de la Párvulos','estado' => 'Activo'],
            ['curso' => '3° C', 'descripcion' => 'Gastronomía','estado' => 'Activo'],
            ['curso' => '3° D', 'descripcion' => 'Gastronomía','estado' => 'Activo'],
            ['curso' => '3° E', 'descripcion' => 'Gastronomía','estado' => 'Activo'],
            ['curso' => '3° F', 'descripcion' => 'Vestuario y Confección Textil','estado' => 'Activo'],
            ['curso' => '3° G', 'descripcion' => 'Vestuario y Confección Textil','estado' => 'Activo'],
            ['curso' => '4° A', 'descripcion' => 'Atención de la Párvulos','estado' => 'Activo'],
            ['curso' => '4° B', 'descripcion' => 'Atención de la Párvulos','estado' => 'Activo'],
            ['curso' => '4° C', 'descripcion' => 'Gastronomía','estado' => 'Activo'],
            ['curso' => '4° D', 'descripcion' => 'Gastronomía','estado' => 'Activo'],
            ['curso' => '4° E', 'descripcion' => 'Atención de la Párvulos','estado' => 'Activo'],
            ['curso' => '4° F', 'descripcion' => 'Vestuario y Confección Textil','estado' => 'Activo'],
            ['curso' => '4° G', 'descripcion' => 'Vestuario y Confección Textil','estado' => 'Activo'],
        ];

        foreach ($cursos as $curso) {
            Curso::factory()->create($curso);
        }

    }
}
