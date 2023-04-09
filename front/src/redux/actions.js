export const setToken = (token, user_id) => {
    return {
        type: 'SET_TOKEN',
        token,
        user_id
    }
}

export const clearToken = () => ({type: 'CLEAR_TOKEN'});
export const setAdmin = () => ({ type: 'SET_ADMIN' });
export const clearAdmin = () => ({ type: 'CLEAR_ADMIN' });