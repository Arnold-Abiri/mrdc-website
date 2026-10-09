<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Office of the Chief Executive Officer',
                'code' => 'DEPT-CEO',
                'description' => 'Internal: Office of the Chief Executive Officer.',
                'public_name' => 'Office of the Chief Executive Officer',
                'public_summary' => 'Corporate leadership, council business and coordination across all 29 wards from Stand 366 Mutoko Centre.',
                'public_description' => 'The Office of the Chief Executive Officer provides corporate leadership for Mutoko Rural District Council, coordinates council and committee business, and oversees implementation of resolutions across the district seat at Mutoko Centre and all 29 wards.',
                'responsibilities' => ['Council and committee business', 'Corporate coordination and performance', 'Town Board administration liaison', 'Stakeholder and government liaison'],
                'public_display_order' => 0,
            ],
            [
                'name' => 'Finance',
                'code' => 'DEPT-FIN',
                'description' => 'Internal: Finance department.',
                'public_name' => 'Finance Department',
                'public_summary' => 'Billing, rates, budgets, procurement support and accountable use of public funds.',
                'public_description' => 'The Finance Department manages council revenue, expenditure, billing and financial reporting for Mutoko Rural District Council, supporting transparent budgets and accountable service delivery.',
                'responsibilities' => ['Rates billing and revenue collection', 'Budgets and financial reporting', 'Expenditure control', 'Procurement and stores support'],
                'public_display_order' => 1,
            ],
            [
                'name' => 'Engineering and Works',
                'code' => 'DEPT-ENG',
                'description' => 'Internal: Engineering department.',
                'public_name' => 'Engineering & Works Department',
                'public_summary' => 'Roads, water points, sewer, public buildings, plant and the building inspectorate.',
                'public_description' => 'The Engineering and Works Department maintains the district road network, water and sewer infrastructure, public buildings and council plant, and provides the building inspectorate function across Mutoko District.',
                'responsibilities' => ['Road network development and maintenance', 'Water supply and sewer maintenance', 'Public buildings and infrastructure', 'Building inspectorate and plant maintenance'],
                'public_display_order' => 2,
            ],
            [
                'name' => 'Planning and Development',
                'code' => 'DEPT-PLAN',
                'description' => 'Internal: Planning department.',
                'public_name' => 'Planning & Development Department',
                'public_summary' => 'Serviced stands, leases, development control and property administration.',
                'public_description' => 'The Planning and Development Department administers land, layouts, leases and development control at Mutoko Centre, Jani growth point and rural service centres, including acquisition, maintenance and disposal of council property.',
                'responsibilities' => ['Layout planning and stand servicing', 'Leases and property administration', 'Development control and building standards', 'Business centre planning'],
                'public_display_order' => 3,
            ],
            [
                'name' => 'Social Services',
                'code' => 'DEPT-SOC',
                'description' => 'Internal: Social Services department.',
                'public_name' => 'Social Services Department',
                'public_summary' => 'Health centres, 84 primary and 44 secondary schools support, welfare and recreation including Chikondoma Stadium.',
                'public_description' => 'The Social Services Department supports rural health centres, 84 primary and 44 secondary schools, welfare services for vulnerable households, and recreation facilities including Chikondoma Stadium, halls and cultural events.',
                'responsibilities' => ['Rural health centres and outreach', 'Education support and school liaison', 'Welfare support for vulnerable groups', 'Sport, recreation and culture'],
                'public_display_order' => 4,
            ],
            [
                'name' => 'Human Resources and Administration',
                'code' => 'DEPT-HR',
                'description' => 'Internal: HR and Admin department.',
                'public_name' => 'Human Resources & Administration',
                'public_summary' => 'Staffing, records, customer care and council registry at Mutoko Centre.',
                'public_description' => 'Human Resources and Administration manages council staffing, records, registry and front-office customer care, so residents can reach the right office first time at Stand 366 Mutoko Centre.',
                'responsibilities' => ['Recruitment and staff welfare', 'Records and registry', 'Customer care and switchboard', 'Committee secretariat support'],
                'public_display_order' => 5,
            ],
            [
                'name' => 'Internal Audit',
                'code' => 'DEPT-AUDIT',
                'description' => 'Internal: Internal Audit unit.',
                'public_name' => 'Internal Audit Unit',
                'public_summary' => 'Independent assurance on risk, controls and compliance.',
                'public_description' => 'The Internal Audit Unit provides independent assurance on risk management, internal controls and compliance with council policies and the law.',
                'responsibilities' => ['Risk-based audit reviews', 'Controls and compliance checks', 'Follow-up on audit actions'],
                'public_display_order' => 6,
            ],
        ];

        foreach ($departments as $definition) {
            $record = Department::query()->where('code', $definition['code'])->first();
            $attributes = [
                ...$definition,
                'status' => 'active',
                'sort_order' => $definition['public_display_order'],
                'public_status' => 'published',
                'public_verification_status' => 'publishable',
                'public_published_at' => now(),
            ];
            if ($record) {
                $record->update($attributes);
            } else {
                Department::query()->create($attributes);
            }
        }
    }
}
