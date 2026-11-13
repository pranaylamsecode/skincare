<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmeOnboarding;

class CmeOnboardingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidates = [
            [
                'registration_id' => 'FMZ-CME-2611-1024',
                'programmes' => ['Beauty & Wellness'],
                'nursing_qualification' => null,
                'pincode' => '380007',
                'city' => 'Ahmedabad',
                'state' => 'Gujarat',
                'first_name' => 'Kavita',
                'last_name' => 'Patel',
                'age_group' => '25–34',
                'gender' => 'Female',
                'highest_qualification' => 'Graduate',
                'vehicle' => 'Two-wheeler',
                'driving_licence' => 'Yes',
                'mobile' => '9825012345',
                'is_whatsapp_same' => true,
                'whatsapp_number' => '9825012345',
                'email' => 'kavita.patel@gmail.com',
                'referral_source' => 'A person referred me',
                'referral_name' => 'Dhruvi Jyani',
                'referral_contact' => '9825000001',
                'status' => 'pending',
                'admin_notes' => 'Holds B.Sc in Chemistry. Very enthusiastic about Nu Skin galvanic facial techniques.',
            ],
            [
                'registration_id' => 'FMZ-CME-2611-1038',
                'programmes' => ['Beauty & Wellness', 'Nursing & Healthcare'],
                'nursing_qualification' => 'B.Sc Nursing',
                'pincode' => '382010',
                'city' => 'Gandhinagar',
                'state' => 'Gujarat',
                'first_name' => 'Pooja',
                'last_name' => 'Shah',
                'age_group' => '18–24',
                'gender' => 'Female',
                'highest_qualification' => 'Graduate',
                'vehicle' => 'Two-wheeler',
                'driving_licence' => 'Yes',
                'mobile' => '9724067890',
                'is_whatsapp_same' => true,
                'whatsapp_number' => '9724067890',
                'email' => 'pooja.shah@outlook.com',
                'referral_source' => 'On my own',
                'status' => 'reviewing',
                'admin_notes' => 'Qualified nurse with 2 years clinic experience. Wants flexible evening visits in Gandhinagar.',
            ],
            [
                'registration_id' => 'FMZ-CME-2611-1055',
                'programmes' => ['Beauty & Wellness'],
                'nursing_qualification' => null,
                'pincode' => '395007',
                'city' => 'Surat',
                'state' => 'Gujarat',
                'first_name' => 'Ananya',
                'last_name' => 'Vashi',
                'age_group' => '25–34',
                'gender' => 'Female',
                'highest_qualification' => 'Postgraduate',
                'vehicle' => 'Four-wheeler',
                'driving_licence' => 'Yes',
                'mobile' => '9898034567',
                'is_whatsapp_same' => true,
                'whatsapp_number' => '9898034567',
                'email' => 'ananya.vashi@gmail.com',
                'referral_source' => 'Through an organisation',
                'referral_org' => 'Gujarat Women Entrepreneurs Chamber',
                'status' => 'interview_scheduled',
                'admin_notes' => 'Shortlisted for Surat hub expansion. Information session scheduled on WhatsApp for Saturday 11 AM.',
            ],
            [
                'registration_id' => 'FMZ-CME-2611-1062',
                'programmes' => ['Carer & Aged Care'],
                'nursing_qualification' => null,
                'pincode' => '360001',
                'city' => 'Rajkot',
                'state' => 'Gujarat',
                'first_name' => 'Meena',
                'last_name' => 'Chauhan',
                'age_group' => '35–44',
                'gender' => 'Female',
                'highest_qualification' => '12th pass',
                'vehicle' => 'Two-wheeler',
                'driving_licence' => 'Yes',
                'mobile' => '9426078901',
                'is_whatsapp_same' => true,
                'whatsapp_number' => '9426078901',
                'email' => 'meena.c@yahoo.com',
                'referral_source' => 'On my own',
                'status' => 'approved',
                'admin_notes' => 'Completed ID verification and orientation. Ready for equipment training.',
            ],
            [
                'registration_id' => 'FMZ-CME-2611-1077',
                'programmes' => ['Nursing & Healthcare'],
                'nursing_qualification' => 'GNM',
                'pincode' => '390001',
                'city' => 'Vadodara',
                'state' => 'Gujarat',
                'first_name' => 'Riddhi',
                'last_name' => 'Desai',
                'age_group' => '25–34',
                'gender' => 'Female',
                'highest_qualification' => 'Diploma / ITI',
                'vehicle' => 'Two-wheeler',
                'driving_licence' => 'Yes',
                'mobile' => '9712098765',
                'is_whatsapp_same' => false,
                'whatsapp_number' => '9712098766',
                'email' => 'riddhi.desai@gmail.com',
                'referral_source' => 'On my own',
                'status' => 'pending',
                'admin_notes' => 'GNM registered with Gujarat Nursing Council. Waiting for preliminary document check.',
            ],
        ];

        foreach ($candidates as $c) {
            CmeOnboarding::updateOrCreate(
                ['registration_id' => $c['registration_id']],
                $c
            );
        }
    }
}
