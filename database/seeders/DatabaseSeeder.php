<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Student;
use App\Models\KtmTemplate;
use App\Models\BatchActivity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@university.edu',
            'password' => Hash::make('password'),
        ]);

        // Create active KTM template
        KtmTemplate::create([
            'name' => 'Default Template 2024',
            'front_template' => 'templates/front-default.png',
            'back_template' => 'templates/back-default.png',
            'settings' => [
                'orientation' => 'portrait',
                'width' => 85.6,
                'height' => 53.98,
            ],
        ]);

        // Create sample students
        $classes = ['Class A', 'Class B', 'Class C', 'Class D'];
        $prodis = ['Computer Science', 'Information Systems', 'Software Engineering', 'Data Science'];

        $studentData = [];
        for ($i = 1; $i <= 1240; $i++) {
            $status = 'pending';
            $generatedAt = null;
            $errorMessage = null;

            // 850 generated, 2 failed, rest pending
            if ($i <= 850) {
                $status = 'generated';
                $generatedAt = now()->subDays(rand(1, 60));
            } elseif ($i <= 852) {
                $status = 'failed';
                $errorMessage = 'Failed to process student photo';
            }

            $studentData[] = [
                'nim' => '2024' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'name' => 'Student ' . $i,
                'email' => 'student' . $i . '@university.edu',
                'class' => $classes[array_rand($classes)],
                'prodi' => $prodis[array_rand($prodis)],
                'photo' => null,
                'created_at' => now()->subDays(rand(1, 90)),
                'updated_at' => now(),
            ];

            // Insert in batches of 100
            if (count($studentData) >= 100) {
                Student::insert($studentData);
                $studentData = [];
            }
        }

        // Insert remaining students
        if (!empty($studentData)) {
            Student::insert($studentData);
        }

        // Create sample batch activities
        BatchActivity::create([
            'batch_id' => '#BATCH-2024-089',
            'action' => 'Generate KTM (Class A)',
            'status' => 'completed',
            'processed_count' => 45,
            'failed_count' => 0,
            'user_id' => 1,
            'created_at' => now()->subDays(1),
        ]);

        BatchActivity::create([
            'batch_id' => '#BATCH-2024-088',
            'action' => 'Template Update',
            'status' => 'uploaded',
            'processed_count' => 0,
            'failed_count' => 0,
            'user_id' => 1,
            'created_at' => now()->subDays(2),
        ]);

        BatchActivity::create([
            'batch_id' => '#BATCH-2024-087',
            'action' => 'Generate KTM (Class B)',
            'status' => 'failed',
            'processed_count' => 43,
            'failed_count' => 2,
            'user_id' => 1,
            'created_at' => now()->subDays(3),
        ]);
    }
}
