<?php

namespace App\Export;

use App\Helpers\Traits\DateRangeHelper;
use App\Helpers\Traits\DateTimeHelper;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllAssetExport implements FromArray, WithHeadings, ShouldAutoSize
{
    use Exportable, DateTimeHelper, DateRangeHelper;

    private Collection $assets;

    public function __construct(Collection $assets)
    {
        $this->assets = $assets;
    }

    public function headings(): array
    {
        return [
            __t('asset_name'),
            __t('asset_type'),
            __t('asset_code'),
            __t('asset_serial_number'),
            __t('is_working'),
            __t('assigned_employee'),
            __t('date'),
            __t('note'),
        ];
    }

    public function array(): array
    {
        return $this->assets->map(function ($asset) {
            return [
                $asset->name,
                $asset->type->name??'-',
                $asset->code,
                $asset->serial_number,
                $asset->is_working,
                $asset->user->name??'-',
                $asset->date,
                $asset->note
            ];
        })->toArray();
    }
}