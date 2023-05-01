<?php

namespace App\Services\Tenant\Salary;

use App\Helpers\Traits\DateTimeHelper;
use App\Models\Tenant\Salary\Salary;
use App\Services\Tenant\TenantService;
use Illuminate\Validation\ValidationException;

class SalaryService extends TenantService
{
    use DateTimeHelper;

    public function validateAttributes(): self
    {
        validator($this->getAttributes(),[
            'amount' => 'required',
            'start_at' => 'required'
        ])->validate();

        return $this;
    }

    public function updateSalary(): self
    {
        $attributes = [
            'user_id' => $this->model->id,
            'amount' => $this->getAttr('amount'),
            'start_at' => $this->carbon($this->getAttr('start_at'))->toDate(),
            'added_by' => auth()->id()
        ];

        $this->checkEffectiveDateValidations()
            ->update($attributes);

        return $this;
    }

    public function checkEffectiveDateValidations(): self
    {
        $isFirstSalaryExists = $this->model
            ->salaries()
            ->exists();

        throw_if(
            $isFirstSalaryExists &&
            $this->carbon($this->getAttr('start_at'))->parse()->isPast(),
            ValidationException::withMessages(['start_at' => [__t('effective_date_must_be_getter_than_now')]])
        );

        return $this;
    }

    public function update($attributes): self
    {
        $needUpdate = Salary::query()
            ->where('user_id', $this->model->id)
            ->whereDate('start_at', '>=', nowFromApp())
            ->whereNull('end_at')
            ->first();

        if ($needUpdate){
            $previousStartDate = $needUpdate->start_at;
            $this->updatePreviousRecord($previousStartDate);
            $needUpdate->update($attributes);
        }else{
            $this->endPreviousSalary();
            Salary::query()->create($attributes);
        }

        return $this;
    }

    public function updatePreviousRecord($previousStartDate): self
    {
        $this->model->salaries()
            ->whereDate('end_at', $previousStartDate)
            ->update([
                'end_at' => $this->getAttr('start_at')
            ]);

        return $this;
    }

    public function endPreviousSalary(): self
    {
        $this->model
            ->salaries()
            ->whereNull('end_at')
            ->update(['end_at' => $this->getAttr('start_at')]);

        return $this;
    }
}
