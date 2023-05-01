<?php


if (!function_exists('tenant')) {

    function tenant() {
        return (object) [
            'id' => 1,
            'name' => 'Default tenant',
            'short_name' => 'default-tenant',
            'is_single' => true
        ];
    }
}
