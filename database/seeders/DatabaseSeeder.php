<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\Evaluation;
use App\Models\Note;
use App\Models\Role;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Création des rôles
        $admin = Role::factory()->create(['wording' => 'admin']);
        $prof = Role::factory()->create(['wording' => 'professor']);
        $student = Role::factory()->create(['wording' => 'student']);

        // Création des salles de classe
        $btsSioSisr = Classroom::factory()->create(['name' => 'BTS SIO SISR']);
        $btsSioSlam = Classroom::factory()->create(['name' => 'BTS SIO SLAM']);
        $bachelorCda = Classroom::factory()->create(['name' => 'Bachelor CDA']);

        // Création d'un administrateur
        User::factory()
            ->for($admin)
            ->create([
                'firstname' => 'Admin',
                'lastname' => 'Admin',
                'email' => 'admin@example.com',
                'username' => 'admin',
                'password' => Hash::make('administrator')
            ]);

        // Création d'un professeur et association à deux salles de classe
        $professor = User::factory()
            ->for($prof)
            ->hasAttached([$btsSioSlam, $bachelorCda])
            ->create([
                'firstname' => 'Adnan',
                'lastname' => 'RIHAN',
                'email' => 'adnan@example.com',
                'username' => 'adnan',
                'password' => Hash::make('professor')
            ]);

        // Création d'étudiants et association à une salle de classe aléatoire
        $classrooms = [$btsSioSisr, $btsSioSlam, $bachelorCda];
        User::factory()
            ->for($student)
            ->count(10)
            ->create()
            ->each(function ($user) use ($classrooms) {
                $user->classrooms()->attach(
                    $classrooms[array_rand($classrooms)]
                );
            });

        // Création d'une évaluation par le professeur pour la salle de classe "BTS SIO SLAM"
        $evaluation = Evaluation::factory()
            ->create([
                'title' => 'Examen de programmation',
                'description' => 'Évaluation des compétences en PHP et Laravel',
                'min_value' => 0,
                'max_value' => 20,
            ]);

        // Récupère les étudiants de la salle de classe "BTS SIO SLAM"
        $studentsInClassroom = $btsSioSlam->users()->where('role_id', $student->id)->get();

        // Attribue des notes à ces étudiants pour l'évaluation
        foreach ($studentsInClassroom as $student) {
            Note::factory()
                ->create([
                    'value' => rand(0, 20), // Note aléatoire entre 0 et 20
                    'user_id' => $student->id,
                    'evaluation_id' => $evaluation->id,
                ]);
        }
    }
}
