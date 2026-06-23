<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CollegeAndProgramSeeder extends Seeder
{
    public function run(): void
    {
        $colleges = [
            [
                'code' => 'CAF',
                'name' => 'College of Accountancy and Finance',
                'programs' => [
                    ['code' => 'BSA',    'name' => 'Bachelor of Science in Accountancy',                                              'major' => null],
                    ['code' => 'BSMA',   'name' => 'Bachelor of Science in Management Accounting',                                    'major' => null],
                    ['code' => 'BSBAFM', 'name' => 'Bachelor of Science in Business Administration',                                  'major' => 'Financial Management'],
                ],
            ],
            [
                'code' => 'CADBE',
                'name' => 'College of Architecture, Design and the Built Environment',
                'programs' => [
                    ['code' => 'BSARCH', 'name' => 'Bachelor of Science in Architecture',         'major' => null],
                    ['code' => 'BSID',   'name' => 'Bachelor of Science in Interior Design',      'major' => null],
                    ['code' => 'BSEP',   'name' => 'Bachelor of Science in Environmental Planning','major' => null],
                ],
            ],
            [
                'code' => 'CAL',
                'name' => 'College of Arts and Letters',
                'programs' => [
                    ['code' => 'ABELS', 'name' => 'Bachelor of Arts in English Language Studies',    'major' => null],
                    ['code' => 'ABF',   'name' => 'Bachelor of Arts in Filipinology',                'major' => null],
                    ['code' => 'ABLCS', 'name' => 'Bachelor of Arts in Literary and Cultural Studies','major' => null],
                    ['code' => 'ABPHILO','name' => 'Bachelor of Arts in Philosophy',                 'major' => null],
                    ['code' => 'BPEA',  'name' => 'Bachelor of Performing Arts',                     'major' => 'Theater Arts'],
                ],
            ],
            [
                'code' => 'CBA',
                'name' => 'College of Business Administration',
                'programs' => [
                    ['code' => 'BSBAHRM',  'name' => 'Bachelor of Science in Business Administration', 'major' => 'Human Resource Management'],
                    ['code' => 'BSBAMM',   'name' => 'Bachelor of Science in Business Administration', 'major' => 'Marketing Management'],
                    ['code' => 'BSENTREP', 'name' => 'Bachelor of Science in Entrepreneurship',        'major' => null],
                    ['code' => 'BSOA',     'name' => 'Bachelor of Science in Office Administration',   'major' => null],
                ],
            ],
            [
                'code' => 'COC',
                'name' => 'College of Communication',
                'programs' => [
                    ['code' => 'BADPR',      'name' => 'Bachelor in Advertising and Public Relations', 'major' => null],
                    ['code' => 'BABROADCAST','name' => 'Bachelor of Arts in Broadcasting',             'major' => null],
                    ['code' => 'BACR',       'name' => 'Bachelor of Arts in Communication Research',  'major' => null],
                    ['code' => 'BAJ',        'name' => 'Bachelor of Arts in Journalism',              'major' => null],
                ],
            ],
            [
                'code' => 'CCIS',
                'name' => 'College of Computer and Information Sciences',
                'programs' => [
                    ['code' => 'BSCS', 'name' => 'Bachelor of Science in Computer Science',       'major' => null],
                    ['code' => 'BSIT', 'name' => 'Bachelor of Science in Information Technology', 'major' => null],
                ],
            ],
            [
                'code' => 'COED',
                'name' => 'College of Education',
                'programs' => [
                    ['code' => 'BTLEdHE',  'name' => 'Bachelor of Technology and Livelihood Education', 'major' => 'Home Economics'],
                    ['code' => 'BTLEdIA',  'name' => 'Bachelor of Technology and Livelihood Education', 'major' => 'Industrial Arts'],
                    ['code' => 'BTLEdICT', 'name' => 'Bachelor of Technology and Livelihood Education', 'major' => 'Information and Communication Technology'],
                    ['code' => 'BLIS',     'name' => 'Bachelor of Library and Information Science',     'major' => null],
                    ['code' => 'BSEdENG',  'name' => 'Bachelor of Secondary Education',                 'major' => 'English'],
                    ['code' => 'BSEdMATH', 'name' => 'Bachelor of Secondary Education',                 'major' => 'Mathematics'],
                    ['code' => 'BSEdSCI',  'name' => 'Bachelor of Secondary Education',                 'major' => 'Science'],
                    ['code' => 'BSEdFIL',  'name' => 'Bachelor of Secondary Education',                 'major' => 'Filipino'],
                    ['code' => 'BSEdSOS',  'name' => 'Bachelor of Secondary Education',                 'major' => 'Social Studies'],
                    ['code' => 'BEEd',     'name' => 'Bachelor of Elementary Education',                'major' => null],
                    ['code' => 'BECEd',    'name' => 'Bachelor of Early Childhood Education',           'major' => null],
                ],
            ],
            [
                'code' => 'CE',
                'name' => 'College of Engineering',
                'programs' => [
                    ['code' => 'BSCE',  'name' => 'Bachelor of Science in Civil Engineering',        'major' => null],
                    ['code' => 'BSCpE', 'name' => 'Bachelor of Science in Computer Engineering',     'major' => null],
                    ['code' => 'BSEE',  'name' => 'Bachelor of Science in Electrical Engineering',   'major' => null],
                    ['code' => 'BSECE', 'name' => 'Bachelor of Science in Electronics Engineering',  'major' => null],
                    ['code' => 'BSIE',  'name' => 'Bachelor of Science in Industrial Engineering',   'major' => null],
                    ['code' => 'BSME',  'name' => 'Bachelor of Science in Mechanical Engineering',   'major' => null],
                    ['code' => 'BSRE',  'name' => 'Bachelor of Science in Railway Engineering',      'major' => null],
                ],
            ],
            [
                'code' => 'CHK',
                'name' => 'College of Human Kinetics',
                'programs' => [
                    ['code' => 'BPE',   'name' => 'Bachelor of Physical Education',             'major' => null],
                    ['code' => 'BSESS', 'name' => 'Bachelor of Science in Exercises and Sports','major' => null],
                ],
            ],
            [
                'code' => 'CL',
                'name' => 'College of Law',
                'programs' => [
                    ['code' => 'JD', 'name' => 'Juris Doctor', 'major' => null],
                ],
            ],
            [
                'code' => 'CPSPA',
                'name' => 'College of Political Science and Public Administration',
                'programs' => [
                    ['code' => 'BPA',  'name' => 'Bachelor of Public Administration',        'major' => null],
                    ['code' => 'BAIS', 'name' => 'Bachelor of Arts in International Studies','major' => null],
                    ['code' => 'BAPE', 'name' => 'Bachelor of Arts in Political Economy',   'major' => null],
                    ['code' => 'BAPS', 'name' => 'Bachelor of Arts in Political Science',   'major' => null],
                ],
            ],
            [
                'code' => 'CSSD',
                'name' => 'College of Social Sciences and Development',
                'programs' => [
                    ['code' => 'BAH',   'name' => 'Bachelor of Arts in History',    'major' => null],
                    ['code' => 'BAS',   'name' => 'Bachelor of Arts in Sociology',  'major' => null],
                    ['code' => 'BSC',   'name' => 'Bachelor of Science in Cooperatives', 'major' => null],
                    ['code' => 'BSE',   'name' => 'Bachelor of Science in Economics',    'major' => null],
                    ['code' => 'BSPSY', 'name' => 'Bachelor of Science in Psychology',  'major' => null],
                ],
            ],
            [
                'code' => 'CS',
                'name' => 'College of Science',
                'programs' => [
                    ['code' => 'BSFT',     'name' => 'Bachelor of Science in Food Technology',        'major' => null],
                    ['code' => 'BSAPMATH', 'name' => 'Bachelor of Science in Applied Mathematics',    'major' => null],
                    ['code' => 'BSBIO',    'name' => 'Bachelor of Science in Biology',                'major' => null],
                    ['code' => 'BSCHEM',   'name' => 'Bachelor of Science in Chemistry',              'major' => null],
                    ['code' => 'BSMATH',   'name' => 'Bachelor of Science in Mathematics',            'major' => null],
                    ['code' => 'BSND',     'name' => 'Bachelor of Science in Nutrition and Dietetics','major' => null],
                    ['code' => 'BSPHY',    'name' => 'Bachelor of Science in Physics',                'major' => null],
                    ['code' => 'BSSTAT',   'name' => 'Bachelor of Science in Statistics',             'major' => null],
                ],
            ],
            [
                'code' => 'CTHTM',
                'name' => 'College of Tourism, Hospitality and Transportation Management',
                'programs' => [
                    ['code' => 'BSHM',  'name' => 'Bachelor of Science in Hospitality Management',    'major' => null],
                    ['code' => 'BSTM',  'name' => 'Bachelor of Science in Tourism Management',        'major' => null],
                    ['code' => 'BSTRM', 'name' => 'Bachelor of Science in Transportation Management', 'major' => null],
                ],
            ],
        ];

        foreach ($colleges as $college) {
            $programs = $college['programs'];
            $collegeId = (string) Str::uuid();

            DB::table('colleges')->insertOrIgnore([
                'id'   => $collegeId,
                'code' => $college['code'],
                'name' => $college['name'],
            ]);

            $collegeId = DB::table('colleges')
                ->where('code', $college['code'])
                ->value('id');

            foreach ($programs as $program) {
                DB::table('programs')->insertOrIgnore([
                    'id'         => (string) Str::uuid(),
                    'college_id' => $collegeId,
                    'code'       => $program['code'],
                    'name'       => $program['name'],
                    'major'      => $program['major'],
                ]);
            }
        }
    }
}
