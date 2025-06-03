import React, { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";
import {
  fetchOptions,
  fetchCustomerProducts,
  fetchKpiData,
  createBulkKpi,
  fetchSuppliers,
  assignSupplierToProducts,
} from "../redux/store";

const KPISetupForm = () => {
  const dispatch = useDispatch();
  const { options, customerProducts, loading, kpiData } = useSelector(
    (state) => state.app
  );

  const [formData, setFormData] = useState({
    unit: "",
    customer: "",
    month: new Date().toISOString().split("T")[0],
  });
  const [selectedProducts, setSelectedProducts] = useState([]);
  const [assignedSuppliers, setAssignedSuppliers] = useState({});
  const [formErrors, setFormErrors] = useState({});
  const [successMessage, setSuccessMessage] = useState("");

  useEffect(() => {
    dispatch(fetchOptions());
    dispatch(fetchKpiData());
    dispatch(fetchSuppliers());
  }, [dispatch]);

  useEffect(() => {
    dispatch(fetchCustomerProducts(formData.customer || null));
  }, [dispatch, formData.customer]);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setSelectedProducts("");
    setFormData({ ...formData, [name]: value });
    if (formErrors[name]) setFormErrors({ ...formErrors, [name]: "" });
  };

  const handleProductSelect = (product) => {
    if (selectedProducts.includes(product)) {
      setSelectedProducts(selectedProducts.filter((p) => p !== product));
      const updated = { ...assignedSuppliers };
      delete updated[product];
      setAssignedSuppliers(updated);
    } else {
      setSelectedProducts([...selectedProducts, product]);
    }
  };

  const handleSupplierAssignment = async (productId, supplierId) => {
    try {
      await dispatch(assignSupplierToProducts({
        supplier_ids: [supplierId],
        product_id: productId
      })).unwrap();
      
      setAssignedSuppliers({ ...assignedSuppliers, [productId]: supplierId });
      setSuccessMessage("Supplier assigned successfully!");
      setTimeout(() => setSuccessMessage(""), 3000);
    } catch (err) {
      console.error("Supplier assignment failed:", err);
      setFormErrors({
        submit: "Failed to assign supplier. Please try again." // Error message is not specific to the product
      });
    }
};

  const validateForm = () => {
    const errors = {};
    const fields = [
      "unit",
      "customer",
      "month",
    ];
    fields.forEach((f) => {
      if (!formData[f]) errors[f] = `${f} is required`;
    });
    if (selectedProducts.length === 0)
      errors.products = "At least one product must be selected";
    selectedProducts.forEach((p) => {
      if (!assignedSuppliers[p])
        errors.suppliers = `Supplier not assigned for ${p}`;
    });
    setFormErrors(errors);
    return Object.keys(errors).length === 0;
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    console.log('submit');
    
    if (!validateForm()) return;

    const kpiRecords = selectedProducts.map((product) => ({
      month: new Date(formData.month).toISOString().split("T")[0],
      customer_id: formData.customer,
      product_id: product,
      supplier_id: assignedSuppliers[product],
      uom: formData.unit,
      quantity: 0,
      asp: 0,
      total_value: 0,
    }));

    try {
      await dispatch(createBulkKpi(kpiRecords)).unwrap();
      setFormData({
        unit: "",
        customer: "",
        month: new Date().toISOString().split("T")[0],
      });
      setSelectedProducts([]);
      setAssignedSuppliers({});
      setSuccessMessage("KPI setup created successfully!");
      setTimeout(() => setSuccessMessage(""), 3000);
    } catch (err) {
      console.error("Creation failed:", err);
      setFormErrors({
        submit: "Failed to create KPI records. Please try again.",
      });
    }
  };

  const activeKpis = kpiData.length;
  const uniqueCustomers = new Set(kpiData.map((k) => k.customer)).size;
  const uniqueProducts = new Set(kpiData.map((k) => k.product)).size;

  return (
    <div className="bg-white rounded-xl shadow-md p-6">
      <h2 className="text-2xl font-bold text-gray-800 mb-6">KPI Setup</h2>

      {loading ? (
        <div className="flex flex-col items-center justify-center py-10">
          <div className="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
          <p className="mt-4 text-gray-600">Loading options...</p>
        </div>
      ) : (
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Unit of Measure (UOM)
              </label>
              <select
                name="unit"
                value={formData.unit}
                onChange={handleInputChange}
                className={`w-full p-2 border rounded-md ${
                  formErrors.unit ? "border-red-500" : "border-gray-300"
                }`}
              >
                <option value="">Select unit</option>
                {options.units.map((opt) => (
                  <option key={opt} value={opt}>
                    {opt}
                  </option>
                ))}
              </select>
              {formErrors.unit && (
                <p className="mt-1 text-sm text-red-600">{formErrors.unit}</p>
              )}
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Customer
              </label>
              <select
                name="customer"
                value={formData.customer}
                onChange={handleInputChange}
                className={`w-full p-2 border rounded-md ${
                  formErrors.customer ? "border-red-500" : "border-gray-300"
                }`}
              >
                <option value="">Select Customer</option>
                {Array.isArray(options?.customers?.data) &&
                options.customers.data.length > 0 ? (
                  options.customers.data.map((customer) => (
                    <option key={customer.id} value={customer.id}>
                      {customer.name}
                    </option>
                  ))
                ) : (
                  <option disabled>No customers available</option>
                )}
              </select>
              {formErrors.customer && (
                <p className="mt-1 text-sm text-red-600">
                  {formErrors.customer}
                </p>
              )}
            </div>
          </div>

          <div className="mt-4">
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Assigned Products
            </label>
            {formData.customer ? (
              Array.isArray(customerProducts?.data) &&
              customerProducts.data.length > 0 ? (
                <div className="flex flex-wrap gap-2">
                  {customerProducts.data.map((product) => (
                    <button
                      key={product.id}
                      type="button"
                      onClick={() => handleProductSelect(product.id)}
                      className={`px-3 py-1 rounded-full text-sm font-medium ${
                        selectedProducts.includes(product.id)
                          ? "bg-blue-500 text-white"
                          : "bg-gray-200 text-gray-700 hover:bg-gray-300"
                      }`}
                    >
                      {product.name}
                    </button>
                  ))}
                </div>
              ) : (
                <p className="text-gray-500 italic">
                  No products assigned to this customer
                </p>
              )
            ) : (
              <p className="text-gray-500 italic">
                Select a customer to view assigned products
              </p>
            )}
            {formErrors.products && (
              <p className="mt-1 text-sm text-red-600">{formErrors.products}</p>
            )}
          </div>

          {selectedProducts.length > 0 && (
            <div className="bg-gray-50 rounded-lg p-4">
              <h3 className="text-lg font-medium text-gray-800 mb-3">
                Supplier Assignment
              </h3>
              <p className="text-gray-600 mb-4">
                Assign a supplier to each selected product:
              </p>
              <div className="space-y-3">
                {selectedProducts.map((productId) => {
                  const productDetails = customerProducts.data.find(
                    (p) => p.id === productId
                  );
                  return (
                    <div key={productId} className="flex items-center space-x-2">
                      <span className="w-32 font-medium text-gray-700">
                        {productDetails?.name || productId}
                        <span className="block text-xs text-gray-500">
                          Code: {productDetails?.code}
                        </span>
                      </span>
                      <div className="flex-1 flex space-x-2">
                        <select
                          value={assignedSuppliers[productId] || ""}
                          onChange={(e) => setAssignedSuppliers({ 
                            ...assignedSuppliers, 
                            [productId]: e.target.value 
                          })}
                          className={`flex-1 p-2 border rounded-md ${
                            !assignedSuppliers[productId]
                              ? "border-red-500"
                              : "border-gray-300"
                          }`}
                        >
                          <option value="">Select Supplier</option>
                          {options.allSuppliers.map((supplier) => (
                            <option key={supplier.id} value={supplier.id}>
                              {supplier.name}
                            </option>
                          ))}
                        </select>
                        <button
                          type="button"
                          onClick={() => handleSupplierAssignment(productId, assignedSuppliers[productId])}
                          disabled={!assignedSuppliers[productId]}
                          className={`px-4 py-2 rounded-md ${
                            assignedSuppliers[productId]
                              ? "bg-blue-600 text-white hover:bg-blue-700"
                              : "bg-gray-300 text-gray-500 cursor-not-allowed"
                          }`}
                        >
                          Assign
                        </button>
                      </div>
                    </div>
                  );
                })}
              </div>
              {formErrors.suppliers && (
                <p className="mt-2 text-sm text-red-600">
                  {formErrors.suppliers}
                </p>
              )}
            </div>
          )}

          <div className="flex justify-end">
            <button
              type="submit"
              className="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700"
            >
              Create KPI Setup
            </button>
          </div>
          {successMessage && (
            <div className="mt-4 p-3 bg-green-100 text-green-700 rounded-md">
              {successMessage}
            </div>
          )}
          {formErrors.submit && (
            <div className="mt-4 p-3 bg-red-100 text-red-700 rounded-md">
              {formErrors.submit}
            </div>
          )}
        </form>
      )}

      <div className="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div className="bg-blue-50 rounded-lg p-4 text-center">
          <p className="text-3xl font-bold text-blue-600">{activeKpis}</p>
          <p className="text-gray-600">Active KPIs</p>
        </div>
        <div className="bg-green-50 rounded-lg p-4 text-center">
          <p className="text-3xl font-bold text-green-600">{uniqueCustomers}</p>
          <p className="text-gray-600">Customers</p>
        </div>
        <div className="bg-purple-50 rounded-lg p-4 text-center">
          <p className="text-3xl font-bold text-purple-600">{uniqueProducts}</p>
          <p className="text-gray-600">Products</p>
        </div>
      </div>
    </div>
  );
};

export default KPISetupForm;
