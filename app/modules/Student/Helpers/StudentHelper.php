<?php

namespace App\Modules\Student\Helpers;

use Carbon\Carbon;

class StudentHelper
{

    public function formatStudents(array $students): array
    {
        $formatted = [];

        foreach ($students as $student) {
            $formatted[] = [
                'name' => ucwords(strtolower($student['name'])),
                'email' => strtolower($student['email']),
                'age' => (int) $student['age'],
                'course' => strtoupper($student['course']),
                'created_date' => date('d-M-y h:i A', strtotime($student['created_date'])),
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
                'Created Date'      => date('d-M-y h:i A', strtotime($student['created_date'])),
            ];
        }

        return $exportData;
    }

    public function parseDate($dateString)
    {
        if (empty($dateString)) {
            return now()->format('Y-m-d h:i:s');
        }

        $formats = [
            'Y-m-d H:i:s',
            'Y-m-d H:i',
            'd-m-Y H:i:s',
            'd-m-Y H:i',
            'Y-m-d',
            'd-m-Y',
            'm-d-Y',
            'Y/m/d',
            'd/m/Y',
            'm/d/Y',
        ];

        foreach ($formats as $format) {
            try {
                $date = Carbon::createFromFormat($format, $dateString);

                if (strlen($dateString) <= 10) {
                    $date->setTime(0, 0, 0);
                } elseif (strlen($dateString) <= 16) {
                    $date->setSeconds(0);
                }

                return $date->format('Y-m-d h:i:s');
            } catch (\Exception $e) {
                continue;
            }
        }

        return now()->format('Y-m-d h:i:s');
    }
}
