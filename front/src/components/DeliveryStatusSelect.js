import React, {useState} from 'react';

const DeliveryStatusSelect = ({ value, onChange }) => {
    const [selectedStatus, setSelectedStatus] = useState(value);

    const handleStatusChange = (event) => {
        const newStatus = event.target.value;
        setSelectedStatus(newStatus);
        onChange(newStatus);
    };

    return (
        <select value={selectedStatus} onChange={handleStatusChange}>
            <option value="pending">Pending</option>
            <option value="rejected">Rejected</option>
            <option value="approved">Approved</option>
        </select>
    );
};

export default DeliveryStatusSelect;