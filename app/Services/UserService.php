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
                foreach($data as $cValue){
                    echo $cValue . "\n";
                    $this->storeCsvToDb($cValue);
                }
            }
            fclose($handle);
        }
        return [
          'success' => true,
          'message' => 'Completed',
        ];
    }

    public function storeCsvToDb($cValue){
        $user = $this->user();
        $user->title = $this->getTitle($cValue);
        $user->first_name = $this->getFirstName($cValue);
        $user->initial = $this->getInitials($cValue);
        $user->last_name = $this->getLastName($cValue);
        $user->save();
    }

    private function getTitle($name): ?string
    {
        $nameArray = explode(' ', $name);
        if(in_array($nameArray[0], $this->userTitles, true) & !in_array($nameArray[1], $this->and, true)){
            //$newName = implode('', $nameArray[0]);
            return $nameArray[0];
        }
        return null;
    }

    private function getFirstName($name): ?string
    {
        $name = strtr($name, array('.' => ''));
        $nameArray = explode(' ', $name);
        if(in_array($nameArray[0], $this->userTitles, true) && !in_array($nameArray[1], $this->and, true)){
            return $nameArray[1];
        }
        return null;
    }

    private function getInitials($name): ?string
    {
        $name = strtr($name, array('.' => ''));
        $nameArray = explode(' ', $name);
        if(in_array($nameArray[0], $this->userTitles, true) & strlen($nameArray[1]) === 1){
            return $nameArray[1];
        }
        return null;
    }

    private function getLastName($name){
        $nameArray = explode(' ', $name);
        $lastNamekey = array_key_last($nameArray);
        return $nameArray[$lastNamekey];
    }

    private function splitTwoNames($name): array
    {
        $firstName = '';
        $secondName = '';
        $nameArray = explode(' ', $name);
        if(in_array($nameArray[0], $this->userTitles, true) && in_array($nameArray[1], $this->and, true)){
            $lastNamekey = array_key_last($nameArray);
            $firstName = $nameArray[0].' '.$nameArray[$lastNamekey];
            $secondName = $nameArray[2].' '.$nameArray[$lastNamekey];
        }else if(array_intersect($this->and, $nameArray) && !in_array($nameArray[1], $this->and, true)){
            $newNameArray = explode('and', $name);
            $firstName = $newNameArray[0];
            $secondName = $newNameArray[1];
        }
        return [
            'first_name' => $firstName,
            'second_name' => $secondName,
        ];
    }





}
