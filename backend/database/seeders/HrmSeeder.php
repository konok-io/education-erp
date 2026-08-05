<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\HR\OnboardingChecklist;
use App\Models\HR\TrainingType;
use App\Models\HR\AwardType;
use Illuminate\Database\Seeder;

class HrmSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedOnboardingChecklists();
        $this->seedTrainingTypes();
        $this->seedAwardTypes();
    }

    private function seedOnboardingChecklists(): void
    {
        $checklists = [
            // Account Setup
            ['checklist_name' => 'Create User Account', 'category' => 'account', 'order' => 1],
            ['checklist_name' => 'Assign Role/Permission', 'category' => 'account', 'order' => 2],
            ['checklist_name' => 'Create Official Email', 'category' => 'account', 'order' => 3],

            // Documents
            ['checklist_name' => 'Issue Employee ID Card', 'category' => 'documents', 'order' => 4],
            ['checklist_name' => 'Collect Photocopies of Documents', 'category' => 'documents', 'order' => 5],
            ['checklist_name' => 'Biometric Registration', 'category' => 'documents', 'order' => 6],
            ['checklist_name' => 'Sign Employment Contract', 'category' => 'documents', 'order' => 7],

            // Equipment
            ['checklist_name' => 'Laptop/Desktop Assignment', 'category' => 'equipment', 'order' => 8],
            ['checklist_name' => 'Mobile Phone Assignment', 'category' => 'equipment', 'order' => 9],
            ['checklist_name' => 'Access Card Assignment', 'category' => 'equipment', 'order' => 10],
            ['checklist_name' => 'Vehicle/Transport Assignment', 'category' => 'equipment', 'order' => 11],

            // Training
            ['checklist_name' => 'Orientation Training', 'category' => 'training', 'order' => 12],
            ['checklist_name' => 'Safety Training', 'category' => 'training', 'order' => 13],
            ['checklist_name' => 'Department-specific Training', 'category' => 'training', 'order' => 14],
            ['checklist_name' => 'IT System Training', 'category' => 'training', 'order' => 15],

            // Payroll
            ['checklist_name' => 'Setup Payroll', 'category' => 'payroll', 'order' => 16],
            ['checklist_name' => 'Setup Bank Account', 'category' => 'payroll', 'order' => 17],
            ['checklist_name' => 'Setup Provident Fund', 'category' => 'payroll', 'order' => 18],
            ['checklist_name' => 'Assign Leave Policy', 'category' => 'payroll', 'order' => 19],
        ];

        foreach ($checklists as $checklist) {
            OnboardingChecklist::firstOrCreate(
                ['checklist_name' => $checklist['checklist_name']],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'category' => $checklist['category'],
                    'order' => $checklist['order'],
                    'is_required' => true,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Onboarding checklists seeded successfully!');
    }

    private function seedTrainingTypes(): void
    {
        $types = [
            ['name' => 'Orientation', 'code' => 'ORIENT', 'description' => 'New employee orientation program'],
            ['name' => 'Technical Skills', 'code' => 'TECH', 'description' => 'Technical skills development training'],
            ['name' => 'Leadership', 'code' => 'LEAD', 'description' => 'Leadership and management training'],
            ['name' => 'Communication', 'code' => 'COMM', 'description' => 'Communication skills training'],
            ['name' => 'Safety', 'code' => 'SAFETY', 'description' => 'Workplace safety training'],
            ['name' => 'IT System', 'code' => 'ITSYS', 'description' => 'IT systems and tools training'],
            ['name' => 'Compliance', 'code' => 'COMPLY', 'description' => 'Regulatory compliance training'],
            ['name' => 'Customer Service', 'code' => 'CUSVC', 'description' => 'Customer service excellence training'],
            ['name' => 'Product Knowledge', 'code' => 'PROD', 'description' => 'Product knowledge training'],
            ['name' => 'Professional Development', 'code' => 'PROF', 'description' => 'Professional development courses'],
            ['name' => 'Fire Safety', 'code' => 'FIRE', 'description' => 'Fire safety and emergency procedures'],
            ['name' => 'First Aid', 'code' => 'FAID', 'description' => 'First aid and medical emergencies'],
            ['name' => 'Harassment Prevention', 'code' => 'HARAS', 'description' => 'Workplace harassment prevention'],
            ['name' => 'Diversity & Inclusion', 'code' => 'DIVERS', 'description' => 'Diversity and inclusion training'],
        ];

        foreach ($types as $type) {
            TrainingType::firstOrCreate(
                ['code' => $type['code']],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'name' => $type['name'],
                    'description' => $type['description'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Training types seeded successfully!');
    }

    private function seedAwardTypes(): void
    {
        $types = [
            ['name' => 'Employee of the Month', 'code' => 'EOTM', 'is_monetary' => true, 'default_reward' => 5000],
            ['name' => 'Employee of the Year', 'code' => 'EOTY', 'is_monetary' => true, 'default_reward' => 25000],
            ['name' => 'Best Teacher Award', 'code' => 'BTCH', 'is_monetary' => true, 'default_reward' => 15000],
            ['name' => 'Research Award', 'code' => 'RESA', 'is_monetary' => true, 'default_reward' => 20000],
            ['name' => 'Long Service Award - 5 Years', 'code' => 'LNG5', 'is_monetary' => true, 'default_reward' => 10000],
            ['name' => 'Long Service Award - 10 Years', 'code' => 'LNG10', 'is_monetary' => true, 'default_reward' => 25000],
            ['name' => 'Performance Award', 'code' => 'PERF', 'is_monetary' => true, 'default_reward' => 8000],
            ['name' => 'Innovation Award', 'code' => 'INNO', 'is_monetary' => true, 'default_reward' => 15000],
            ['name' => 'Team Spirit Award', 'code' => 'TEAM', 'is_monetary' => false, 'default_reward' => 0],
            ['name' => 'Customer Service Award', 'code' => 'CUSA', 'is_monetary' => true, 'default_reward' => 5000],
            ['name' => 'Excellence Award', 'code' => 'EXCL', 'is_monetary' => true, 'default_reward' => 20000],
            ['name' => 'Best Researcher Award', 'code' => 'BRSC', 'is_monetary' => true, 'default_reward' => 30000],
        ];

        foreach ($types as $type) {
            AwardType::firstOrCreate(
                ['code' => $type['code']],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'name' => $type['name'],
                    'is_monetary' => $type['is_monetary'],
                    'default_reward' => $type['default_reward'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Award types seeded successfully!');
    }
}
