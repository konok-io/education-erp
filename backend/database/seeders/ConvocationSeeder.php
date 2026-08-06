<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Convocation\Convocation;
use App\Models\Document\DocumentVerification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ConvocationSeeder extends Seeder
{
    public function run(): void
    {
        // Create Sample Convocations
        $convocations = [
            [
                'name' => '11th Convocation 2024',
                'name_bn' => '১১তম সমাবর্তন ২০২৪',
                'year' => 2024,
                'semester' => 'Spring',
                'ceremony_date' => '2024-03-15',
                'start_time' => '10:00',
                'end_time' => '14:00',
                'venue' => 'Main Auditorium',
                'address' => 'Main Campus, Dhaka',
                'chief_guest' => 'Honorable President',
                'special_guest' => 'Education Minister',
                'expected_attendees' => 500,
                'registration_fee' => 1000,
                'status' => 'registration',
            ],
            [
                'name' => '10th Convocation 2023',
                'name_bn' => '১০ম সমাবর্তন ২০২৩',
                'year' => 2023,
                'semester' => 'Spring',
                'ceremony_date' => '2023-03-20',
                'start_time' => '10:00',
                'end_time' => '14:00',
                'venue' => 'Main Auditorium',
                'address' => 'Main Campus, Dhaka',
                'chief_guest' => 'Honorable President',
                'expected_attendees' => 450,
                'registration_fee' => 800,
                'status' => 'completed',
            ],
        ];

        foreach ($convocations as $convocationData) {
            $convocation = Convocation::create(array_merge($convocationData, [
                'uuid' => (string) Str::uuid(),
                'convocation_no' => Convocation::generateConvocationNo(),
            ]));
        }

        // Create Sample Document Verifications
        $verifications = [
            [
                'applicant_name' => 'Rahim Ahmed',
                'applicant_email' => 'rahim@example.com',
                'applicant_phone' => '01712345678',
                'document_type' => 'certificate',
                'document_name' => 'Graduation Certificate',
                'document_number' => 'CERT-2023-001',
                'verification_type' => 'employer',
                'verifier_name' => 'HR Manager',
                'verifier_organization' => 'ABC Corp',
                'status' => 'verified',
                'verified_at' => now(),
            ],
            [
                'applicant_name' => 'Fatima Begum',
                'applicant_email' => 'fatima@example.com',
                'applicant_phone' => '01812345678',
                'document_type' => 'transcript',
                'document_name' => 'Academic Transcript',
                'document_number' => 'TRN-2023-005',
                'verification_type' => 'institution',
                'verifier_name' => 'Admissions Office',
                'verifier_organization' => 'XYZ University',
                'status' => 'pending',
            ],
        ];

        foreach ($verifications as $verificationData) {
            $uuid = (string) Str::uuid();
            DocumentVerification::create(array_merge($verificationData, [
                'uuid' => $uuid,
                'verification_no' => DocumentVerification::generateVerificationNo(),
                'qr_code' => $uuid,
                'verification_link' => DocumentVerification::generateVerificationLink($uuid),
            ]));
        }
    }
}
