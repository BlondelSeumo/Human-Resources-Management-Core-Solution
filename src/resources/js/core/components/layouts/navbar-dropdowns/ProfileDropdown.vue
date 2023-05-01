<template>
    <li class="nav-item nav-profile dropdown">
        <a class="d-flex align-items-center nav-link dropdown-toggle mr-0"
           id="profileDropdown"
           href="#"
           data-toggle="dropdown"
           aria-expanded="false">
            <div class="nav-profile-text">
                <p class="mb-0 text-black text-right">
                    {{ user.full_name }} <br>
                    <span class="text-secondary font-size-90 d-block mt-1">
                        {{ user.role }}
                    </span>
                </p>
            </div>
            <app-avatar :title="user.full_name"
                        avatar-class="avatars-w-40"
                        :img="user.img"
                        alt-text="image"/>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
            <div class="dropdown-item profile">
                <app-avatar :title="user.full_name"
                            avatar-class="avatars-w-50"
                            :img="user.img"
                            alt-text="image"/>
                <div class="nav-profile-text font-size-default ml-2">
                    <p class="my-0 text-black">{{ user.full_name }}</p>
                    <span class="text-secondary font-size-90">{{ user.role }}</span>
                </div>
            </div>
            <div class="dropdown-divider"/>
            <a class="dropdown-item" v-for="(item, index) in data" :href="item.url" :key="index">
                <app-icon class="mr-3" :name="item.optionIcon"/>
                {{ item.optionName }}
            </a>
        </div>
    </li>
</template>

<script>
    export default {
        name: "ProfileDropdown",
        props: {
            user: {
                type: Object,
                required: true
            },
            data: {
                type: Array,
                required: true
            }
        }
    }
</script>
