import React, { useState } from "react";
import axios from "axios";

export default function KPIEntryForm() {
  const [selectedMonth, setSelectedMonth] = useState("2025-06");
  const [entriesByMonth, setEntriesByMonth] = useState({
    "2025-06": [
      {
        customer_id: 1,
        customer_name: "Unilever",
        product_id: 1,
        product_name: "Product A",
        supplier_id: 1,
        supplier_name: "Supplier A",
        uom: "Kg",
        month: "2025-06-01",
        quantity: 2,
        asp: 4,
        total_value: 8
      },
      {
        customer_id: 2,
        customer_name: "Nestle",
        product_id: 2,
        product_name: "Product B",
        supplier_id: 2,
        supplier_name: "Supplier B",
        uom: "Ltr",
        month: "2025-06-01",
        quantity: 3,
        asp: 5,
        total_value: 15
      }
    ]
  });

  const currentEntries = entriesByMonth[selectedMonth] || [];

  const handleMonthChange = (e) => {
    const monthValue = e.target.value;
    setSelectedMonth(monthValue);
    if (!entriesByMonth[monthValue]) {
      setEntriesByMonth((prev) => ({
        ...prev,
        [monthValue]: []
      }));
    }
  };

  const handleChange = (index, field, value) => {
    const updated = [...currentEntries];
    updated[index][field] = field === "quantity" || field === "asp" ? parseFloat(value) : value;
    updated[index].total_value = updated[index].quantity * updated[index].asp;
    updated[index].month = selectedMonth + "-01";
    setEntriesByMonth((prev) => ({
      ...prev,
      [selectedMonth]: updated
    }));
  };

  const handleSubmit = async () => {
    try {
      const payload = currentEntries.map(({ customer_name, product_name, supplier_name, ...e }) => e);
      await axios.post("/api/kpi-entries", { entries: payload });
      alert("KPI entries created successfully");
    } catch (error) {
      console.error(error);
      alert("Error saving KPI entries");
    }
  };

  const handleAddRow = () => {
    const newEntry = {
      customer_id: 0,
      customer_name: "",
      product_id: 0,
      product_name: "",
      supplier_id: 0,
      supplier_name: "",
      uom: "",
      month: selectedMonth + "-01",
      quantity: 0,
      asp: 0,
      total_value: 0
    };
    setEntriesByMonth((prev) => ({
      ...prev,
      [selectedMonth]: [...currentEntries, newEntry]
    }));
  };

  return (
    <div className="p-4">
      <h2 className="text-xl font-bold text-orange-600 mb-4">KPI Entry Form</h2>

      <div className="mb-4 flex gap-4 items-center">
        <label className="font-medium">Select Month:</label>
        <input
          type="month"
          value={selectedMonth}
          onChange={handleMonthChange}
          className="border px-2 py-1"
        />
        <button
          className="bg-blue-600 text-white px-3 py-1 rounded"
          onClick={handleAddRow}
        >
          + Add Row
        </button>
      </div>

      <table className="w-full border text-sm">
        <thead className="bg-gray-100">
          <tr>
            <th>Month</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Supplier</th>
            <th>UOM</th>
            <th>Quantity</th>
            <th>ASP</th>
            <th>Total Value</th>
          </tr>
        </thead>
        <tbody>
          {currentEntries.map((entry, i) => (
            <tr key={i} className="text-center border-b">
              <td>{new Date(entry.month).toLocaleDateString()}</td>
              <td>{entry.customer_name}</td>
              <td>{entry.product_name}</td>
              <td>{entry.supplier_name}</td>
              <td>
                <select
                  value={entry.uom}
                  onChange={(e) => handleChange(i, "uom", e.target.value)}
                  className="border px-2 py-1"
                >
                  <option value="">Select</option>
                  <option value="Kg">Kg</option>
                  <option value="Ltr">Ltr</option>
                  <option value="Pcs">Pcs</option>
                </select>
              </td>
              <td>
                <input
                  type="number"
                  value={entry.quantity}
                  onChange={(e) => handleChange(i, "quantity", e.target.value)}
                  className="border px-2 py-1 w-16"
                />
              </td>
              <td>
                <input
                  type="number"
                  value={entry.asp}
                  onChange={(e) => handleChange(i, "asp", e.target.value)}
                  className="border px-2 py-1 w-16"
                />
              </td>
              <td>{entry.total_value}</td>
            </tr>
          ))}
        </tbody>
      </table>

      <div className="flex justify-end mt-4 gap-2">
        <button
          className="bg-gray-200 px-4 py-2 rounded"
          onClick={() => window.location.reload()}
        >
          Cancel
        </button>
        <button
          className="bg-green-600 text-white px-4 py-2 rounded"
          onClick={handleSubmit}
        >
          Save
        </button>
      </div>
    </div>
  );
}
