<?php


namespace App\Helpers\Traits;


trait SettingHelper
{
    use SettingKeyHelper,
        DateRangeHelper;

    public function leaveYear(): array
    {
        return $this->convertRangesToStringFormat(
            $this->yearRange(
                $this->getSettingFromKey('leave')('start_month') ?: 'jan'
            )
        );
    }
}