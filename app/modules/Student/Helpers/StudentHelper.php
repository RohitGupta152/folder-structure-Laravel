<?php

namespace App\Modules\Student\Helpers;

class StudentHelper
{

    public function formatStudents(array $students): array
    {
        // dd($students);
        $formatted = [];

        foreach ($students as $student) {
            $formatted[] = [
                'name' => ucwords(strtolower($student['name'])),
                'email' => strtolower($student['email']),
                'age' => (int) $student['age'],
                'course' => strtoupper($student['course']),
                'created_date' => date('d-M-y h:i A', strtotime($student['created_date'])),
                // 'updated_date' => date('d-M-y h:i A', strtotime($student['updated_date'])),
            ];
        }

        return $formatted;
    }

    public function formatStudentsForExport(array $students): array
    {
        $exportData = [];

        foreach ($students as $student) {
            $exportData[] = [
                'Student Name'    => ucwords(strtolower($student['name'])),
                'Student Email'   => strtolower($student['email']),
                'Age (Years)'     => (int) $student['age'],
                'Enrolled Course' => strtoupper($student['course']),
                'Created On'      => date('d-M-y h:i A', strtotime($student['created_date'])),
                // 'Updated On'      => date('d-M-y h:i A', strtotime($student['updated_date'] ?? now())),
            ];
        }

        return $exportData;
    }
}
