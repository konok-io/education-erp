<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Casual Leave',
                'name_bn' => 'মজুরি ছুটি',
                'code' => 'CL',
                'short_code' => 'CL',
                'leave_days' => 10,
                'is_paid' => true,
                'is_encashable' => false,
                'is_carry_forward' => false,
                'max_consecutive_days' => 5,
                'max_carry_forward_days' => 0,
                'requires_approval' => true,
            ],
            [
                'name' => 'Sick Leave',
                'name_bn' => 'অসুস্থতার ছুটি',
                'code' => 'SL',
                'short_code' => 'SL',
                'leave_days' => 10,
                'is_paid' => true,
                'is_encashable' => false,
                'is_carry_forward' => false,
                'max_consecutive_days' => 5,
                'max_carry_forward_days' => 0,
                'requires_approval' => true,
            ],
            [
                'name' => 'Annual Leave',
                'name_bn' => 'বার্ষিক ছুটি',
                'code' => 'AL',
                'short_code' => 'AL',
                'leave_days' => 20,
                'is_paid' => true,
                'is_encashable' => true,
                'is_carry_forward' => true,
                'max_consecutive_days' => 15,
                'max_carry_forward_days' => 10,
                'requires_approval' => true,
            ],
            [
                'name' => 'Maternity Leave',
                'name_bn' => 'প্রসূতি ছুটি',
                'code' => 'ML',
                'short_code' => 'ML',
                'leave_days' => 90,
                'is_paid' => true,
                'is_encashable' => false,
                'is_carry_forward' => false,
                'max_consecutive_days' => 90,
                'max_carry_forward_days' => 0,
                'requires_approval' => true,
            ],
            [
                'name' => 'Paternity Leave',
                'name_bn' => 'পিতৃত্ব ছুটি',
                'code' => 'PL',
                'short_code' => 'PL',
                'leave_days' => 10,
                'is_paid' => true,
                'is_encashable' => false,
                'is_carry_forward' => false,
                'max_consecutive_days' => 5,
                'max_carry_forward_days' => 0,
                'requires_approval' => true,
            ],
            [
                'name' => 'Study Leave',
                'name_bn' => 'অধ্যয়ন ছুটি',
                'code' => 'STL',
                'short_code' => 'STL',
                'leave_days' => 15,
                'is_paid' => true,
                'is_encashable' => false,
                'is_carry_forward' => false,
                'max_consecutive_days' => 15,
                'max_carry_forward_days' => 0,
                'requires_approval' => true,
            ],
            [
                'name' => 'Leave Without Pay',
                'name_bn' => 'বেতন ব্যতীত ছুটি',
                'code' => 'LWP',
                'short_code' => 'LWP',
                'leave_days' => 30,
                'is_paid' => false,
                'is_encashable' => false,
                'is_carry_forward' => false,
                'max_consecutive_days' => 30,
                'max_carry_forward_days' => 0,
                'requires_approval' => true,
            ],
            [
                'name' => 'Special Leave',
                'name_bn' => 'বিশেষ ছুটি',
                'code' => 'SPL',
                'short_code' => 'SPL',
                'leave_days' => 5,
                'is_paid' => true,
                'is_encashable' => false,
                'is_carry_forward' => false,
                'max_consecutive_days' => 5,
                'max_carry_forward_days' => 0,
                'requires_approval' => true,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            DB::table('leave_types')->updateOrInsert(
                ['code' => $leaveType['code']],
                [
                    'uuid' => Str::uuid()->toString(),
                    'name' => $leaveType['name'],
                    'name_bn' => $leaveType['name_bn'],
                    'short_code' => $leaveType['short_code'],
                    'leave_days' => $leaveType['leave_days'],
                    'is_paid' => $leaveType['is_paid'],
                    'is_encashable' => $leaveType['is_encashable'],
                    'is_carry_forward' => $leaveType['is_carry_forward'],
                    'max_consecutive_days' => $leaveType['max_consecutive_days'],
                    'max_carry_forward_days' => $leaveType['max_carry_forward_days'],
                    'requires_approval' => $leaveType['requires_approval'],
                    'is_active' => true,
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
