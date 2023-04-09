import 'bootstrap/dist/css/bootstrap.min.css';
import './App.css';
import LoginForm from './components/LoginForm';
import RegisterForm from './components/RegisterForm';
import Main from './components/Main';
import {Button, Container} from 'react-bootstrap';
import {BrowserRouter, Route, Routes, Navigate, Link} from 'react-router-dom';
import {useDispatch, useSelector} from 'react-redux';
import {Navbar, Nav} from 'react-bootstrap';
import {clearAdmin, clearToken} from "./redux/actions";
import UserTable from "./components/UserTable";
import CityTable from "./components/CityTable";

function App() {
    const dispatch = useDispatch();
    const token = useSelector(state => state.token);
    const is_admin = useSelector(state => state.is_admin);

    const logout = () => {
        if (!token) return;

        const requestOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
            },
        };
        fetch(`${process.env.REACT_APP_BACKEND_API_URL}/logout`, requestOptions)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                dispatch(clearToken());
                dispatch(clearAdmin());
            })
            .catch(error => console.log(error));
    }

    return (
        <BrowserRouter basename={process.env.PUBLIC_URL}>
            <div className='dark-theme'>
                <Navbar bg="dark" variant="dark" expand="lg">
                    <Navbar.Brand href="/">Заказы</Navbar.Brand>
                    <Navbar.Toggle aria-controls="basic-navbar-nav"/>
                    <Navbar.Collapse id="basic-navbar-nav">
                        <Nav className="mr-auto">
                            {token ? (
                            <Nav.Item>
                                <Nav.Link as={Link} to="/" className="nav-link">Все заказы</Nav.Link>
                            </Nav.Item> ) : null }
                            {is_admin ? (
                                <>
                                    <Nav.Item>
                                        <Nav.Link as={Link} to="/users" className="nav-link">Пользователи</Nav.Link>
                                    </Nav.Item>
                                    <Nav.Item>
                                        <Nav.Link as={Link} to="/cities" className="nav-link">Справочник городов</Nav.Link>
                                    </Nav.Item>

                                </>
                            ) : null}
                        </Nav>
                        <Nav>
                            {token ? (
                                <Nav.Item>
                                    <Button variant="outline-secondary" onClick={logout}>Выход</Button>
                                </Nav.Item>
                            ) : null}
                        </Nav>
                    </Navbar.Collapse>
                </Navbar>
                <Container>
                    <Routes>
                        <Route path="/" element={token ? <Main/> : <LoginForm/>}/>
                        <Route path="/login" element={token ? <Navigate to="/"/> : <LoginForm/>}/>
                        <Route path="/registration" element={token ? <Navigate to="/"/> : <RegisterForm/>}/>
                        {is_admin ? <Route path="/users" element={<UserTable />} /> : null}
                        {is_admin ? <Route path="/cities" element={<CityTable />} /> : null}
                        <Route path="*" element={<Navigate to="/"/>}/>
                    </Routes>
                </Container>
            </div>
        </BrowserRouter>
    );
}

export default App;
