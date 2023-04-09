const initialState = {
    token: null,
    user_id: null,
    is_admin: false
};

const tokenReducer = (state = initialState, action) => {
    switch (action.type) {
        case 'SET_TOKEN':
            return {
                ...state,
                token: action.token,
                user_id: action.user_id,
            };
        case 'CLEAR_TOKEN':
            return {
                ...state,
                token: null,
                user_id: null,
            };

        case 'SET_ADMIN':
            return {
                ...state,
                is_admin: true,
            };
        case 'CLEAR_ADMIN':
            return {
                ...state,
                is_admin: false,
            };
        default:
            return state;
    }
};

export default tokenReducer;