import {axiosGet} from "../../../common/Helper/AxiosHelper";
import {SELECTABLE} from "../../Config/ApiUrl";

export default {
    data() {
        return {
            tableOptions: {
                showSearch: false,
                showFilter: true,
                filters: [
                    {
                        title: this.$t('users'),
                        type: 'drop-down-filter',
                        key: 'user',
                        filterName: 'user-search-filter',
                        option: [],
                        listValueField: 'full_name',
                        active: this.firstUser ? JSON.parse(this.firstUser).id : 0
                    },
                    {
                        title: this.$t('users'),
                        type: 'avatar-filter',
                        key: 'user',
                        option: [],
                        filterName: 'user-avatar-filter',
                        listValueField: 'full_name',
                        imgRelationship: 'profile_picture',
                        imgKey: 'full_url',
                        active: this.firstUser ? JSON.parse(this.firstUser).id : 0
                    }
                ],
            },
        }
    },
    methods: {
        getSelectableUsers() {
            if (!this.user || this.fromEmployeeDetails) {
                return;
            }
            axiosGet(this.selectableUserUrl ? this.selectableUserUrl :
                (`${SELECTABLE}/${this.userType ? this.userType : 'attendance'}/${this.user.id}/users`))
                .then(response => {
                    this.tableOptions.filters.find(fl => fl.key === 'user' && fl.type === 'drop-down-filter').option = response.data;
                    this.tableOptions.filters.find(fl => fl.key === 'user' && fl.type === 'avatar-filter').option = response.data.map((user) => {
                        return {
                            ...user,
                            status: user.status.class
                        }
                    });
                })
        },
        setActiveUserToAvatarFilter(user_id) {
            this.tableOptions.filters.find(fl => fl.key === 'user' && fl.type === 'avatar-filter').active = user_id;
        },
        setActiveUserToDropdownFilter(user_id) {
            this.tableOptions.filters.find(fl => fl.key === 'user' && fl.type === 'drop-down-filter').active = user_id;
        }
    },
    created() {
        this.getSelectableUsers();
    }
}