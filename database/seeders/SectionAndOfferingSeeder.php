<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SectionAndOfferingSeeder extends Seeder
{
    public function run(): void
    {
        $bscsId       = DB::table('programs')->where('code', 'BSCS')->value('id');
        $thirdYearId  = DB::table('year_levels')->where('label', '3rd Year')->value('id');
        $facultyRole  = DB::table('roles')->where('code', 'faculty')->value('id');
        $activeStatus = DB::table('profile_statuses')->where('code', 'active')->value('id');

        $sem1Id = DB::table('semesters')->where('label', '1st Semester')->value('id');
        $sem2Id = DB::table('semesters')->where('label', '2nd Semester')->value('id');
        $sumId  = DB::table('semesters')->where('label', 'Summer')->value('id');

        // ── Seed faculty ──────────────────────────────────────────
        $facultyData = [
            ['full_name' => 'Ken Dela Cruz',   'email' => 'ken.delacruz@pup.edu.ph'],
            ['full_name' => 'Von Santos',       'email' => 'von.santos@pup.edu.ph'],
            ['full_name' => 'Florence Reyes',   'email' => 'florence.reyes@pup.edu.ph'],
            ['full_name' => 'Gabriel Mendoza',  'email' => 'gabriel.mendoza@pup.edu.ph'],
            ['full_name' => 'Maria Lim',        'email' => 'maria.lim@pup.edu.ph'],
            ['full_name' => 'Jose Bautista',    'email' => 'jose.bautista@pup.edu.ph'],
            ['full_name' => 'Ana Ramos',        'email' => 'ana.ramos@pup.edu.ph'],
        ];

        $facultyIds = [];
        foreach ($facultyData as $f) {
            $existing = DB::table('profiles')->where('email', $f['email'])->value('id');
            if ($existing) {
                $facultyIds[] = $existing;
            } else {
                $id = (string) Str::uuid();
                DB::table('profiles')->insert([
                    'id'         => $id,
                    'role_id'    => $facultyRole,
                    'status_id'  => $activeStatus,
                    'full_name'  => $f['full_name'],
                    'email'      => $f['email'],
                    'password'   => Hash::make('faculty123'),
                    'created_at' => now(),
                ]);
                $facultyIds[] = $id;
            }
        }

        // ── All possible schedule slots (day combo → times) ───────
        // Each entry: [day_label, time, [days_used]]
        $allSlots = [
            ['MWF', '7:30-9:00 AM',        ['Monday', 'Wednesday', 'Friday']],
            ['TTh', '7:30-9:30 AM',         ['Tuesday', 'Thursday']],
            ['MWF', '9:00-10:30 AM',        ['Monday', 'Wednesday', 'Friday']],
            ['TTh', '9:30-11:30 AM',        ['Tuesday', 'Thursday']],
            ['MWF', '10:30 AM-12:00 PM',    ['Monday', 'Wednesday', 'Friday']],
            ['TTh', '11:30 AM-1:30 PM',     ['Tuesday', 'Thursday']],
            ['MWF', '1:00-2:30 PM',         ['Monday', 'Wednesday', 'Friday']],
            ['TTh', '1:30-3:30 PM',         ['Tuesday', 'Thursday']],
            ['MWF', '2:30-4:00 PM',         ['Monday', 'Wednesday', 'Friday']],
            ['TTh', '3:30-5:30 PM',         ['Tuesday', 'Thursday']],
            ['MWF', '4:00-5:30 PM',         ['Monday', 'Wednesday', 'Friday']],
            ['TTh', '5:30-7:30 PM',         ['Tuesday', 'Thursday']],
            ['MWF', '5:30-7:00 PM',         ['Monday', 'Wednesday', 'Friday']],
            ['MWF', '7:00-8:30 PM',         ['Monday', 'Wednesday', 'Friday']],
            ['TTh', '7:30-9:30 PM',         ['Tuesday', 'Thursday']],
            // Single day slots as fallback
            ['Mon', '7:30-10:30 AM',        ['Monday']],
            ['Tue', '7:30-10:30 AM',        ['Tuesday']],
            ['Wed', '7:30-10:30 AM',        ['Wednesday']],
            ['Thu', '7:30-10:30 AM',        ['Thursday']],
            ['Fri', '7:30-10:30 AM',        ['Friday']],
            ['Mon', '1:00-4:00 PM',         ['Monday']],
            ['Tue', '1:00-4:00 PM',         ['Tuesday']],
            ['Wed', '1:00-4:00 PM',         ['Wednesday']],
            ['Thu', '1:00-4:00 PM',         ['Thursday']],
            ['Fri', '1:00-4:00 PM',         ['Friday']],
            ['Mon', '4:30-7:30 PM',         ['Monday']],
            ['Tue', '4:30-7:30 PM',         ['Tuesday']],
            ['Wed', '4:30-7:30 PM',         ['Wednesday']],
            ['Thu', '4:30-7:30 PM',         ['Thursday']],
            ['Fri', '4:30-7:30 PM',         ['Friday']],
            ['Sat', '7:30-10:30 AM',        ['Saturday']],
            ['Sat', '10:30 AM-1:30 PM',     ['Saturday']],
            ['Sat', '1:30-4:30 PM',         ['Saturday']],
            ['Sat', '4:30-7:30 PM',         ['Saturday']],
        ];

        // Vacant day pairs per section (max 2 vacant days Mon-Sat)
        $vacantDayPairs = [
            ['Wednesday', 'Saturday'],  // 3-1
            ['Tuesday',   'Friday'],    // 3-1N
            ['Monday',    'Thursday'],  // 3-2
            ['Wednesday', 'Friday'],    // 3-3
            ['Tuesday',   'Saturday'],  // 3-4
            ['Monday',    'Saturday'],  // 3-5
        ];

        $labs = ['S501', 'S502', 'S503', 'S504', 'S505', 'S506', 'S507', 'S508', 'S509', 'S510'];

        // 3rd Year subjects per semester
        $subjectsBySem = [
            $sem1Id => ['COMP 019', 'COSC 302', 'ELEC CS-E1', 'COSC 301', 'COMP 015', 'COMP 013', 'COSC 303'],
            $sem2Id => ['GEED 006', 'ELEC CS-E2', 'COSC 305', 'COMP 020', 'COSC 304', 'COMP 021', 'COMP 016'],
            $sumId  => ['COSC 306'],
        ];

        $sectionNames = ['BSCS 3-1', 'BSCS 3-1N', 'BSCS 3-2', 'BSCS 3-3', 'BSCS 3-4', 'BSCS 3-5'];

        foreach ([$sem1Id, $sem2Id, $sumId] as $semId) {
            $subjectCodes = $subjectsBySem[$semId];

            foreach ($sectionNames as $sectionIndex => $sectionName) {
                // Create section if not exists
                $sectionId = DB::table('sections')
                    ->where('name', $sectionName)
                    ->where('semester_id', $semId)
                    ->value('id');

                if (!$sectionId) {
                    $sectionId = (string) Str::uuid();
                    DB::table('sections')->insert([
                        'id'            => $sectionId,
                        'semester_id'   => $semId,
                        'program_id'    => $bscsId,
                        'year_level_id' => $thirdYearId,
                        'adviser_id'    => $facultyIds[$sectionIndex % count($facultyIds)],
                        'name'          => $sectionName,
                        'max_capacity'  => 40,
                    ]);
                }

                $vacantDays       = $vacantDayPairs[$sectionIndex];
                $saturdayIsVacant = in_array('Saturday', $vacantDays);
                $isNightSection   = str_contains($sectionName, '3-1N');

                // Filter slots: remove any that use a vacant day
                $availableSlots = array_values(array_filter($allSlots, function ($slot) use ($vacantDays, $isNightSection) {
                    $daysUsed = $slot[2];
                    foreach ($daysUsed as $day) {
                        if (in_array($day, $vacantDays)) return false;
                    }
                    // Night section: only keep PM slots after 4pm
                    if ($isNightSection) {
                        $time = $slot[1];
                        $startsAfter4 = preg_match('/^[4-9]:\d{2}.*PM|^[1-9][0-9]/', $time);
                        if (!$startsAfter4) return false;
                    }
                    return true;
                }));

                // Safety fallback — if somehow still empty, use all single-day slots
                if (empty($availableSlots)) {
                    $availableSlots = array_values(array_filter($allSlots, fn($s) => count($s[2]) === 1));
                }

                $slotOffset = $sectionIndex * 2;

                foreach ($subjectCodes as $subjectIndex => $code) {
                    $subjectId = DB::table('subjects')->where('code', $code)->value('id');
                    if (!$subjectId) continue;

                    $exists = DB::table('subject_offerings')
                        ->where('subject_id', $subjectId)
                        ->where('section_id', $sectionId)
                        ->exists();
                    if ($exists) continue;

                    $isNstp = str_starts_with($code, 'NSTP');
                    $isPe   = str_starts_with($code, 'PATHFIT');

                    if ($isNstp) {
                        $schedule = 'Sun 7:30-10:30 AM';
                        $room     = 'Covered Court';
                    } elseif ($isPe) {
                        if ($saturdayIsVacant) {
                            $schedule = 'Fri 4:00-6:00 PM';
                        } else {
                            $peSlots  = ['7:30-10:30 AM', '10:30 AM-1:30 PM', '1:30-4:30 PM', '4:30-7:30 PM'];
                            $schedule = 'Sat ' . $peSlots[$subjectIndex % count($peSlots)];
                        }
                        $room = 'Gymnasium';
                    } else {
                        $slot     = $availableSlots[($slotOffset + $subjectIndex) % count($availableSlots)];
                        $schedule = $slot[0] . ' ' . $slot[1];
                        $room     = $labs[($sectionIndex + $subjectIndex) % count($labs)];
                    }

                    DB::table('subject_offerings')->insert([
                        'id'         => (string) Str::uuid(),
                        'subject_id' => $subjectId,
                        'section_id' => $sectionId,
                        'faculty_id' => $facultyIds[($sectionIndex + $subjectIndex) % count($facultyIds)],
                        'room'       => $room,
                        'schedule'   => $schedule,
                    ]);
                }
            }
        }
    }
}