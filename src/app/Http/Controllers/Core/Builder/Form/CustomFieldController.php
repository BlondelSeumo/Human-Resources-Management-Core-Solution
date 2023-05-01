<?php

namespace App\Http\Controllers\Core\Builder\Form;

use App\Filters\Common\Form\CustomFieldFilter as AppCustomFieldFilter;
use App\Filters\Core\CustomFieldFilter;
use App\Hooks\Form\BeforeCustomFieldSaved;
use App\Hooks\Form\CustomFieldCreated;
use App\Hooks\Form\WhileCustomFieldDeleting;
use App\Http\Controllers\Controller;
use App\Http\Requests\Core\Builder\Form\CustomFieldRequest;
use App\Models\Core\Builder\Form\CustomField;
use App\Services\Core\Builder\Form\CustomFieldService;

class CustomFieldController extends Controller
{
    public $hook;

    public function __construct(CustomFieldService $service, CustomFieldFilter $filter, CustomFieldCreated $hook)
    {
        $this->service = $service;
        $this->filter = $filter;
        $this->hook = $hook;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return (new AppCustomFieldFilter(
            $this->service
                ->with('customFieldType:id,name', 'createdBy:id,first_name,last_name')
                ->orderBy('id', 'DESC')
                ->filters($this->filter))
        )->filter()
            ->paginate(request('per_page', 15));
    }

    /**
     * @param CustomFieldRequest $request
     * @return array
     */
    public function store(CustomFieldRequest $request)
    {
        BeforeCustomFieldSaved::new(true)
            ->handle();

        $field = $this
            ->service
            ->save(
                array_merge(
                    $request->only('name', 'context', 'meta', 'in_list', 'custom_field_type_id'),
                    $request->has('tenant_id') ? $request->only('tenant_id') : []
                )
            );

        $this->hook
            ->setModel($field)
            ->handle();

        return created_responses('custom_field');
    }


    public function show(CustomField $customField)
    {
        return $customField->load('customFieldType');
    }


    public function update(CustomField $customField, CustomFieldRequest $request)
    {
        BeforeCustomFieldSaved::new(true)
            ->handle();

        $customField->update(
            array_merge(
                $request->only('name', 'context', 'meta', 'in_list', 'custom_field_type_id'),
                $request->has('tenant_id') ? $request->only('tenant_id') : []
            )
        );

        $this->hook
            ->setModel($customField)
            ->handle();

        return updated_responses('custom_field');
    }


    public function destroy(CustomField $customField)
    {
        WhileCustomFieldDeleting::new(true)
            ->setModel($customField)
            ->handle();

        if ($customField->delete()) {
            return deleted_responses('custom_field');
        }

        return failed_responses();
    }
}
