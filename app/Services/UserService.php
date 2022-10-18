<?php

namespace App\Services;

use App\Models\User;

/**
 * Class UserService.
 */
class UserService
{
    public function user(): User
    {
        return new User();
    }

    public function importFile($request){
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
        foreach($nameArray as $value){
            if(in_array($value, ['Mr', 'Mrs'])){
                return implode('', $value);
            }
        }
        return null;
    }

    private function getFirstName($name){

    }

    private function getInitial($name){

    }

    private function getLastName($name){

    }



}
