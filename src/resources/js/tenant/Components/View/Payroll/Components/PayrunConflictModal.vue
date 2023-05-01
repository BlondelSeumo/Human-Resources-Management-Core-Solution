<template>
    <modal id="payslip-edit-modal"
           size="large"
           v-model="showModal"
           :title="this.$t('conflicted_payslip_for_employee')"
           :cancel-btn-label="$t('close')"
           :hide-submit-button="true"
           @=""
           :preloader="preloader">

        <div class="d-flex justify-content-between mb-primary align-items-center">
            <div>
                <app-user-media
                    :row-data="selectedUser"
                    :value="selectedUser.profile_picture"
                />
            </div>
            <div class="width-1 height-50 border-right"></div>
            <div class="pt-2">
                <app-filter-with-search
                    :options="userModalFilter"
                    @filteredValue="setUserId"
                />
            </div>
            <div v-if="!isPreviousNull || !isNextNull" class="d-flex align-items-center ml-primary">
                <a href="#" @click="changeUser('previous')"
                   :class="{'disabled text-muted' : isPreviousNull}">
                    <i class="fa fa-angle-left fa-2x"></i>
                </a>
                <span class="text-muted px-2">  </span>
                <a href="#" @click="changeUser('next')"
                   :class="{'disabled text-muted' : isNextNull}">
                    <i class="fa fa-angle-right fa-2x fa-circle-thin"></i>
                </a>
            </div>
        </div>

        <app-pre-loader v-if="loading"/>

        <template v-else>
            <conflicted-payslip-row
                v-if="conflictedPayslips.length"
                :payrunId="payrunId"
                :payslips="conflictedPayslips"
                @payslipDeleted="getConflictedPayslips"
            />
            <app-empty-data-block v-else :message="$t('no_payslip_found')"/>
        </template>

    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {PAYRUN} from "../../../../Config/ApiUrl";
import ConflictedPayslipRow from "./ConflictedPayslipRow";
import SelectAbleUserFilterMixin from "../../../Mixins/SelectAbleUserFilterMixin";
import {collection} from "../../../../../common/Helper/helpers";

export default {
    name: "PayrunConflictModal",
    components: {ConflictedPayslipRow},
    mixins: [ModalMixin, SelectAbleUserFilterMixin],
    props: {
        payrunId: {}
    },
    data() {
        return {
            conflictedPayslips: [],
            selectedUser: {},
            loading: false,
        }
    },
    computed: {
        isPreviousNull() {
            return collection(this.getAllUsers).first().id === this.selectedUser.id
        },
        getAllUsers() {
            return this.tableOptions.filters.find(data => data.key === 'user').option
        },
        isNextNull() {
            return collection(this.getAllUsers).last().id === this.selectedUser.id
        },
        userModalFilter(){
            let filters = this.tableOptions.filters.filter(item => item.filterName !== 'user-search-filter')

            let tableOptionsClone = _.cloneDeep(this.tableOptions)

            tableOptionsClone.filters = filters

            return tableOptionsClone;
        }
    },
    methods: {
        setUserId(filter) {
            let userId = filter.user !== "" ? filter.user : this.getAllUsers[0].id;
            this.selectedUser = this.getAllUsers.find((user) => user.id === userId);
            this.getConflictedPayslips(userId);
            this.setActiveUserToAvatarFilter(userId)
            this.setActiveUserToDropdownFilter(userId);
        },
        changeUser(type) {
            let index = this.getAllUsers.indexOf(this.selectedUser)
            if (type === 'next') {
                this.selectedUser = this.getAllUsers[index + 1]
                this.setUserId({user: this.selectedUser.id})
            } else if (type === 'previous') {
                this.selectedUser = this.getAllUsers[index - 1]
                this.setUserId({user: this.selectedUser.id})
            }
        },
        getSelectableUsers() {
            this.preloader = true;
            axiosGet(`${PAYRUN}/${this.payrunId}/users/conflicted`)
                .then(response => {
                    this.selectedUser = response.data[0];
                    this.tableOptions.filters.find(fl => fl.key === 'user' && fl.type === 'drop-down-filter').option = response.data;
                    this.tableOptions.filters.find(fl => fl.key === 'user' && fl.type === 'avatar-filter').option = response.data.map((user) => {
                        return {
                            ...user,
                            status: user.status.class
                        }
                    });
                }).finally(() => {
                this.setUserId({user: this.selectedUser.id})
            })
        },
        getConflictedPayslips(user) {
            this.loading = true
            axiosGet(`${PAYRUN}/${this.payrunId}/user/${user || this.selectedUser.id}/conflicted`).then(({data}) => {
                this.conflictedPayslips = data;
            }).finally(() => {
                this.preloader = false;
                this.loading = false;
            })
        }
    }
}
</script>

<style scoped>

</style>