<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Exam\ExamSession;
use App\Models\Exam\ExamCenter;
use App\Models\Exam\QuestionCategory;
use App\Models\Exam\Question;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ExamSeeder extends Seeder
{
    public function run(): void
    {
        // Create Exam Sessions
        $sessions = [
            [
                'name' => 'Spring 2024',
                'name_bn' => 'বসন্ত ২০২৪',
                'session' => '2024-Spring',
                'academic_year' => 2024,
                'term' => 'Spring',
                'start_date' => '2024-01-01',
                'end_date' => '2024-06-30',
                'status' => 'active',
            ],
            [
                'name' => 'Fall 2024',
                'name_bn' => 'শরৎ ২০২৪',
                'session' => '2024-Fall',
                'academic_year' => 2024,
                'term' => 'Fall',
                'start_date' => '2024-07-01',
                'end_date' => '2024-12-31',
                'status' => 'upcoming',
            ],
        ];

        foreach ($sessions as $sessionData) {
            ExamSession::create(array_merge($sessionData, [
                'uuid' => (string) Str::uuid(),
            ]));
        }

        // Create Exam Centers
        $centers = [
            [
                'center_code' => 'C001',
                'name' => 'Main Campus Center',
                'name_bn' => 'প্রধান ক্যাম্পাস কেন্দ্র',
                'building' => 'Academic Building A',
                'floor' => 'Ground',
                'address' => 'Main Campus, Dhaka',
                'capacity' => 100,
                'status' => 'active',
            ],
            [
                'center_code' => 'C002',
                'name' => 'Science Building Center',
                'name_bn' => 'বিজ্ঞান ভবন কেন্দ্র',
                'building' => 'Science Building',
                'floor' => '1st',
                'address' => 'Science Building, Dhaka',
                'capacity' => 60,
                'status' => 'active',
            ],
            [
                'center_code' => 'C003',
                'name' => 'Arts Building Center',
                'name_bn' => 'কলা ভবন কেন্দ্র',
                'building' => 'Arts Building',
                'floor' => '2nd',
                'address' => 'Arts Building, Dhaka',
                'capacity' => 50,
                'status' => 'active',
            ],
        ];

        foreach ($centers as $centerData) {
            ExamCenter::create(array_merge($centerData, [
                'uuid' => (string) Str::uuid(),
            ]));
        }

        // Create Question Categories
        $categories = [
            ['name' => 'Multiple Choice', 'name_bn' => 'বহুনির্বাচনী', 'code' => 'MCQ'],
            ['name' => 'Creative Questions', 'name_bn' => 'সৃজনশীল', 'code' => 'CQ'],
            ['name' => 'Short Questions', 'name_bn' => 'সংক্ষিপ্ত', 'code' => 'SHORT'],
            ['name' => 'True/False', 'name_bn' => 'সত্য/মিথ্যা', 'code' => 'TF'],
            ['name' => 'Fill in the Blank', 'name_bn' => 'শূন্যস্থান পূরণ', 'code' => 'FIB'],
        ];

        foreach ($categories as $index => $categoryData) {
            QuestionCategory::create(array_merge($categoryData, [
                'uuid' => (string) Str::uuid(),
                'order' => $index + 1,
            ]));
        }

        // Create Sample Questions
        $questions = [
            [
                'question_type' => 'mcq',
                'difficulty' => 'easy',
                'marks' => 1,
                'question' => 'What is the capital of Bangladesh?',
                'options' => ['Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi'],
                'correct_answer' => 'Dhaka',
            ],
            [
                'question_type' => 'mcq',
                'difficulty' => 'medium',
                'marks' => 1,
                'question' => 'Which planet is known as the Red Planet?',
                'options' => ['Venus', 'Mars', 'Jupiter', 'Saturn'],
                'correct_answer' => 'Mars',
            ],
            [
                'question_type' => 'true_false',
                'difficulty' => 'easy',
                'marks' => 1,
                'question' => 'Water freezes at 0°C.',
                'correct_answer' => 'True',
            ],
            [
                'question_type' => 'fill_blank',
                'difficulty' => 'medium',
                'marks' => 2,
                'question' => 'The process of photosynthesis requires ______ as a raw material.',
                'correct_answer' => 'Carbon dioxide',
            ],
            [
                'question_type' => 'cq',
                'difficulty' => 'hard',
                'marks' => 10,
                'question' => 'Explain the concept of supply and demand in economics with examples.',
                'correct_answer' => 'Supply and demand is the fundamental economic principle...',
            ],
        ];

        foreach ($questions as $questionData) {
            Question::create([
                'uuid' => (string) Str::uuid(),
                'question_code' => Question::generateQuestionCode(),
                'subject_id' => 1,
                'category_id' => 1,
                'chapter' => 'Chapter 1',
                'topic' => 'Basic Concepts',
                'question_type' => $questionData['question_type'],
                'difficulty' => $questionData['difficulty'],
                'marks' => $questionData['marks'],
                'question' => $questionData['question'],
                'options' => $questionData['options'] ?? null,
                'correct_answer' => $questionData['correct_answer'],
                'created_by' => 1,
                'status' => 'active',
            ]);
        }
    }
}
