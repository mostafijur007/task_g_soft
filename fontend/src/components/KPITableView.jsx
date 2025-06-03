// KPITableView.jsx
import React, { useState, useEffect } from 'react';
import { useDispatch, useSelector } from 'react-redux';
import {
  fetchKpiData,
  updateKpiRecord,
  softDeleteRecord,
  restoreRecord
} from '../redux/store';

const KPITableView = () => {
  const dispatch = useDispatch();
  const { kpiData, deletedRecords, loading } = useSelector(state => state.app);
  
  const [showDeleted, setShowDeleted] = useState(false);
  const [editingId, setEditingId] = useState(null);
  const [editForm, setEditForm] = useState({});
  const [groupedData, setGroupedData] = useState({});

  useEffect(() => {
    dispatch(fetchKpiData());
  }, [dispatch]);

  useEffect(() => {
    
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
    setEditForm({ uom: item.uom, quantity: item.quantity, asp: item.asp });
  };

  const handleEditChange = (e) => {
    const { name, value } = e.target;
    setEditForm({
      ...editForm,
      [name]: name === 'quantity' || name === 'asp' ? (value === '' ? '' : parseFloat(value)) : value
    });
  };

  const handleSave = async (id) => {
    try {
      await dispatch(updateKpiRecord({ id, data: editForm })).unwrap();
      setEditingId(null);
    } catch (error) {
      console.error('Failed to update record:', error);
    }
  };

  const handleCancelEdit = () => setEditingId(null);
  const handleSoftDelete = (id) => dispatch(softDeleteRecord(id));
  const handleRestore = (id) => dispatch(restoreRecord(id));

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
  };

  return (
    <div className="bg-white rounded-xl shadow-md p-6">
      <div className="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h2 className="text-2xl font-bold text-gray-800">KPI Table View</h2>
        <label className="flex items-center space-x-2 mt-4 md:mt-0">
          <input type="checkbox" checked={showDeleted} onChange={() => setShowDeleted(!showDeleted)} className="h-4 w-4 text-blue-600 rounded" />
          <span className="text-gray-700">Show Deleted Records</span>
        </label>
      </div>

      {loading ? (
        <div className="flex justify-center py-10">
          <div className="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500"></div>
        </div>
      ) : (
        <>
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  {['Month', 'Customer', 'Product', 'Supplier', 'UOM', 'Quantity', 'ASP', 'Total Value', 'Actions'].map(h => (
                    <th key={h} className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{h}</th>
                  ))}
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {Object.entries(groupedData).map(([key, items]) => (
                  items.map((item, index) => (
                    <tr key={item.id} className={item.deleted_at ? 'bg-red-50' : ''}>
                      {index === 0 && [
                        <td key="month" rowSpan={items.length} className="px-6 py-4 text-sm font-medium text-gray-900 align-top">{formatDate(item.month)}</td>,
                        <td key="customer" rowSpan={items.length} className="px-6 py-4 text-sm text-gray-500 align-top">{item.customer?.name || item.customer}</td>,
                        <td key="product" rowSpan={items.length} className="px-6 py-4 text-sm text-gray-500 align-top">{item.product?.name || item.product}</td>
                      ]}
                      <td className="px-6 py-4 text-sm text-gray-500">{item.supplier?.name || item.supplier}</td>
                      {editingId === item.id ? (
                        <>
                          <td className="px-6 py-4">
                            <select name="uom" value={editForm.uom} onChange={handleEditChange} className="w-full p-1 border border-gray-300 rounded-md">
                              {['Units', 'Cases', 'Pallets', 'Kg', 'Liters'].map(unit => <option key={unit} value={unit}>{unit}</option>)}
                            </select>
                          </td>
                          <td className="px-6 py-4">
                            <input type="number" name="quantity" value={editForm.quantity} onChange={handleEditChange} min="0" step="1" className="w-full p-1 border border-gray-300 rounded-md" />
                          </td>
                          <td className="px-6 py-4">
                            <input type="number" name="asp" value={editForm.asp} onChange={handleEditChange} min="0" step="0.01" className="w-full p-1 border border-gray-300 rounded-md" />
                          </td>
                          <td className="px-6 py-4 text-sm text-gray-500">{(editForm.quantity * editForm.asp).toFixed(2)}</td>
                          <td className="px-6 py-4 text-sm font-medium">
                            <div className="flex space-x-2">
                              <button onClick={() => handleSave(item.id)} className="text-green-600 hover:text-green-900">Save</button>
                              <button onClick={handleCancelEdit} className="text-gray-600 hover:text-gray-900">Cancel</button>
                            </div>
                          </td>
                        </>
                      ) : (
                        <>
                          <td className="px-6 py-4 text-sm text-gray-500">{item.uom}</td>
                          <td className="px-6 py-4 text-sm text-gray-500">{item.quantity}</td>
                          <td className="px-6 py-4 text-sm text-gray-500">{item.asp}</td>
                          <td className="px-6 py-4 text-sm text-gray-500">{(item.quantity * item.asp)}</td>
                          <td className="px-6 py-4 text-sm font-medium">
                            {!item.deleted_at && (
                              <div className="flex space-x-3">
                                <button onClick={() => handleEdit(item)} className="text-blue-600 hover:text-blue-900">Edit</button>
                                <button onClick={() => handleSoftDelete(item.id)} className="text-red-600 hover:text-red-900">Delete</button>
                              </div>
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

          {Object.keys(groupedData).length === 0 && (
            <div className="py-12 text-center">
              <p className="text-gray-500">No KPI records found. Create some in the KPI Setup tab.</p>
            </div>
          )}

          {showDeleted && (
            <div className="mt-10">
              <h3 className="text-lg font-medium text-gray-900 mb-4">Deleted Records</h3>
              {deletedRecords.length > 0 ? (
                <div className="overflow-x-auto">
                  <table className="min-w-full divide-y divide-gray-200">
                    <thead className="bg-gray-50">
                      <tr>
                        {['Month', 'Customer', 'Product', 'Supplier', 'UOM', 'Quantity', 'ASP', 'Total Value', 'Actions'].map(h => (
                          <th key={h} className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{h}</th>
                        ))}
                      </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                      {deletedRecords.map(item => (
                        <tr key={item.id} className="bg-red-50">
                          <td className="px-6 py-4 text-sm text-gray-500">{formatDate(item.month)}</td>
                          <td className="px-6 py-4 text-sm text-gray-500">{item.customer}</td>
                          <td className="px-6 py-4 text-sm text-gray-500">{item.product}</td>
                          <td className="px-6 py-4 text-sm text-gray-500">{item.supplier}</td>
                          <td className="px-6 py-4 text-sm text-gray-500">{item.uom}</td>
                          <td className="px-6 py-4 text-sm text-gray-500">{item.quantity}</td>
                          <td className="px-6 py-4 text-sm text-gray-500">{item.asp.toFixed(2)}</td>
                          <td className="px-6 py-4 text-sm text-gray-500">{(item.quantity * item.asp).toFixed(2)}</td>
                          <td className="px-6 py-4 text-sm font-medium">
                            <button onClick={() => handleRestore(item.id)} className="text-green-600 hover:text-green-900">Restore</button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              ) : (
                <div className="py-8 text-center">
                  <p className="text-gray-500">No deleted records found.</p>
                </div>
              )}
            </div>
          )}
        </>
      )}
    </div>
  );
};

export default KPITableView;
