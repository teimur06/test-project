import React, {useRef} from 'react';
import {Form, Button} from 'react-bootstrap';
import {Link} from "react-router-dom";
import {useDispatch} from "react-redux";
import {setAdmin, setToken} from "../redux/actions";

function RegisterForm() {
    const email = useRef(null);
    const name = useRef(null);
    const password = useRef(null);
    const password_confirmation = useRef(null);
    const dispatch = useDispatch();


    const handleSubmit = async (event) => {
        event.preventDefault();

        const requestOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                name: name.current.value,
                email: email.current.value,
                password: password.current.value,
                password_confirmation: password_confirmation.current.value,
            })
        };

        try {
            const response = await fetch(`${process.env.REACT_APP_BACKEND_API_URL}/register`, requestOptions);
            const data = await response.json();
            if (data.success) {
                dispatch(setToken(data.access_token, data.user_id))
                if (data.is_admin) dispatch(setAdmin());
                console.log(data);
            }

        } catch (error) {
            console.log(error);
        }
    };

    return (
        <div>
            <Form onSubmit={handleSubmit}>
                <Form.Group className="mb-3" controlId="formBasicName">
                    <Form.Label>Name</Form.Label>
                    <Form.Control type="text" placeholder="Enter name" ref={name}/>
                </Form.Group>

                <Form.Group className="mb-3" controlId="formBasicEmail">
                    <Form.Label>Email address</Form.Label>
                    <Form.Control type="email" placeholder="Enter email" ref={email}/>
                </Form.Group>

                <Form.Group className="mb-3" controlId="formBasicPassword">
                    <Form.Label>Password</Form.Label>
                    <Form.Control type="password" placeholder="Password" ref={password}/>
                </Form.Group>

                <Form.Group className="mb-3" controlId="formBasicPasswordConfirm">
                    <Form.Label>Confirm Password</Form.Label>
                    <Form.Control type="password" placeholder="Confirm Password" ref={password_confirmation}/>
                </Form.Group>

                <Button className="me-5" variant="primary" type="submit">
                    Регистрация
                </Button>
                <Link className='link-success link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover' to="/login">Войти</Link>
            </Form>

        </div>
    );
}

export default RegisterForm;