import Vue from 'vue'
import Collection from "./Collection";
import Permission from "./Permission";
import optional from "./Support/Optional";

Vue.prototype.$optional = (obj, ...props) => {
    return optional(obj, ...props);
};

export const collection = list => new Collection(list);

Vue.prototype.collection = list => collection(list);

Vue.prototype.$can = ability => (new Permission()).can(ability);

Vue.prototype.$isAdmin = () => (new Permission()).isAdmin();

Vue.prototype.$isOnlyDepartmentManager = () => (new Permission()).isOnlyDepartmentManager();


