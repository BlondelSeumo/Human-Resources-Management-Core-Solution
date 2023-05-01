<?php


namespace App\Helpers\Core\General;


class ResponseHelper
{
    public function createdResponse($name, $data = [])
    {
        return array_merge([
            'status' => true,
            'message' => trans('default.created_response', [
                'name' => __t($name)
            ]),
        ], $data);
    }

    public function updatedResponse($name, $data = [])
    {
        return array_merge([
            'status' => true,
            'message' => trans('default.updated_response', [
                'name' => __t($name)
            ]),
        ], $data);
    }

    public function deletedResponse($name, $data = [])
    {
        return array_merge([
            'status' => true,
            'message' => trans('default.deleted_response', [
                'name' => __t($name)
            ]),
        ], $data);
    }

    public function failedResponse($data = [])
    {
        return array_merge([
            'status' => false,
            'message' => trans('default.failed_response')
        ], $data);
    }

    public function attachedResponse($name, $data = [])
    {
        return array_merge([
            'status' => true,
            'message' => trans('default.attached_response', [
                'name' => __t($name)
            ])
        ], $data);
    }

    public function detachedResponse($name, $data = [])
    {
        return array_merge([
            'status' => true,
            'message' => trans('default.detached_response', [
                'name' => __t($name)
            ])
        ], $data);
    }

    public function duplicatedResponse($name, $data = [])
    {
        return array_merge([
            'status' => true,
            'message' => trans('default.duplicated_response', [
                'name' => __t($name)
            ])
        ], $data);
    }

    public function statusResponse($name, $status, $data = [])
    {
        return array_merge([
            'status' => true,
            'message' => trans('default.status_updated_response', [
                'name' => __t($name),
                'status' => strtolower(__t($status))
            ])
        ], $data);
    }

}
