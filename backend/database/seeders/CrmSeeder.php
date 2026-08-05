<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CRM\CrmKnowledgeBase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CrmSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedKnowledgeBase();
    }

    private function seedKnowledgeBase(): void
    {
        $articles = [
            // FAQs
            ['title' => 'How to apply for admission?', 'type' => 'faq', 'category' => 'admission'],
            ['title' => 'What are the admission requirements?', 'type' => 'faq', 'category' => 'admission'],
            ['title' => 'How to pay tuition fees?', 'type' => 'faq', 'category' => 'accounts'],
            ['title' => 'How to check exam results?', 'type' => 'faq', 'category' => 'result'],
            ['title' => 'How to apply for leave?', 'type' => 'faq', 'category' => 'attendance'],
            ['title' => 'How to borrow books from library?', 'type' => 'faq', 'category' => 'library'],
            ['title' => 'How to register for hostel?', 'type' => 'faq', 'category' => 'hostel'],
            ['title' => 'How to apply for transport facility?', 'type' => 'faq', 'category' => 'transport'],

            // Policies
            ['title' => 'Attendance Policy', 'type' => 'policy', 'category' => 'attendance'],
            ['title' => 'Examination Policy', 'type' => 'policy', 'category' => 'exam'],
            ['title' => 'Fee Refund Policy', 'type' => 'policy', 'category' => 'accounts'],
            ['title' => 'Library Rules and Regulations', 'type' => 'policy', 'category' => 'library'],
            ['title' => 'Hostel Management Policy', 'type' => 'policy', 'category' => 'hostel'],
            ['title' => 'Transport Policy', 'type' => 'policy', 'category' => 'transport'],

            // Tutorials
            ['title' => 'How to use the student portal', 'type' => 'tutorial', 'category' => 'general'],
            ['title' => 'How to access online classes', 'type' => 'tutorial', 'category' => 'academic'],
            ['title' => 'How to submit assignments', 'type' => 'tutorial', 'category' => 'academic'],
            ['title' => 'How to view attendance records', 'type' => 'tutorial', 'category' => 'attendance'],
        ];

        foreach ($articles as $index => $article) {
            $content = match ($article['type']) {
                'faq' => "This is a frequently asked question about {$article['category']}. " .
                    "Please find the detailed answer below.\n\n" .
                    "If you need further assistance, please contact our support team.",
                'policy' => "{$article['title']}\n\n" .
                    "1. Purpose\n" .
                    "This policy outlines the rules and guidelines for {$article['category']}.\n\n" .
                    "2. Scope\n" .
                    "This policy applies to all students and staff members.\n\n" .
                    "3. Guidelines\n" .
                    "Please follow the guidelines provided to ensure compliance.",
                'tutorial' => "Step-by-step guide for {$article['title']}\n\n" .
                    "Step 1: Log in to your account\n" .
                    "Step 2: Navigate to the relevant section\n" .
                    "Step 3: Follow the on-screen instructions\n" .
                    "Step 4: Complete the process\n\n" .
                    "If you face any issues, please contact support.",
                default => "Article content for {$article['title']}",
            };

            CrmKnowledgeBase::firstOrCreate(
                ['slug' => Str::slug($article['title'])],
                [
                    'uuid' => Str::uuid(),
                    'title' => $article['title'],
                    'slug' => Str::slug($article['title']),
                    'content' => $content,
                    'summary' => substr($content, 0, 150) . '...',
                    'category' => $article['category'],
                    'type' => $article['type'],
                    'author_id' => 1,
                    'is_published' => true,
                    'is_featured' => $index < 3,
                    'order' => $index + 1,
                ]
            );
        }

        $this->command->info('Knowledge base articles seeded successfully!');
    }
}
