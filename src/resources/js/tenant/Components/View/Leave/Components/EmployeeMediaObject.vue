<template>
    <div class="justify-content-start px-primary profile-box">
        <div class="mr-2">
            <app-avatar
                :title="employee.full_name"
                :status="employee.status"
                avatar-class="avatars-w-50 d-inline-block"
                :img="$optional(employee, 'profile_picture', 'full_url')"
            />
        </div>
        <div>
            <h6 class="primary-text-color">{{ employee.full_name }}</h6>
            <p class="text-muted mb-0 font-size-90">{{ $optional(employee, 'department', 'name') }}</p>
        </div>
    </div>
</template>

<script>
export default {
    name: "EmployeeMediaObject",
    props: {
        employee: {}
    }
}
</script>