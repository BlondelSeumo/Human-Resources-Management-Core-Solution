import {collection} from './helpers';
import optional from './Support/Optional';

export const users = (nEvent) => {
    const audiences = collection(
        optional(nEvent.settings, 'audiences') || []
    ).find('users', 'audience_type');

    if (audiences) {
        return optional(audiences, 'audiences');
    }
    return []
};

export const roles = (nEvent) => {
    const audiences = collection(
        optional(nEvent.settings, 'audiences') || []
    ).find('roles', 'audience_type');

    if (audiences) {
        return optional(audiences, 'audiences');
    }
    return []
};
