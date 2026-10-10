<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use App\Models\Vacancy;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VacancySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminUser = User::query()->first();
        $adminId = $adminUser?->id;

        $financeDept = Department::query()->where('name', 'Finance')->first();
        $planningDept = Department::query()->where('name', 'Planning and Development')->first();
        $engineeringDept = Department::query()->where('name', 'Engineering and Works')->first();
        $hrDept = Department::query()->where('name', 'Human Resources and Administration')->first();

        $vacancies = [
            [
                'slug' => 'graduate-trainee-finance',
                'title' => 'Graduate Trainee - Finance',
                'grade' => 'Graduate Trainee',
                'reference' => 'MRDC/FIN/GT/2025/01',
                'employment_type' => 'internship',
                'department_id' => $financeDept?->id,
                'description' => 'Applications are invited from suitably qualified, experienced, self-motivated and task-oriented individual to fill the above position that has arisen within the Mutoko Rural District Council. The incumbent shall be reporting to the Assistant Executive Officer Finance.',
                'responsibilities' => implode("\n", [
                    '• Assist in the preparation of financial statements and reports.',
                    '• Support the monthly and annual financial closing processes.',
                    '• Help in the development and monitoring of departmental budgets.',
                    '• Analyse budget variances and provide insights.',
                    '• Process invoices and payments.',
                    '• Assist in the preparation, monitoring, and reporting of council budgets.',
                    '• Support budget holders with financial forecasting and variance analysis.',
                    '• Help compile financial reports for senior management and council committees.',
                    '• Assist with accounts payable/receivable, payroll, and ledger reconciliations.',
                    '• Process invoices, grants, and payments in line with financial regulations.',
                    '• Support month-end and year-end financial closing procedures.',
                    '• Help maintain strong financial controls to prevent fraud or mismanagement.',
                    '• Prepare financial statements, performance reports, and cost-benefit analyses.',
                    '• Support the production of statutory reports.',
                    '• Help manage council investments, borrowing, and debt strategies.',
                ]),
                'requirements' => implode("\n", [
                    '• 2.1 Bachelor’s degree in Accounting, Banking and Finance or equivalent.',
                    '• Must be at least 25 years of age.',
                    '• Knowledge of Sage Pastel Evolution is an added advantage.',
                    '• Must be computer literate.',
                    '• Clean Class 4 driver’s license will be an added advantage.',
                    '• NB: Applicant must be a resident of Mutoko.',
                ]),
                'opens_at' => Carbon::parse('2025-01-01'),
                'closes_at' => Carbon::parse('2025-05-16'),
                'application_instructions' => "Interested candidates should submit a detailed Curriculum Vitae with at least 3 names of contactable referees and copies of their qualifications addressed to the Chief Executive Officer in a sealed envelope clearly marked the “Post Applied for” or email to recruitment@mutokordc.co.zw. CLOSING DATE: 16 May 2025.\n\nNB: MUTOKO RURAL DISTRICT COUNCIL IS AN EQUAL OPPORTUNITY EMPLOYER, THEREFORE FEMALE CANDIDATES ARE ENCOURAGED TO APPLY FOR THE POST.",
                'display_order' => 1,
            ],
            [
                'slug' => 'graduate-trainee-planning-environment',
                'title' => 'Graduate Trainee - Planning & Environment',
                'grade' => 'Graduate Trainee',
                'reference' => 'MRDC/PLN/GT/2025/01',
                'employment_type' => 'internship',
                'department_id' => $planningDept?->id,
                'description' => 'Applications are invited from suitably qualified, experienced, self-motivated and task-oriented individual to fill the above position that has arisen within the Mutoko Rural District Council. The incumbent shall be reporting to the Executive Officer Town Planning and Environment.',
                'responsibilities' => implode("\n", [
                    '• Carrying out site visits on projects and receiving progress.',
                    '• Overseeing the design aspects of projects and laws out plans Ensuring projects and plans meet statutory requirements.',
                    '• Preparing township layout plans.',
                    '• To process change of use of land/ property.',
                    '• Carrying out development control on all built up areas.',
                    '• Carrying out site planning and pegging for different land uses/developments.',
                    '• Carrying out socio-economic surveys for planning purposes (master and local plans).',
                    '• Recommend sites for topographical surveys.',
                    '• Prepared district development plans.',
                    '• Processing license applications for all licensed businesses/ premises.',
                    '• To manage all pegging and allocation of stands for different uses.',
                    '• Any other duties assigned by the supervisor.',
                ]),
                'requirements' => implode("\n", [
                    '• 2.1 Bachelor\'s degree in Rural and Urban Planning, Surveying and Geometrics or equivalent.',
                    '• Must be at least 25 years of age.',
                    '• Possession of Planning related qualifications or studying towards a relevant Professional qualification will be an added advantage.',
                    '• Previous work experience within Planning or Surveying environment will be an added advantage.',
                    '• Knowledge of Auto CAD will be an added advantage.',
                    '• NB: Applicant must be a resident of Mutoko.',
                ]),
                'opens_at' => Carbon::parse('2025-01-01'),
                'closes_at' => Carbon::parse('2025-05-27'),
                'application_instructions' => "Interested candidates should submit a detailed Curriculum Vitae with at least 3 names of contactable referees and copies of their qualifications addressed to the Chief Executive Officer in a sealed envelope clearly marked the “Post Applied for” or email to recruitment@mutokordc.co.zw. CLOSING DATE: 27 May 2025.\n\nNB: MUTOKO RURAL DISTRICT COUNCIL IS AN EQUAL OPPORTUNITY EMPLOYER, THEREFORE FEMALE CANDIDATES ARE ENCOURAGED TO APPLY FOR THE POST.",
                'display_order' => 2,
            ],
            [
                'slug' => 'building-inspector-grade-8',
                'title' => 'Building Inspector',
                'grade' => 'Grade 8',
                'reference' => 'MRDC/ENG/BI/2025/01',
                'employment_type' => 'full_time',
                'department_id' => $planningDept?->id ?? $engineeringDept?->id,
                'description' => 'Applications are invited from suitably qualified, experienced, self-motivated and task-oriented individual to fill the above position that has arisen within the Mutoko Rural District Council. The incumbent shall be reporting to the Executive Officer Town Planning and Environment.',
                'responsibilities' => implode("\n", [
                    '• Conducting building inspections to ensure that all Civil Engineering Constructions structures are in accordance with the approved plans and designs in compliance with model building by-laws and Civil Engineering statutes.',
                    '• Maintenance of Council Premises in need of brick and block laying.',
                    '• Serve notices and enforcements orders of illegal structures and make follow ups to ensure compliance.',
                    '• Conducting site inspections and feasibility assessments for proposed developments.',
                    '• Any other duties as may be assigned by the Executive Officer Town Planning and Environment.',
                ]),
                'requirements' => implode("\n", [
                    '• National Diploma in Building construction/ Construction/ Engineering/ Brick and Block laying or class 1 builder.',
                    '• Computer literate with experience in Auto CAD.',
                    '• Ability to read construction layouts drawings and specifications.',
                    '• A clean Class 3 driver’s license and Class 4 is an added advantage.',
                    '• At least 25 years old and mature.',
                    '• A Citizen of Zimbabwe.',
                    '• A clean criminal record.',
                    '• A clean record of service within the local government fraternity.',
                ]),
                'opens_at' => Carbon::parse('2025-01-01'),
                'closes_at' => Carbon::parse('2025-03-25'),
                'application_instructions' => "Interested candidates should submit a detailed Curriculum Vitae with at least 3 names of contactable referees and copies of their qualifications addressed to the Chief Executive Officer in a sealed envelope clearly marked the “Post Applied for” or email to recruitment@mutokordc.co.zw. CLOSING DATE: 25 March 2025.\n\nNB: MUTOKO RURAL DISTRICT COUNCIL IS AN EQUAL OPPORTUNITY EMPLOYER, THEREFORE FEMALE CANDIDATES ARE ENCOURAGED TO APPLY FOR THE POST.",
                'display_order' => 3,
            ],
            [
                'slug' => 'executive-officer-human-resources-and-administration-grade-10',
                'title' => 'Executive Officer Human Resources and Administration',
                'grade' => 'Grade 10',
                'reference' => 'MRDC/HR/EO/2025/01',
                'employment_type' => 'full_time',
                'department_id' => $hrDept?->id,
                'description' => 'Applications are invited from suitably qualified, experienced, self-motivated and task-oriented individual to fill the above position that has arisen within the Mutoko Rural District Council. The incumbent shall be reporting to the Chief Executive Officer.',
                'responsibilities' => implode("\n", [
                    '• Recruitment and Selection of Council employees.',
                    '• Payroll management.',
                    '• Planning and implementing training and development programs for employees and councilors.',
                    '• Facilitating the formulation of Council By-laws.',
                    '• Handling labour matters.',
                    '• Development, implementation and review of HR policies.',
                    '• Manage health and safety well-being of employees.',
                    '• Public relations management.',
                    '• Maintain order and employees’ discipline in accordance with the provisions of the Code of conduct.',
                    '• Management of Council assets.',
                    '• Preparing departmental budgets.',
                    '• Managing the preparation and production of Council agendas and minutes of all Council meetings.',
                    '• Talent management and employee development initiatives.',
                    '• Fostering harmonious, mutual relationships. Monitor the use of Council Resources.',
                    '• Any other duties as may be assigned by the Chief Executive Officer.',
                ]),
                'requirements' => implode("\n", [
                    '• A Degree from a recognized university in administration, human resources/ labour relation, local government, law or a social science degree with a recognised human resource or labour relation diploma.',
                    '• Master’s Degree is an added advantage.',
                    '• At least four years post qualification experience in middle management in an administrative position. Local Government experience is an added advantage.',
                    '• A clean Class 4 driver’s license.',
                    '• At least 30 years old and mature.',
                    '• A Citizen of Zimbabwe.',
                    '• A clean criminal record.',
                    '• A clean record of service within the local government fraternity.',
                ]),
                'opens_at' => Carbon::parse('2025-01-01'),
                'closes_at' => Carbon::parse('2025-02-28'),
                'application_instructions' => "Interested candidates should submit a detailed Curriculum Vitae with at least 3 names of contactable referees and copies of their qualifications addressed to the Chief Executive Officer in a sealed envelope clearly marked the “Post Applied for” or email to recruitment@mutokordc.co.zw. CLOSING DATE: 28 February 2025.\n\nNB: MUTOKO RURAL DISTRICT COUNCIL IS AN EQUAL OPPORTUNITY EMPLOYER, THEREFORE FEMALE CANDIDATES ARE ENCOURAGED TO APPLY FOR THE POST.",
                'display_order' => 4,
            ],
            [
                'slug' => 'systems-administrator-grade-8',
                'title' => 'Systems Administrator',
                'grade' => 'Grade 8',
                'reference' => 'MRDC/HR/SA/2025/01',
                'employment_type' => 'full_time',
                'department_id' => $hrDept?->id,
                'description' => 'Applications are invited from suitably qualified, experienced, self-motivated and task-oriented individual to fill the above position that has arisen within the Mutoko Rural District Council. The incumbent shall be reporting to the Executive Officer Human Resources and Administration.',
                'responsibilities' => implode("\n", [
                    '• Managing Council Information Technology systems.',
                    '• Establish and direct the strategic long-term goals, policies and procedures of the IT Section.',
                    '• Provide technical guidance and orientation to Council.',
                    '• Analyze Council\'s IT requirements and organize the resources accordingly.',
                    '• Evaluating Council\'s needs and choosing the most suitable software, hardware and other IT requirements such as networking.',
                    '• Provide troubleshooting solutions.',
                    '• Ensure that all IT requirements of Council are fulfilled.',
                    '• Ensure the smooth functioning of all IT infrastructure such as servers and network connections, besides hardware and software.',
                    '• Ensuring security of the physical and virtual components of Information Technology such as security of the server rooms and installing virus protections and firewalls.',
                    '• Organizing data, storing them securely and creating backups.',
                    '• Any other duty as assigned.',
                ]),
                'requirements' => implode("\n", [
                    '• Bachelor\'s degree from a recognized institution in Computer Science / Information Technology.',
                    '• Professional qualification from a recognized Institution is an added advantage.',
                    '• At least 2 years\' practical experience as a Systems Administrator.',
                    '• Must be 25 years and above.',
                    '• Sound technical knowledge about IT security of systems and latest developments in the field.',
                    '• Excellent management, organization and time management skills.',
                    '• Excellent communication skills.',
                    '• Excellent observation and analytical skills.',
                    '• Strong problem-solving skills.',
                    '• Strong Accounting and Mathematical abilities.',
                    '• A clean class 4 driver\'s licence.',
                    '• Have knowledge in software used by local authorities i.e. SAGE PASTEL and Belina Payroll software.',
                ]),
                'opens_at' => Carbon::parse('2025-01-01'),
                'closes_at' => Carbon::parse('2025-02-28'),
                'application_instructions' => "Interested candidates should submit a detailed Curriculum Vitae with at least 3 names of contactable referees and copies of their qualifications addressed to the Chief Executive Officer in a sealed envelope clearly marked the “Post Applied for” or email to recruitment@mutokordc.co.zw. CLOSING DATE: 28 February 2025.\n\nNB: MUTOKO RURAL DISTRICT COUNCIL IS AN EQUAL OPPORTUNITY EMPLOYER, THEREFORE FEMALE CANDIDATES ARE ENCOURAGED TO APPLY FOR THE POST.",
                'display_order' => 5,
            ],
            [
                'slug' => 'human-resources-officer-grade-9',
                'title' => 'Human Resources Officer',
                'grade' => 'Grade 9',
                'reference' => 'MRDC/HR/HRO/2026/01',
                'employment_type' => 'full_time',
                'department_id' => $hrDept?->id,
                'description' => 'Applications are invited from suitably qualified, experienced, self-motivated and task-oriented individuals to fill the position of Human Resources Officer that has arisen within the Mutoko Rural District Council. The incumbent shall be reporting to the Executive Officer Human Resources and Administration.',
                'responsibilities' => implode("\n", [
                    '• Coordinate recruitment, selection, and onboarding of Council employees.',
                    '• Maintain staff records, leave registers, and establishment schedules.',
                    '• Process payroll inputs and staff benefits administration.',
                    '• Coordinate staff training, development, and performance appraisals.',
                    '• Handle disciplinary procedures in line with the Code of Conduct.',
                    '• Prepare HR reports for management and Council committees.',
                    '• Any other duties as may be assigned by the supervisor.',
                ]),
                'requirements' => implode("\n", [
                    '• Bachelor’s degree in Human Resources Management, Labour Relations, or equivalent.',
                    '• At least 2 years post-qualification experience; Local Government experience is an added advantage.',
                    '• Computer literate with knowledge of Belina Payroll software.',
                    '• A clean Class 4 driver’s license is an added advantage.',
                    '• At least 25 years old and mature.',
                    '• A Citizen of Zimbabwe with a clean criminal record.',
                ]),
                'opens_at' => Carbon::parse('2026-10-10'),
                'closes_at' => Carbon::parse('2026-12-18'),
                'application_instructions' => "Interested candidates should submit a detailed Curriculum Vitae with at least 3 names of contactable referees and copies of their qualifications addressed to the Chief Executive Officer in a sealed envelope clearly marked the “Post Applied for” or email to recruitment@mutokordc.co.zw. CLOSING DATE: 18 December 2026.\n\nNB: MUTOKO RURAL DISTRICT COUNCIL IS AN EQUAL OPPORTUNITY EMPLOYER, THEREFORE FEMALE CANDIDATES ARE ENCOURAGED TO APPLY FOR THE POST.",
                'display_order' => 6,
            ],
            [
                'slug' => 'technician-water-and-sanitation',
                'title' => 'Technician - Water and Sanitation',
                'grade' => 'Grade 7',
                'reference' => 'MRDC/ENG/TECH/2026/01',
                'employment_type' => 'full_time',
                'department_id' => $engineeringDept?->id,
                'description' => 'Applications are invited from suitably qualified, experienced, self-motivated and task-oriented individuals to fill the position of Technician (Water and Sanitation) that has arisen within the Mutoko Rural District Council. The incumbent shall be reporting to the Executive Officer Engineering and Works.',
                'responsibilities' => implode("\n", [
                    '• Install, maintain, and repair water supply and sanitation infrastructure.',
                    '• Conduct routine inspections of boreholes, pumps, and reticulation systems.',
                    '• Respond to burst pipes, blockages, and breakdowns timeously.',
                    '• Keep maintenance logs and report on spares and materials required.',
                    '• Any other duties as may be assigned by the supervisor.',
                ]),
                'requirements' => implode("\n", [
                    '• National Diploma in Water Engineering, Plumbing, or equivalent; Class 1 trade certificate is an added advantage.',
                    '• At least 2 years relevant practical experience.',
                    '• Ability to read engineering drawings and specifications.',
                    '• A clean Class 3 driver’s license is an added advantage.',
                    '• At least 25 years old and mature.',
                    '• A Citizen of Zimbabwe with a clean criminal record.',
                ]),
                'opens_at' => Carbon::parse('2026-10-10'),
                'closes_at' => Carbon::parse('2027-01-30'),
                'application_instructions' => "Interested candidates should submit a detailed Curriculum Vitae with at least 3 names of contactable referees and copies of their qualifications addressed to the Chief Executive Officer in a sealed envelope clearly marked the “Post Applied for” or email to recruitment@mutokordc.co.zw. CLOSING DATE: 30 January 2027.\n\nNB: MUTOKO RURAL DISTRICT COUNCIL IS AN EQUAL OPPORTUNITY EMPLOYER, THEREFORE FEMALE CANDIDATES ARE ENCOURAGED TO APPLY FOR THE POST.",
                'display_order' => 7,
            ],
        ];

        foreach ($vacancies as $item) {
            Vacancy::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    ...$item,
                    'status' => 'published',
                    'verification_status' => 'publishable',
                    'published_at' => now(),
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                    'verified_by' => $adminId,
                    'verified_at' => now(),
                ]
            );
        }
    }
}
