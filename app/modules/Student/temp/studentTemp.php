// public function importStudent(Request $request)
    // {
    //     try {
    //         if (!$request->hasFile('file')) {
    //             return response()->json(['status' => 'error', 'message' => 'No file uploaded'], 400);
    //         }

    //         $file = $request->file('file');

    //         if ($file->getClientOriginalExtension() !== 'csv') {
    //             return response()->json(['status' => 'error', 'message' => 'Only CSV files allowed'], 400);
    //         }

    //         $path = $file->getRealPath();
    //         $handle = fopen($path, 'r');

    //         $header = fgetcsv($handle); // Skip header row

    //         while (($data = fgetcsv($handle)) !== false) {
    //             Student::create([
    //                 'name' => $data[0],
    //                 'email' => $data[1],
    //                 'age' => $data[2],
    //                 'course' => $data[3],
    //             ]);
    //         }

    //         fclose($handle);

    //         return response()->json(['status' => 'success', 'message' => 'CSV imported successfully']);
    //     } catch (\Exception $e) {
    //         Log::error('CSV Import Error: ' . $e->getMessage());
    //         return response()->json(['status' => 'error', 'message' => 'Import failed: ' . $e->getMessage()], 500);
    //     }
    // }


    // public function importStudent(StudentImportRequest $request)
    // {
    //     try {
    //         $file = $request->file('file');
    //         $fileContents = file($file->getPathname());
    //         array_shift($fileContents);

    //         $importCount = 0;
    //         $errorCount = 0;
    //         $errors = [];

    //         foreach ($fileContents as $index => $line) {
    //             // $data = str_getcsv($line);
    //             $data = explode(',', $line);
    //             // dd($data);
    //             if (count($data) >= 6) {
    //                 try {
    //                     $createdDate = $this->parseDate(trim($data[4]));
    //                     $updatedDate = $this->parseDate(trim($data[5]));

    //                     $student = Student::create([
    //                         'name' => trim($data[0]),
    //                         'email' => trim($data[1]),
    //                         'age' => (int)trim($data[2]),
    //                         'course' => trim($data[3]),
    //                         'created_date' => $createdDate,
    //                         'updated_date' => $updatedDate,
    //                     ]);

    //                     $importCount++;
    //                 } catch (\Exception $e) {
    //                     $errorCount++;
    //                     $errors[] = [
    //                         'row' => $index + 2, // +2 because of header and zero-indexing
    //                         'error' => $e->getMessage()
    //                     ];
    //                 }
    //             } else {
    //                 $errorCount++;
    //                 $errors[] = [
    //                     'row' => $index + 2,
    //                     'error' => 'Insufficient data columns'
    //                 ];
    //             }
    //         }

    //         $response = [
    //             'status' => 'success',
    //             'total_rows' => count($fileContents),
    //             'imported_count' => $importCount,
    //             'error_count' => $errorCount,
    //         ];

    //         if (!empty($errors)) {
    //             $response['errors'] = $errors;
    //         }

    //         return response()->json($response);
    //     } catch (\Exception $e) {
    //         Log::error('CSV Import Error: ' . $e->getMessage());

    //         return response()->json([
    //             'status' => 'error',
    //             'message' => 'Import failed: ' . $e->getMessage()
    //         ], 500);
    //     }
    // }

    // private function parseDate($dateString)
    // {
    //     if (empty($dateString)) {
    //         return now()->format('Y-m-d h:i:s');
    //     }
    
    //     $formats = [
    //         'Y-m-d H:i:s',
    //         'Y-m-d H:i',
    //         'Y-m-d',
    //         'd-m-Y',
    //         'm-d-Y',
    //         'Y/m/d',
    //         'd/m/Y',
    //         'm/d/Y',
    //     ];
    
    //     foreach ($formats as $format) {
    //         try {
    //             $date = \Carbon\Carbon::createFromFormat($format, $dateString);
    
    //             // If only date or date+hour+minute is provided, set time parts
    //             if (strlen($dateString) <= 10) {
    //                 $date->setTime(00, 00, 00);
    //             } elseif (strlen($dateString) <= 16) {
    //                 $date->setSeconds(00);
    //             }
    
    //             return $date->format('Y-m-d h:i:s');
    //         } catch (\Exception $e) {
    //             continue;
    //         }
    //     }
    
    //     return now()->format('Y-m-d h:i:s');
    // }

