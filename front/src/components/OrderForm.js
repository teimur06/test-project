import React, { useState } from 'react';
import { Form, Row, Col, Button } from 'react-bootstrap';

const OrderForm = ({ cities, onAddOrder}) => {
    const [fromCity, setFromCity] = useState('');
    const [toCity, setToCity] = useState('');
    const [deliveryDate, setDeliveryDate] = useState('');


    const handleSubmit = (event) => {
        event.preventDefault();
        if (fromCity && toCity && deliveryDate) {
            onAddOrder(fromCity, toCity, deliveryDate);
        }
    };

    return (
        <Form className="mt-5" onSubmit={handleSubmit}>
            <Row className="align-items-end">
                <Col>
                    <Form.Label htmlFor="fromCity">Откуда:</Form.Label>
                    <Form.Select
                        id="fromCity"
                        value={fromCity}
                        onChange={(event) => setFromCity(event.target.value)}
                    >
                        <option value="">Выберите город отправления</option>
                        {cities.map((city) => (
                            <option key={city.id} value={city.id}>
                                {city.name}
                            </option>
                        ))}
                    </Form.Select>
                </Col>
                <Col>
                    <Form.Label htmlFor="toCity">Куда:</Form.Label>
                    <Form.Select
                        id="toCity"
                        value={toCity}
                        onChange={(event) => setToCity(event.target.value)}
                    >
                        <option value="">Выберите город назначения</option>
                        {cities.map((city) => (
                            <option key={city.id} value={city.id}>
                                {city.name}
                            </option>
                        ))}
                    </Form.Select>
                </Col>
                <Col>
                    <Form.Label htmlFor="deliveryDate">Дата доставки:</Form.Label>
                    <Form.Control
                        id="deliveryDate"
                        type="date"
                        value={deliveryDate}
                        onChange={(event) => setDeliveryDate(event.target.value)}
                    />
                </Col>
                <Col>
                    <Button variant="primary" type="submit">
                        Добавить заказ
                    </Button>
                </Col>
            </Row>
        </Form>
    );
};

export default OrderForm;