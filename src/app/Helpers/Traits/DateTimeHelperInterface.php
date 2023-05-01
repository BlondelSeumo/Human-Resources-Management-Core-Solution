<?php


namespace App\Helpers\Traits;


use Illuminate\Support\Carbon;

interface DateTimeHelperInterface
{
    public function toDate(): string;

    public function toTime(): string;

    public function toDateTime(): string;

    public function toDay(): string;

    public function toDayInLowerCase(): string;

    public function parse(): Carbon;
}