<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DesignFactor;
use App\Models\DesignFactorOption;

class DesignFactorSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'code'        => 'DF01',
                'name'        => 'Enterprise Strategy',
                'description' => 'Pilih satu strategi utama organisasi.',
                'type'        => 'single_choice',
                'options'     => [
                    ['value' => 'growth',     'label' => 'Growth / Acquisition'],
                    ['value' => 'client',     'label' => 'Client Service / Stability'],
                    ['value' => 'cost',       'label' => 'Cost Leadership'],
                    ['value' => 'innovation', 'label' => 'Innovation / Differentiation'],
                ],
            ],
            [
                'code'        => 'DF02',
                'name'        => 'Enterprise Goals',
                'description' => 'Beri skor prioritas (1–5) untuk setiap enterprise goal.',
                'type'        => 'rating',
                'options'     => [
                    ['value' => 'EG01', 'label' => 'Portfolio of Competitive Products'],
                    ['value' => 'EG02', 'label' => 'Managed Business Risk'],
                    ['value' => 'EG03', 'label' => 'Compliance'],
                    ['value' => 'EG04', 'label' => 'Quality of Financial Information'],
                    ['value' => 'EG05', 'label' => 'Customer-Oriented Service Culture'],
                    ['value' => 'EG06', 'label' => 'Business Service Continuity'],
                    ['value' => 'EG07', 'label' => 'Quality of Management Information'],
                    ['value' => 'EG08', 'label' => 'Optimization of Internal Business Process Functionality'],
                    ['value' => 'EG09', 'label' => 'Optimization of Business Process Costs'],
                    ['value' => 'EG10', 'label' => 'Staff Skills and Motivation'],
                    ['value' => 'EG11', 'label' => 'Compliance with Internal Policies'],
                    ['value' => 'EG12', 'label' => 'Managed Digital Transformation Programs'],
                    ['value' => 'EG13', 'label' => 'Product and Business Innovation'],
                ],
            ],
            [
                'code'        => 'DF03',
                'name'        => 'Risk Profile',
                'description' => 'Tentukan tingkat risiko (1–5).',
                'type'        => 'rating',
                'options'     => [
                    ['value' => 'IT_investment',        'label' => 'IT investment decision making'],
                    ['value' => 'program_lifecycle',    'label' => 'Program & projects lifecycle management'],
                    ['value' => 'IT_expertise',         'label' => 'IT expertise & skills'],
                    ['value' => 'staff_operations',     'label' => 'Staff operations'],
                    ['value' => 'information',          'label' => 'Information'],
                    ['value' => 'architecture',         'label' => 'Architecture'],
                    ['value' => 'infrastructure',       'label' => 'Infrastructure'],
                    ['value' => 'software',             'label' => 'Software'],
                    ['value' => 'business_continuity',  'label' => 'Business continuity'],
                    ['value' => 'unauthorized_actions', 'label' => 'Unauthorized actions'],
                    ['value' => 'software_adoption',    'label' => 'Software adoption / usage problems'],
                    ['value' => 'hardware_incidents',   'label' => 'Hardware incidents'],
                    ['value' => 'software_failures',    'label' => 'Software failures'],
                    ['value' => 'logical_attacks',      'label' => 'Logical attacks (hacking, malware, etc.)'],
                    ['value' => 'third_party',          'label' => 'Third-party / supplier incidents'],
                    ['value' => 'noncompliance',        'label' => 'Noncompliance'],
                    ['value' => 'geopolitical',         'label' => 'Geopolitical issues'],
                    ['value' => 'industrial_action',    'label' => 'Industrial action'],
                    ['value' => 'acts_of_nature',       'label' => 'Acts of nature'],
                    ['value' => 'innovation',           'label' => 'Innovation'],
                ],
            ],
            [
                'code'        => 'DF04',
                'name'        => 'I&T Related Issues',
                'description' => 'Pilih isu-isu yang memang terjadi pada organisasi (boleh lebih dari satu).',
                'type'        => 'multiple_choice',
                'options'     => [
                    ['value' => 'frustration',           'label' => 'Frustration between different IT entities'],
                    ['value' => 'low_quality',           'label' => 'Low-quality IT services'],
                    ['value' => 'significant_incidents', 'label' => 'Significant incidents related to IT'],
                    ['value' => 'service_delivery',      'label' => 'Service delivery problems by outsourcers'],
                    ['value' => 'failures',              'label' => 'Failures / shortfalls to meet regulatory or contractual requirements'],
                    ['value' => 'regular_audit',         'label' => 'Regular audit findings'],
                    ['value' => 'business_dissatisfied', 'label' => 'Business dissatisfaction with IT quality'],
                    ['value' => 'board_concerns',        'label' => 'Board members or senior management concerns'],
                    ['value' => 'complex_architecture',  'label' => 'Complex IT architecture'],
                    ['value' => 'high_spending',         'label' => 'High level of IT-related spending'],
                    ['value' => 'dissatisfied_users',    'label' => 'Dissatisfied business users'],
                    ['value' => 'multiple_initiatives',  'label' => 'Multiple and complex initiatives'],
                    ['value' => 'data_quality',          'label' => 'Problems with data quality'],
                    ['value' => 'lack_knowledge',        'label' => 'Lack of knowledge / skills'],
                    ['value' => 'inability_exploit',     'label' => 'Inability to exploit new technologies'],
                ],
            ],
            [
                'code'        => 'DF05',
                'name'        => 'Threat Landscape',
                'description' => 'Pilih tingkat ancaman yang relevan bagi organisasi.',
                'type'        => 'single_choice',
                'options'     => [
                    ['value' => 'normal', 'label' => 'Normal'],
                    ['value' => 'high',   'label' => 'High'],
                ],
            ],
            [
                'code'        => 'DF06',
                'name'        => 'Compliance Requirements',
                'description' => 'Pilih tingkat kebutuhan kepatuhan organisasi.',
                'type'        => 'single_choice',
                'options'     => [
                    ['value' => 'low',    'label' => 'Low'],
                    ['value' => 'normal', 'label' => 'Normal'],
                    ['value' => 'high',   'label' => 'High'],
                ],
            ],
            [
                'code'        => 'DF07',
                'name'        => 'Role of IT',
                'description' => 'Pilih peran utama TI dalam organisasi.',
                'type'        => 'single_choice',
                'options'     => [
                    ['value' => 'support',    'label' => 'Support'],
                    ['value' => 'factory',    'label' => 'Factory'],
                    ['value' => 'turnaround', 'label' => 'Turnaround'],
                    ['value' => 'strategic',  'label' => 'Strategic'],
                ],
            ],
            [
                'code'        => 'DF08',
                'name'        => 'Sourcing Model for IT',
                'description' => 'Pilih model sourcing utama yang digunakan organisasi.',
                'type'        => 'single_choice',
                'options'     => [
                    ['value' => 'outsourcing', 'label' => 'Outsourcing'],
                    ['value' => 'cloud',       'label' => 'Cloud'],
                    ['value' => 'insourced',   'label' => 'Insourced'],
                    ['value' => 'hybrid',      'label' => 'Hybrid'],
                ],
            ],
            [
                'code'        => 'DF09',
                'name'        => 'IT Implementation Methods',
                'description' => 'Pilih metode implementasi yang paling sesuai dengan praktik TI organisasi.',
                'type'        => 'single_choice',
                'options'     => [
                    ['value' => 'agile',       'label' => 'Agile'],
                    ['value' => 'devops',      'label' => 'DevOps'],
                    ['value' => 'traditional', 'label' => 'Traditional'],
                    ['value' => 'hybrid',      'label' => 'Hybrid'],
                ],
            ],
            [
                'code'        => 'DF10',
                'name'        => 'Technology Adoption Strategy',
                'description' => 'Seberapa cepat organisasi mengadopsi teknologi baru.',
                'type'        => 'single_choice',
                'options'     => [
                    ['value' => 'first_mover',  'label' => 'First mover'],
                    ['value' => 'follower',     'label' => 'Follower'],
                    ['value' => 'slow_adopter', 'label' => 'Slow adopter'],
                ],
            ],
            [
                'code'        => 'DF11',
                'name'        => 'Enterprise Size',
                'description' => 'Pilih ukuran organisasi berdasarkan jumlah pegawai.',
                'type'        => 'single_choice',
                'options'     => [
                    ['value' => 'large',  'label' => 'Large (> 1.000 employees)'],
                    ['value' => 'medium', 'label' => 'Medium (250 – 1.000 employees)'],
                    ['value' => 'small',  'label' => 'Small (< 250 employees)'],
                ],
            ],
        ];

        foreach ($data as $item) {
            $df = DesignFactor::updateOrCreate(
                ['code' => $item['code']],
                [
                    'name'        => $item['name'],
                    'description' => $item['description'],
                    'type'        => $item['type'],
                ]
            );

            foreach ($item['options'] as $index => $opt) {
                // Cari existing dulu
                $option = DesignFactorOption::firstOrNew([
                    'design_factor_id' => $df->id_df,
                    'value'            => $opt['value'],
                ]);

                // Set kolom yang ada di fillable
                $option->label = $opt['label'];
                $option->order = $index + 1;

                // Kolom 'name' wajib di DB (meski tidak ada di fillable & migration yang Anda kirim)
                $option->name = $opt['label'];

                $option->save();
            }
        }
    }
}