export const FilterCloseMixin = {

    methods:{
        closeDropDown() {
            $('.dropdown-menu').removeClass('show');
        },
        removeDropdownShow() {
            $('.dropdown').removeClass('show');
        }
    }

}
