import React, {useEffect, useRef, useState} from 'react';
import {Button} from 'react-bootstrap';
import { useSelector} from 'react-redux';
import DeliveryStatusSelect from './DeliveryStatusSelect';
import OrderForm from './OrderForm';


const Main = () => {
    const twiceEffect1 = useRef(false);
    const twiceEffect2 = useRef(false);

    const token = useSelector(state => state.token);
    const is_admin = useSelector(state => state.is_admin);

    const [deliveryRequest, setDeliveryRequest] = useState([]);
    const [cities, setCities] = useState([]);
    const requestOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
        },
    };

    useEffect(() => {

        if (twiceEffect1.current === false) {
            async function fetchData() {
                const response = await fetch(`${process.env.REACT_APP_BACKEND_API_URL}/cities`, requestOptions);
                const data = await response.json();
                console.log(data);
                setCities(data.cities);
            }

            fetchData();
        }

        return () => {twiceEffect1.current = true};
    });


    useEffect(() => {
        if (twiceEffect2.current === false) {
            async function fetchData() {
                const response = await fetch(`${process.env.REACT_APP_BACKEND_API_URL}/delivery`, requestOptions);

                const data = await response.json();
                console.log(data);
                setDeliveryRequest(data);
            }

            fetchData();
        }
        return () => {twiceEffect2.current = true};
    });


    const handleStatusChange = (id, status) => {
        const newRequestOptions = {
            ...requestOptions,
            method: 'PUT',
            body: JSON.stringify({status})
        }


        fetch(`${process.env.REACT_APP_BACKEND_API_URL}/delivery/${id}`, newRequestOptions)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                // Обновляем данные в локальном состоянии
                setDeliveryRequest(prevData => {
                    const newData = [...prevData];
                    const index = newData.findIndex(item => item.id === id);
                    if (index !== -1) {
                        newData[index] = {...newData[index], status};
                    }
                    return newData;
                });
            });
    };


    const handleAddOrder = async (fromCityId, toCityId, deliveryDate) => {
        const requestOptions = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({from_city_id: fromCityId, to_city_id: toCityId, delivery_date: deliveryDate})
        };

        try {
            const response = await fetch(`${process.env.REACT_APP_BACKEND_API_URL}/delivery`, requestOptions);
            const newOrder = await response.json();

            if (newOrder.success)
                setDeliveryRequest(prevData => [...prevData, newOrder['deliveryRequest']]);
            else console.log(newOrder);
        } catch (e) {
            console.log(e);
        }
    };

    const handleDeleteOrder = async (id) => {
        const newRequestOptions = {
            ...requestOptions,
            method: 'DELETE'
        }

        fetch(`${process.env.REACT_APP_BACKEND_API_URL}/delivery/${id}`, newRequestOptions)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                setDeliveryRequest(prevData => prevData.filter(item => item.id !== id));
            });
    };

    return (
        <>
            <table className="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Пользователь</th>
                    <th>Откуда</th>
                    <th>Куда</th>
                    <th>Дата доставки</th>
                    <th>Статус</th>
                    <th>Группа</th>
                    {is_admin && <th>Удалить</th>}
                </tr>
                </thead>
                <tbody>
                {deliveryRequest.map((item, index) => (
                    <tr key={index}>
                        <td>{item.id}</td>
                        <td>{item.user_name}</td>
                        <td>{item.from_city_name}</td>
                        <td>{item.to_city_name}</td>
                        <td>{item.delivery_date}</td>
                        <td>
                            {is_admin ? (
                                <DeliveryStatusSelect
                                    value={item.status}
                                    onChange={(newStatus) =>
                                        handleStatusChange(item.id, newStatus)
                                    }
                                />
                            ) : (
                                item.status
                            )}
                        </td>
                        <td>{item.group}</td>
                        {is_admin && (
                            <td>
                                <Button variant="danger" onClick={() => handleDeleteOrder(item.id)}>Удалить</Button>
                            </td>
                        )}
                    </tr>
                ))}
                </tbody>
            </table>
            <OrderForm cities={cities} onAddOrder={handleAddOrder}/>
        </>
    );
};

export default Main;