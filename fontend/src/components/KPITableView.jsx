// src/components/KPITableView.jsx
import React, { useState } from 'react';
import axios from 'axios';

const KPITableView = ({ kpiData, deletedRecords, onUpdateKPI, onSoftDelete, onRestore }) => {
  const [showDeleted, setShowDeleted] = useState(false);
  const [editingId, setEditingId] = useState(null);
  const [editForm, setEditForm] = useState({});
  const [groupedData, setGroupedData] = useState({});

  // Group data by month+customer+product
  React.useEffect(() => {
    const grouped = kpiData.reduce((acc, item) => {
      const key = `${item.month}-${item.customer}-${item.product}`;
      if (!acc[key]) acc[key] = [];
      acc[key].push(item);
      return acc;
    }, {});
    
    setGroupedData(grouped);
  }, [kpiData]);

  const handleEdit = (item) => {
    setEditingId(item.id);
    setEditForm({
      uom: item.uom,
      quantity: item.quantity,
      asp: item.asp
    });
  };

  const handleEditChange = (e) => {
    const { name, value } = e.target;
    setEditForm({
      ...editForm,
      [name]: name === 'quantity' || name === 'asp' ? 
        (value === '' ? '' : parseFloat(value)) : 
        value
    });
  };

  const handleSave = (id) => {
    const updatedKPI = kpiData.find(item => item.id === id);
    if (updatedKPI) {
      onUpdateKPI({
        ...updatedKPI,
        ...editForm
      });
    }
    setEditingId(null);
  };

  const handleCancelEdit = () => {
    setEditingId(null);
  };

  return (
    <div className="kpi-table-view">
      <div className="view-controls">
        <h2>KPI Table View</h2>
        <div className="toggle-deleted">
          <label>
            <input 
              type="checkbox" 
              checked={showDeleted} 
              onChange={() => setShowDeleted(!showDeleted)} 
            />
            Show Deleted Records
          </label>
        </div>
      </div>

      <div className="table-container">
        <table className="kpi-table">
          <thead>
            <tr>
              <th>Month</th>
              <th>Customer</th>
              <th>Product</th>
              <th>Supplier</th>
              <th>UOM</th>
              <th>Quantity</th>
              <th>ASP</th>
              <th>Total Value</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            {Object.entries(groupedData).map(([key, items]) => (
              items.map((item, index) => (
                <tr key={item.id} className={item.isDeleted ? 'deleted' : ''}>
                  {index === 0 && (
                    <>
                      <td rowSpan={items.length}>{item.month}</td>
                      <td rowSpan={items.length}>{item.customer}</td>
                      <td rowSpan={items.length}>{item.product}</td>
                    </>
                  )}
                  <td>{item.supplier}</td>
                  
                  {editingId === item.id ? (
                    <>
                      <td>
                        <select 
                          name="uom"
                          value={editForm.uom}
                          onChange={handleEditChange}
                        >
                          <option value="Units">Units</option>
                          <option value="Cases">Cases</option>
                          <option value="Pallets">Pallets</option>
                          <option value="Kg">Kg</option>
                          <option value="Liters">Liters</option>
                        </select>
                      </td>
                      <td>
                        <input
                          type="number"
                          name="quantity"
                          value={editForm.quantity}
                          onChange={handleEditChange}
                          min="0"
                          step="1"
                        />
                      </td>
                      <td>
                        <input
                          type="number"
                          name="asp"
                          value={editForm.asp}
                          onChange={handleEditChange}
                          min="0"
                          step="0.01"
                        />
                      </td>
                      <td>{(editForm.quantity * editForm.asp).toFixed(2)}</td>
                      <td className="actions">
                        <button 
                          className="save-btn"
                          onClick={() => handleSave(item.id)}
                        >
                          Save
                        </button>
                        <button 
                          className="cancel-btn"
                          onClick={handleCancelEdit}
                        >
                          Cancel
                        </button>
                      </td>
                    </>
                  ) : (
                    <>
                      <td>{item.uom}</td>
                      <td>{item.quantity}</td>
                      <td>{item.asp.toFixed(2)}</td>
                      <td>{item.totalValue.toFixed(2)}</td>
                      <td className="actions">
                        <button 
                          className="edit-btn"
                          onClick={() => handleEdit(item)}
                        >
                          Edit
                        </button>
                        {!item.isDeleted && (
                          <button 
                            className="delete-btn"
                            onClick={() => onSoftDelete(item.id)}
                          >
                            Delete
                          </button>
                        )}
                      </td>
                    </>
                  )}
                </tr>
              ))
            ))}
          </tbody>
        </table>
      </div>

      {showDeleted && deletedRecords.length > 0 && (
        <div className="deleted-records">
          <h3>Deleted Records</h3>
          <table className="deleted-table">
            <thead>
              <tr>
                <th>Month</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Supplier</th>
                <th>UOM</th>
                <th>Quantity</th>
                <th>ASP</th>
                <th>Total Value</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              {deletedRecords.map(item => (
                <tr key={item.id} className="deleted">
                  <td>{item.month}</td>
                  <td>{item.customer}</td>
                  <td>{item.product}</td>
                  <td>{item.supplier}</td>
                  <td>{item.uom}</td>
                  <td>{item.quantity}</td>
                  <td>{item.asp.toFixed(2)}</td>
                  <td>{item.totalValue.toFixed(2)}</td>
                  <td>
                    <button 
                      className="restore-btn"
                      onClick={() => onRestore(item.id)}
                    >
                      Restore
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      {kpiData.length === 0 && !showDeleted && (
        <div className="no-data">
          <p>No KPI records found. Create some in the KPI Setup tab.</p>
        </div>
      )}

      {showDeleted && deletedRecords.length === 0 && (
        <div className="no-data">
          <p>No deleted records found.</p>
        </div>
      )}
    </div>
  );
};

export default KPITableView;