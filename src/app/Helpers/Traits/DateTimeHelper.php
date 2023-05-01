<?php


namespace App\Helpers\Traits;


use Illuminate\Support\Carbon;

trait DateTimeHelper
{
    protected function carbon($timestamp, $timezone = 'UTC'): DateTimeHelperInterface
    {
        if (!$timestamp instanceof Carbon) {
            $timestamp = Carbon::parse($timestamp, $timezone);
        }

        return new class($timestamp) implements DateTimeHelperInterface {
            protected static string $dateTimeFormat = 'Y-m-d H:i:s';
            protected static string $timeFormat = 'H:i:s';
            protected static string $dateFormat = 'Y-m-d';
            protected static string $dayFormat = 'D';

            protected Carbon $carbon;

            public function __construct($carbon)
            {
                $this->carbon = $carbon;
            }

            public function toDateTime(): string
            {
                return $this->carbon->format(self::$dateTimeFormat);
            }

            public function toDate(): string
            {
                return $this->carbon->format(self::$dateFormat);
            }


            public function toTime(): string
            {
                return $this->carbon->format(self::$timeFormat);
            }

            public function toDay(): string
            {
                return $this->carbon->format(self::$dayFormat);
            }

            public function toDayInLowerCase(): string
            {
                return strtolower($this->toDay());
            }

            public function parse(): Carbon
            {
                return $this->carbon;
            }
        };
    }
}