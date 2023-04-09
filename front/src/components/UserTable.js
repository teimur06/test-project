import React, {useEffect, useRef, useState} from 'react';
import {Table} from 'react-bootstrap';
import { useSelector} from 'react-redux';

const UserTable = () => {
    const twiceEffect = useRef(false);
    const token = useSelector((state) => state.token);
    const [users, setUsers] = useState([]);
    const user_id = useSelector((state) => state.user_id);


    const requestOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`,
        },
    };

    useEffect(() => {
        if (twiceEffect.current === false) {
            console.log("user_id", user_id)

            async function fetchData() {
                const response = await fetch(
                    `${process.env.REACT_APP_BACKEND_API_URL}/users`,
                    requestOptions
                );
                const data = await response.json();
                console.log(data);
                setUsers(data.users);
            }

            fetchData();
        }
        return () => {twiceEffect.current = true}
    });

    const handleAdminChange = (event, user) => {
        const newIsAdmin = event.target.checked;
        const newUsers = users.map((u) => {
            if (u.id === user.id) {
                return {...u, is_admin: newIsAdmin};
            }
            return u;
        });
        setUsers(newUsers);

        const requestBody = [{user_id: user.id, is_admin: newIsAdmin}];
        const postRequestOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Authorization: `Bearer ${token}`,
            },
            body: JSON.stringify(requestBody),
        };

        fetch(
            `${process.env.REACT_APP_BACKEND_API_URL}/users/update_is_admin`,
            postRequestOptions
        )
            .then((response) => response.json())
            .then((data) => console.log(data))
            .catch((error) => console.log(error));
    };

    return (
        <Table striped bordered hover>
            <thead>
            <tr>
                <th>ID</th>
                <th>Имя</th>
                <th>Email</th>
                <th>Администратор</th>
            </tr>
            </thead>
            <tbody>
            {users.map((user) => (
                <tr key={user.id}>
                    <td>{user.id}</td>
                    <td>{user.name}</td>
                    <td>{user.email}</td>
                    <td>
                        <input
                            type="checkbox"
                            checked={user.is_admin}
                            disabled={user.id === user_id}
                            onChange={(event) => handleAdminChange(event, user)}
                        />
                    </td>
                </tr>
            ))}
            </tbody>
        </Table>
    );
};

export default UserTable;
