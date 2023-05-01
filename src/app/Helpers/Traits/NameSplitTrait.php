<?php

namespace App\Helpers\Traits;

trait NameSplitTrait
{
    public function getFirstnameLastnameFromName(string $name)
    {
        $splited = explode(' ', $name);

        if (count($splited)) {
            if (count($splited) == 2) {
                return [
                    'first_name' => $splited[0],
                    'last_name' => $splited[1],
                ];
            }else if (count($splited) == 1) {
                return [
                    'first_name' => $splited[0],
                    'last_name' => ' ',
                ];
            }else if (count($splited) == 3) {
                return [
                    'first_name' => $splited[0].' '.$splited[1],
                    'last_name' => $splited[2],
                ];
            }else {
                return [
                    'first_name' => $splited[0],
                    'last_name' => join(' ', array_slice($splited, 1)),
                ];
            }
        }
    }
}