import React, {useState, useEffect, useRef} from 'react';
import {Form, Row, Col, Button, Table} from 'react-bootstrap';
import {useSelector} from 'react-redux';

const CityTable = () => {
    const twiceEffect = useRef(false);
    const [cities, setCities] = useState([]);
    const [newCityName, setNewCityName] = useState('');
    const token = useSelector(state => state.token);

    useEffect(() => {
        if (twiceEffect.current === false) {
            const fetchData = async () => {
                const requestOptions = {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                };
                const response = await fetch(`${process.env.REACT_APP_BACKEND_API_URL}/cities`, requestOptions);
                const data = await response.json();
                setCities(data.cities);
                console.log(data);
            };
            fetchData();
        }
        return () => {twiceEffect.current = true}
    }, [token]);

    const handleDelete = (id) => {
        const requestOptions = {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            },
        };
        fetch(`${process.env.REACT_APP_BACKEND_API_URL}/cities/${id}`, requestOptions)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                setCities(prevCities => prevCities.filter(city => city.id !== id));
            });
    };

    const handleAdd = (event) => {
        event.preventDefault();
        const requestOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({name: newCityName})
        };
        fetch(`${process.env.REACT_APP_BACKEND_API_URL}/cities`, requestOptions)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log(data);
                    setCities(prevCities => [...prevCities, data.city]);
                    setNewCityName('');
                }
            });
    };

    return (
        <>
            <Table striped bordered hover>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Город</th>
                    <th>Удалить</th>
                </tr>
                </thead>
                <tbody>
                {cities.map(city => (
                    <tr key={city.id}>
                        <td>{city.id}</td>
                        <td>{city.name}</td>
                        <td>
                            <Button variant="danger" onClick={() => handleDelete(city.id)}>Удалить</Button>
                        </td>
                    </tr>
                ))}
                </tbody>
            </Table>
            <Form onSubmit={handleAdd}>
                <Row className="align-items-end mb-3">
                    <Col>
                        <Form.Group>
                            <Form.Control type="text" placeholder="Введите название города" value={newCityName}
                                          onChange={(event) => setNewCityName(event.target.value)}/>
                        </Form.Group>
                    </Col>
                    <Col>
                        <Button variant="primary" type="submit">Добавить</Button>
                    </Col>
                </Row>
            </Form>
        </>
    );
};

export default CityTable;
