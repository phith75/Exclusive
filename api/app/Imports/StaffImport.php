<?php

namespace App\Imports;

use App\Http\Requests\StaffImportRequest;
use App\Models\Staff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StaffImport implements ToModel, WithHeadingRow
{
    use RemembersRowNumber;

    protected $importCount = 0;
    protected $emails = [];
    protected $userCodes = [];

    public function model(array $row)
    {
        $currentRowNumber = $this->getRowNumber();
        $formattedErrors = [];
        if (in_array($row['email'], $this->emails)) {
            $formattedErrors['email'] = [
                'message' => 'Duplicate email in the import file.',
                'value' => $row['email'],
                'row' => $currentRowNumber,
            ];
        } else {
            $this->emails[] = $row['email'];
        }

        if (in_array($row['user_code'], $this->userCodes)) {
            $formattedErrors['user_code'] = [

                'message' => 'Duplicate user code in the import file.',
                'value' => $row['user_code'],
                'row' => $currentRowNumber,
            ];
        } else {
            $this->userCodes[] = $row['user_code'];
        }

        // Validate the row against the rules
        $validator = Validator::make($row, (new StaffImportRequest())->rules());

        if ($validator->fails()) {
            $errors = $validator->errors()->messages();
            foreach ($errors as $field => $messages) {
                foreach ($messages as $message) {
                    $formattedErrors[$field] = [
                        'message' => $message,
                        'value' => $row[$field] ?? null,
                        'row' => $currentRowNumber,
                    ];
                }
            }
        }

        // If there are any errors, throw a ValidationException
        if (! empty($formattedErrors)) {
            throw ValidationException::withMessages($formattedErrors);
        }

        // Create or update the staff record
        Staff::updateOrCreate(
            [
                'user_code' => $row['user_code'],
            ],
            [
                'email' => $row['email'],
                'name' => $row['name'],
            ]
        );

        $this->importCount++;
    }

    public function getImportCount()
    {
        return $this->importCount;
    }
}
