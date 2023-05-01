<template>
    <div class="profile-box">
        <app-avatar :title="employee.full_name"
                    :status="employee.status"
                    avatar-class="avatars-w-50 mb-2 mx-auto d-inline-block"
                    :img="$optional(employee, 'profile_picture', 'full_url')"
        />

        <h6 class="primary-text-color">{{ employee.full_name }}</h6>
        <p class="text-muted mb-0 font-size-90">{{ $optional(employee, 'department', 'name') }}</p>
    </div>
</template>

<script>
export default {
    name: "AppEmployeeProfileCard",
    props: {
        employee: {}
    }
}
</script>