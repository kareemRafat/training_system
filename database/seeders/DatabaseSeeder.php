<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Comment;
use App\Models\Group;
use App\Models\Instructor;
use App\Models\RepeatedStudent;
use App\Models\Student;
use App\Models\TrainingGroup;
use App\Models\User;
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
            'name' => 'كريم الدين',
            'username' => 'kareem',
            'email' => 'kareem@mail.com',
            'password' => bcrypt('12345678'),
            'is_active' => 'active',
        ]);

        User::factory()->create([
            'name' => 'هندالشال',
            'username' => 'hend',
            'email' => 'hend@mail.com',
            'password' => bcrypt('12345678'),
            'branch_id' => 1,
            'is_active' => 'banned',
        ]);

        // branches
        $branches = ['Mansoura', 'Tanta', 'Zagazig'];
        $branchesInArabic = ['المنصورة', 'طنطا', 'الزقازيق'];
        foreach ($branches as $index => $branch) {
            Branch::factory()->create([
                'name' => $branch,
                'arabic_name' => $branchesInArabic[$index],
            ]);
        }

        // instructors
        Instructor::factory(4)->create();

        // groups
        $groups = ['london', 'cut', 'lock', 'home', 'nike'];
        foreach ($groups as $group) {
            Group::factory()->create(['name' => $group]);
        }

        // training groups
        $trainingGroups = ['training1', 'training2', 'training3'];
        foreach ($trainingGroups as $trainingGroup) {
            TrainingGroup::factory()->create(['name' => $trainingGroup]);
        }

        // students
        Student::factory(100)->create();

        // comments
        Comment::factory(10)->create();

        // Repeated Sutdent
        RepeatedStudent::factory(10)->create();
    }
}
