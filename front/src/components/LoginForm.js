import {Button, Form} from 'react-bootstrap';
import React, {useRef} from 'react';
import {useDispatch} from "react-redux";
import {setToken, setAdmin} from '../redux/actions';
import {Link} from 'react-router-dom';

const LoginForm = () => {
    const emailRef = useRef(null);
    const passwordRef = useRef(null);
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
                email: emailRef.current.value,
                password: passwordRef.current.value,
            })
        };

        try {
            const response = await fetch(`${process.env.REACT_APP_BACKEND_API_URL}/login`, requestOptions);
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
                <Form.Group className="mb-3" controlId="formBasicEmail">
                    <Form.Label>Email address</Form.Label>
                    <Form.Control type="email" placeholder="Enter email" ref={emailRef}/>
                </Form.Group>

                <Form.Group className="mb-3" controlId="formBasicPassword">
                    <Form.Label>Password</Form.Label>
                    <Form.Control type="password" placeholder="Password" ref={passwordRef}/>
                </Form.Group>

                <Button className="me-3" variant="primary" type="submit">
                    Вход
                </Button>
                <Link className='link-success link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover' to="/registration">Регистрация</Link>
            </Form>

        </div>
    );
};

export default LoginForm;