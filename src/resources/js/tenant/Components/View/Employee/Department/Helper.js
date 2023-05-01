import {pick} from "lodash";
import optional from "../../../../../common/Helper/Support/Optional";

export const organizeOrganizationStructure = data => {
    let chart = formatDepartment(data);
    chart.children = chart.child_departments.map(department => {
        return organizeChildren(department);
    });
    return chart;
}

const organizeChildren = department => {
    department.children = department.child_departments.map(department => {
        return organizeChildren(department);
    })

    return formatDepartment(department);
}

const formatDepartment = data => {
    const object = pick(data, ['id', 'name', 'manager', 'child_departments', 'children']);
    object.title = getTitle({...object});
    delete object.manager;
    return object;
}

const getTitle = department => {
    return optional(department, 'manager', 'full_name');
}
