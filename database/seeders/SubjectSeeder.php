<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $bscsId = DB::table('programs')->where('code', 'BSCS')->value('id');

        $subjects = [
            // ── 1st Year, 1st Semester ────────────────────────────
            ['code' => 'COMP 001',     'title' => 'Introduction to Computing',                          'units' => 3, 'type' => 'lecture'],
            ['code' => 'COMP 002',     'title' => 'Computer Programming 1',                             'units' => 3, 'type' => 'lecture'],
            ['code' => 'GEED 004',     'title' => 'Mathematics in the Modern World',                    'units' => 3, 'type' => 'GE'],
            ['code' => 'GEED 005',     'title' => 'Purposive Communication',                            'units' => 3, 'type' => 'GE'],
            ['code' => 'GEED 020',     'title' => 'Politics, Governance and Citizenship',               'units' => 3, 'type' => 'GE'],
            ['code' => 'GEED 032',     'title' => 'Filipinolohiya at Pambansang Kaunlaran',             'units' => 3, 'type' => 'GE'],
            ['code' => 'NSTP 001',     'title' => 'National Service Training Program 1',                'units' => 3, 'type' => 'NSTP'],
            ['code' => 'PATHFIT 1',    'title' => 'Physical Activity Towards Health and Fitness 1',     'units' => 2, 'type' => 'PE'],

            // ── 1st Year, 2nd Semester ────────────────────────────
            ['code' => 'COMP 003',     'title' => 'Computer Programming 2',                             'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 002'],
            ['code' => 'COMP 004',     'title' => 'Discrete Structures 1',                              'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'GEED 004'],
            ['code' => 'GEED 001',     'title' => 'Understanding the Self',                             'units' => 3, 'type' => 'GE'],
            ['code' => 'GEED 007',     'title' => 'Science, Technology and Society',                    'units' => 3, 'type' => 'GE'],
            ['code' => 'GEED 033',     'title' => 'Pagsasalin sa Kontekstong Filipino',                 'units' => 3, 'type' => 'GE', 'prerequisite_codes' => 'GEED 032'],
            ['code' => 'MATH 017',     'title' => 'Differential Calculus for Computer Science Students','units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'GEED 004'],
            ['code' => 'NSTP 002',     'title' => 'National Service Training Program 2',                'units' => 3, 'type' => 'NSTP'],
            ['code' => 'PATHFIT 2',    'title' => 'Physical Activity Towards Health and Fitness 2',     'units' => 2, 'type' => 'PE', 'prerequisite_codes' => 'PATHFIT 1'],

            // ── 2nd Year, 1st Semester ────────────────────────────
            ['code' => 'COSC 201',     'title' => 'Logic Design and Digital Computer Circuits',         'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 001'],
            ['code' => 'COSC 202',     'title' => 'Modeling and Simulation',                            'units' => 3, 'type' => 'lecture'],
            ['code' => 'COMP 005',     'title' => 'Discrete Structures 2',                              'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 004'],
            ['code' => 'COMP 006',     'title' => 'Data Structures and Algorithms',                     'units' => 3, 'type' => 'lecture'],
            ['code' => 'COMP 009',     'title' => 'Object Oriented Programming',                        'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 003'],
            ['code' => 'ELEC CS-FE1',  'title' => 'BSCS Free Elective 1',                              'units' => 3, 'type' => 'lecture'],
            ['code' => 'GEED 008',     'title' => 'Ethics/Etika',                                       'units' => 3, 'type' => 'GE'],
            ['code' => 'PATHFIT 3',    'title' => 'Physical Activity Towards Health and Fitness 3',     'units' => 2, 'type' => 'PE', 'prerequisite_codes' => 'PATHFIT 2'],

            // ── 2nd Year, 2nd Semester ────────────────────────────
            ['code' => 'COSC 203',     'title' => 'Design and Analysis of Algorithms',                  'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 006'],
            ['code' => 'COMP 007',     'title' => 'Operating Systems',                                  'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 001'],
            ['code' => 'COMP 008',     'title' => 'Data Communications and Networking',                 'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COSC 201'],
            ['code' => 'COMP 010',     'title' => 'Information Management',                             'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 006'],
            ['code' => 'COMP 011',     'title' => 'Technical Documentation and Presentation Skills in ICT', 'units' => 3, 'type' => 'lecture'],
            ['code' => 'ELEC CS-FE2',  'title' => 'BSCS Free Elective 2',                              'units' => 3, 'type' => 'lecture'],
            ['code' => 'GEED 010',     'title' => 'People and the Earth\'s Ecosystems',                 'units' => 3, 'type' => 'GE'],
            ['code' => 'PATHFIT 4',    'title' => 'Physical Activity Towards Health and Fitness 4',     'units' => 2, 'type' => 'PE', 'prerequisite_codes' => 'PATHFIT 3'],

            // ── 3rd Year, 1st Semester ────────────────────────────
            ['code' => 'COSC 301',     'title' => 'Computer Organization and Assembly Language',        'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 002,COSC 201'],
            ['code' => 'COSC 302',     'title' => 'Automata and Language Theory',                       'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 006'],
            ['code' => 'COSC 303',     'title' => 'Principles of Programming Languages',                'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 006'],
            ['code' => 'COMP 013',     'title' => 'Human Computer Interaction',                         'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 002'],
            ['code' => 'COMP 015',     'title' => 'Fundamentals of Research',                           'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 011'],
            ['code' => 'COMP 019',     'title' => 'Applications Development and Emerging Technologies', 'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 009'],
            ['code' => 'ELEC CS-E1',   'title' => 'BSCS Elective 1',                                   'units' => 3, 'type' => 'lecture'],

            // ── 3rd Year, 2nd Semester ────────────────────────────
            ['code' => 'COSC 304',     'title' => 'Introduction to Artificial Intelligence',            'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COSC 302'],
            ['code' => 'COSC 305',     'title' => 'CS Thesis Writing 1',                                'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 015'],
            ['code' => 'COMP 016',     'title' => 'Web Development',                                    'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 009,COMP 010'],
            ['code' => 'COMP 020',     'title' => 'Information Assurance and Security',                 'units' => 3, 'type' => 'lecture'],
            ['code' => 'COMP 021',     'title' => 'Software Engineering 1',                             'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 009,COMP 010'],
            ['code' => 'ELEC CS-E2',   'title' => 'BSCS Elective 2',                                   'units' => 3, 'type' => 'lecture'],
            ['code' => 'GEED 006',     'title' => 'Art Appreciation',                                   'units' => 3, 'type' => 'GE'],

            // ── 3rd Year, Summer ──────────────────────────────────
            ['code' => 'COSC 306',     'title' => 'Practicum (200 hours)',                              'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 008,COMP 009,COMP 010,COMP 021'],

            // ── 4th Year, 1st Semester ────────────────────────────
            ['code' => 'COSC 401',     'title' => 'CS Thesis Writing 2',                                'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COSC 305'],
            ['code' => 'COMP 022',     'title' => 'Software Engineering 2',                             'units' => 3, 'type' => 'lecture', 'prerequisite_codes' => 'COMP 021'],
            ['code' => 'ELEC CS-E3',   'title' => 'BSCS Elective 3',                                   'units' => 3, 'type' => 'lecture'],
            ['code' => 'GEED 002',     'title' => 'Readings in Philippine History',                     'units' => 3, 'type' => 'GE'],
            ['code' => 'GEED 003',     'title' => 'The Contemporary World',                             'units' => 3, 'type' => 'GE'],
            ['code' => 'GEED 026',     'title' => 'Philippine Popular Culture',                         'units' => 3, 'type' => 'GE'],

            // ── 4th Year, 2nd Semester ────────────────────────────
            ['code' => 'COSC 402',     'title' => 'Current Trends and Topics in Computing',             'units' => 3, 'type' => 'lecture'],
            ['code' => 'COMP 023',     'title' => 'Social and Professional Issues in Computing',        'units' => 3, 'type' => 'lecture'],
            ['code' => 'ELEC CS-E4',   'title' => 'BSCS Elective 4',                                   'units' => 3, 'type' => 'lecture'],
            ['code' => 'GEED 037',     'title' => 'Life and Works of Rizal',                            'units' => 3, 'type' => 'GE'],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['code' => $subject['code']],
                [
                    'id'                 => DB::table('subjects')->where('code', $subject['code'])->value('id') ?? (string) Str::uuid(),
                    'program_id'         => $bscsId,
                    'title'              => $subject['title'],
                    'units'              => $subject['units'],
                    'type'               => $subject['type'],
                    'prerequisite_codes' => $subject['prerequisite_codes'] ?? null,
                ]
            );
        }
    }
}
