<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Library\LibraryCategory;
use App\Models\Library\LibraryAuthor;
use App\Models\Library\LibraryPublisher;
use App\Models\Library\LibraryFineRule;
use App\Models\Library\LibraryIssueRule;
use App\Models\Library\LibraryReadingRoomSeat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategories();
        $this->seedAuthors();
        $this->seedPublishers();
        $this->seedFineRules();
        $this->seedIssueRules();
        $this->seedReadingRoomSeats();
    }

    private function seedCategories(): void
    {
        $categories = [
            ['name' => 'Academic', 'code' => 'ACAD', 'lending_days' => 14, 'lending_limit' => 5],
            ['name' => 'Reference', 'code' => 'REF', 'lending_days' => 0, 'lending_limit' => 0, 'is_reference_only' => true],
            ['name' => 'Research', 'code' => 'RES', 'lending_days' => 30, 'lending_limit' => 10],
            ['name' => 'Magazine', 'code' => 'MAG', 'lending_days' => 7, 'lending_limit' => 2],
            ['name' => 'Journal', 'code' => 'JOUR', 'lending_days' => 7, 'lending_limit' => 2],
            ['name' => 'Newspaper', 'code' => 'NEWS', 'lending_days' => 1, 'lending_limit' => 1],
            ['name' => 'Story', 'code' => 'STOR', 'lending_days' => 21, 'lending_limit' => 3],
            ['name' => 'Novel', 'code' => 'NOV', 'lending_days' => 21, 'lending_limit' => 3],
            ['name' => 'Science', 'code' => 'SCIE', 'lending_days' => 14, 'lending_limit' => 5],
            ['name' => 'Technology', 'code' => 'TECH', 'lending_days' => 14, 'lending_limit' => 5],
            ['name' => 'Religion', 'code' => 'REL', 'lending_days' => 30, 'lending_limit' => 5],
            ['name' => 'Biography', 'code' => 'BIO', 'lending_days' => 21, 'lending_limit' => 3],
            ['name' => 'Children', 'code' => 'CHIL', 'lending_days' => 14, 'lending_limit' => 5],
            ['name' => 'Dictionary', 'code' => 'DICT', 'lending_days' => 30, 'lending_limit' => 1],
            ['name' => 'Encyclopedia', 'code' => 'ENCY', 'lending_days' => 7, 'lending_limit' => 1],
            ['name' => 'Question Bank', 'code' => 'QB', 'lending_days' => 14, 'lending_limit' => 3],
        ];

        foreach ($categories as $category) {
            LibraryCategory::firstOrCreate(
                ['code' => $category['code']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $category['name'],
                    'code' => $category['code'],
                    'lending_days' => $category['lending_days'],
                    'lending_limit' => $category['lending_limit'],
                    'is_reference_only' => $category['is_reference_only'] ?? false,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Library categories seeded successfully!');
    }

    private function seedAuthors(): void
    {
        $authors = [
            ['name' => 'William Shakespeare', 'country' => 'United Kingdom'],
            ['name' => 'Rabindranath Tagore', 'country' => 'India'],
            ['name' => 'Kazi Nazrul Islam', 'country' => 'Bangladesh'],
            ['name' => 'Humayun Ahmed', 'country' => 'Bangladesh'],
            ['name' => 'Sharif Hussein', 'country' => 'Bangladesh'],
            ['name' => 'Zia Haq', 'country' => 'Bangladesh'],
            ['name' => 'Albert Einstein', 'country' => 'Germany'],
            ['name' => 'Stephen Hawking', 'country' => 'United Kingdom'],
            ['name' => 'Isaac Newton', 'country' => 'United Kingdom'],
            ['name' => 'Charles Darwin', 'country' => 'United Kingdom'],
        ];

        foreach ($authors as $author) {
            LibraryAuthor::firstOrCreate(
                ['name' => $author['name']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $author['name'],
                    'country' => $author['country'],
                    'status' => 'active',
                ]
            );
        }

        $this->command->info('Library authors seeded successfully!');
    }

    private function seedPublishers(): void
    {
        $publishers = [
            ['name' => 'Oxford University Press', 'country' => 'United Kingdom'],
            ['name' => 'Cambridge University Press', 'country' => 'United Kingdom'],
            ['name' => 'Penguin Books', 'country' => 'United Kingdom'],
            ['name' => 'Bangla Academy', 'country' => 'Bangladesh'],
            ['name' => 'Anyaprokash', 'country' => 'Bangladesh'],
            ['name' => 'Agami Prokash', 'country' => 'Bangladesh'],
            ['name' => 'Shrabon Prokashani', 'country' => 'Bangladesh'],
            ['name' => 'McGraw Hill', 'country' => 'United States'],
            ['name' => 'Pearson Education', 'country' => 'United Kingdom'],
            ['name' => 'Wiley', 'country' => 'United States'],
        ];

        foreach ($publishers as $publisher) {
            LibraryPublisher::firstOrCreate(
                ['name' => $publisher['name']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $publisher['name'],
                    'status' => 'active',
                ]
            );
        }

        $this->command->info('Library publishers seeded successfully!');
    }

    private function seedFineRules(): void
    {
        $rules = [
            [
                'name' => 'Student Overdue Fine',
                'member_type' => 'student',
                'fine_type' => 'overdue',
                'amount' => 5,
                'grace_period' => 1,
                'max_fine' => 200,
            ],
            [
                'name' => 'Teacher Overdue Fine',
                'member_type' => 'teacher',
                'fine_type' => 'overdue',
                'amount' => 2,
                'grace_period' => 3,
                'max_fine' => 100,
            ],
            [
                'name' => 'Staff Overdue Fine',
                'member_type' => 'staff',
                'fine_type' => 'overdue',
                'amount' => 3,
                'grace_period' => 2,
                'max_fine' => 150,
            ],
            [
                'name' => 'Default Overdue Fine',
                'member_type' => 'all',
                'fine_type' => 'overdue',
                'amount' => 5,
                'grace_period' => 1,
                'max_fine' => 200,
            ],
        ];

        foreach ($rules as $rule) {
            LibraryFineRule::firstOrCreate(
                ['name' => $rule['name']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $rule['name'],
                    'member_type' => $rule['member_type'],
                    'fine_type' => $rule['fine_type'],
                    'amount' => $rule['amount'],
                    'grace_period' => $rule['grace_period'],
                    'max_fine' => $rule['max_fine'],
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Library fine rules seeded successfully!');
    }

    private function seedIssueRules(): void
    {
        $rules = [
            ['name' => 'Student Issue Rules', 'member_type' => 'student', 'max_books' => 5, 'max_days' => 14, 'max_renewals' => 2],
            ['name' => 'Teacher Issue Rules', 'member_type' => 'teacher', 'max_books' => 20, 'max_days' => 90, 'max_renewals' => 3],
            ['name' => 'Staff Issue Rules', 'member_type' => 'staff', 'max_books' => 10, 'max_days' => 30, 'max_renewals' => 2],
            ['name' => 'Guest Issue Rules', 'member_type' => 'guest', 'max_books' => 2, 'max_days' => 14, 'max_renewals' => 1],
        ];

        foreach ($rules as $rule) {
            LibraryIssueRule::firstOrCreate(
                ['name' => $rule['name']],
                [
                    'uuid' => Str::uuid(),
                    'name' => $rule['name'],
                    'member_type' => $rule['member_type'],
                    'max_books' => $rule['max_books'],
                    'max_days' => $rule['max_days'],
                    'max_renewals' => $rule['max_renewals'],
                    'allow_reservation' => true,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Library issue rules seeded successfully!');
    }

    private function seedReadingRoomSeats(): void
    {
        for ($floor = 1; $floor <= 2; $floor++) {
            $floorName = $floor === 1 ? 'Ground Floor' : 'First Floor';
            $zone = $floor === 1 ? 'Silent Zone' : 'General';

            for ($i = 1; $i <= 10; $i++) {
                $seatNo = sprintf('%s-%02d', $floor === 1 ? 'G' : 'F', $i);
                LibraryReadingRoomSeat::firstOrCreate(
                    ['seat_no' => $seatNo],
                    [
                        'uuid' => Str::uuid(),
                        'seat_no' => $seatNo,
                        'floor' => $floorName,
                        'zone' => $zone,
                        'seat_type' => $i <= 5 ? 'silent' : 'individual',
                        'has_power' => $i % 2 === 0,
                        'has_lamp' => true,
                        'status' => 'available',
                    ]
                );
            }
        }

        $this->command->info('Reading room seats seeded successfully!');
    }
}
