<?php

namespace App\Services;

use App\Models\User;

/**
 * Class UserService.
 */
class UserService
{
    private array $userTitles = ['Mr', 'Mrs', 'Dr', 'Mister', 'Prof'];
    private array $and = ['And', '&', 'and'];

    public function user(): User
    {
        return new User();
    }

    public function importFile($request): array
    {
        $row = 1;
        $path = $request->file('file')->getRealPath();
        if (($handle = fopen($path, 'rb')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                echo "$num fields in line $row: \n";
                $row++;
                foreach ($data as $cValue) {
                    echo $cValue . "\n";
                    $user = $this->user();
                    $user->title = $cValue;
                    $user->first_name = $cValue;
                    $user->initial = $cValue;
                    $user->last_name = $cValue;
                    $user->save();
                }
            }
            fclose($handle);
        }
        return [
          'success' => true,
          'message' => 'Completed',
        ];
    }

    public function storeCsvExport(){

    }

    private function getTitle($name): ?string
    {
        $nameArray = explode(' ', $name);
        if(in_array($nameArray[0], $this->userTitles, true) & !in_array($nameArray[1], $this->and, true)){
            return implode('', $nameArray[0]);
        }
        return null;
    }

    private function getFirstName($name): ?string
    {
        $nameArray = explode(' ', $name);
        if(in_array($nameArray[0], $this->userTitles, true) & !in_array($nameArray[1], $this->and, true)){
            return implode('', $nameArray[1]);
        }
        return null;
    }

    private function getInitials($name): ?string
    {
        $nameArray = explode(' ', $name);
        if(in_array($nameArray[0], $this->userTitles, true) & strlen($nameArray[1]) <= 2){
            return implode('', $nameArray[1]);
        }
        return null;
    }

    private function getLastName($name){

    }

    private function getInitial($name){

    }





}
