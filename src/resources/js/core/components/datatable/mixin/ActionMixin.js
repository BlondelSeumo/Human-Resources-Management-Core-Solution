import CoreLibrary from "../../../helpers/CoreLibrary";

export const ActionMixin = {
    extends: CoreLibrary,
    props: {
        actions: {
            required: true,
            type: Array
        },
        uniqueKey: {},
        rowData: {}
    },
    data() {
        return {
            action: {}
        }
    },
    methods: {
        modal() {
            $(`#${this.action.modalId}`).modal('show');
        },
        pageRedirect(action) {
            const url = `${this.action.url}/${this.actionUniqueKey(action)}`;
            window.location.replace(url);
        },
        actionUniqueKey(){
            return this.action.uniqueKey ? this.rowData[this.action.uniqueKey] : this.uniqueKey;
        },
        callMethod(e, action) {
            this.action = action;
            if (action.type === "modal") this.modal()
            else if (action.type === "page") this.pageRedirect();
            this.sendAction();
        },
        sendAction() {
            this.$emit("action", this.rowData, this.action, true);
        }
    },
    computed: {
        visibleActions() {
            return this.actions.filter(action => {
                if (this.isUndefined(action.title)) return false
                if (this.isUndefined(action.modifier)) return true
                return action.modifier(this.rowData)
            })
        }
    }
};
