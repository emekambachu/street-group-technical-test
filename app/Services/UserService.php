<?php

namespace App\Services;

use App\Models\User;

/**
 * Class UserService.
 */
class UserService
{
    private array $userTitles = ['Mr', 'Mrs', 'Dr', 'Mister', 'Prof', 'Ms'];
    private array $and = ['And', '&', 'and'];
    public array $output = [];

    public function user(): User
    {
        return new User();
    }

    public function importFile($request): array
    {
        $path = $request->file('file')->getRealPath();
        if (($handle = fopen($path, 'rb')) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                foreach($data as $cValue){
                    if($cValue === 'homeowner'){
                        continue;
                    }
                    // Convert name string to array
                    $nameArray = explode(' ', $cValue);
                    // Split double surnames. e.g Mr and Mrs Smith
                    if(in_array($nameArray[0], $this->userTitles, true) && in_array($nameArray[1], $this->and, true)){
                        $lastNamekey = array_key_last($nameArray);
                        $firstName = $nameArray[0].' '.$nameArray[$lastNamekey];
                        $secondName = $nameArray[2].' '.$nameArray[$lastNamekey];
                        // save each name in array function
                        $this->storeCsvInArray($firstName);
                        $this->storeCsvInArray($secondName);
                        continue;
                    }
                    // Split names full double names. e.g Mr John Smith and Mrs Jane Smith
                    if(array_intersect($this->and, $nameArray) && !in_array($nameArray[1], $this->and, true)){
                        $newNameArray = explode('and', $cValue);
                        $firstName = trim($newNameArray[0]);
                        $secondName = trim($newNameArray[1]);
                        // save each name in array function
                        $this->storeCsvInArray($firstName);
                        $this->storeCsvInArray($secondName);
                        continue;
                    }
                    // save other names in array
                    $this->storeCsvInArray($cValue);
                }
            }
             fclose($handle);
        }
        return [
          'success' => true,
          'output' => $this->output,
          'message' => 'Completed',
        ];
    }

    public function storeCsvInArray($cValue): void
    {
        $this->output[] = [
            'title' => $this->getTitle($cValue),
            'first_name' => $this->getFirstName($cValue),
            'initial' => $this->getInitials($cValue),
            'last_name' => $this->getLastName($cValue),
        ];
    }

    private function getTitle($name): ?string
    {
        $nameArray = explode(' ', $name);
        if(in_array($nameArray[0], $this->userTitles, true) & !in_array($nameArray[1], $this->and, true)){
            return $nameArray[0];
        }
        return null;
    }

    private function getFirstName($name): ?string
    {
        $name = strtr($name, array('.' => ''));
        $nameArray = explode(' ', $name);
        if(in_array($nameArray[0], $this->userTitles, true) && !in_array($nameArray[1], $this->and, true) && strlen($nameArray[1]) > 1 && count($nameArray) > 2){
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

}
