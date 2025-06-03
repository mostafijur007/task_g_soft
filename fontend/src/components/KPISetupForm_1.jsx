// src/components/KPISetupForm.jsx
import React, { useState, useEffect } from 'react';
import axios from 'axios';

const KPISetupForm = ({ onAddKPI, kpiData }) => {
  const [formData, setFormData] = useState({
    unit: '',
    material: '',
    itemGroup: '',
    item: '',
    customer: '',
    products: [],
    suppliers: {}
  });

  const [options, setOptions] = useState({
    units: [],
    materials: [],
    itemGroups: [],
    items: [],
    customers: [],
    allProducts: [],
    allSuppliers: []
  });

  const [customerProducts, setCustomerProducts] = useState([]);
  const [selectedProducts, setSelectedProducts] = useState([]);
  const [assignedSuppliers, setAssignedSuppliers] = useState({});
  const [isLoading, setIsLoading] = useState(true);
  const [formErrors, setFormErrors] = useState({});
  const [successMessage, setSuccessMessage] = useState('');

  // Fetch dropdown options
  useEffect(() => {
    const fetchOptions = async () => {
      try {
        setIsLoading(true);
        
        // In a real app, these would be API calls
        // Simulating API responses with timeouts
        setTimeout(() => {
          setOptions({
            units: ['Units', 'Cases', 'Pallets', 'Kg', 'Liters'],
            customers: ['Unilever', 'Nestle', 'P&G', 'Coca-Cola', 'PepsiCo'],
            allProducts: ['Product A', 'Product B', 'Product C', 'Product D', 'Product E'],
            allSuppliers: ['Supplier A', 'Supplier B', 'Supplier C', 'Supplier D', 'Supplier E']
          });
          setIsLoading(false);
        }, 1000);
      } catch (error) {
        console.error('Error fetching options:', error);
        setIsLoading(false);
      }
    };

    fetchOptions();
  }, []);

  // Get products for selected customer
  useEffect(() => {
    if (formData.customer) {
      // In a real app, this would be an API call to get customer's products
      // For demo, we'll simulate with the first 3 products
      const customerProducts = options.allProducts.slice(0, 3);
      setCustomerProducts(customerProducts);
    } else {
      setCustomerProducts([]);
    }
  }, [formData.customer, options.allProducts]);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });
    
    // Clear errors when field is updated
    if (formErrors[name]) {
      setFormErrors({ ...formErrors, [name]: '' });
    }
  };

  const handleProductSelect = (product) => {
    if (selectedProducts.includes(product)) {
      setSelectedProducts(selectedProducts.filter(p => p !== product));
      
      // Remove supplier assignment if product is deselected
      const newAssignedSuppliers = { ...assignedSuppliers };
      delete newAssignedSuppliers[product];
      setAssignedSuppliers(newAssignedSuppliers);
    } else {
      setSelectedProducts([...selectedProducts, product]);
    }
  };

  const handleSupplierAssignment = (product, supplier) => {
    setAssignedSuppliers({
      ...assignedSuppliers,
      [product]: supplier
    });
  };

  const validateForm = () => {
    const errors = {};
    
    if (!formData.unit) errors.unit = 'Unit is required';
    if (!formData.customer) errors.customer = 'Customer is required';
    
    if (selectedProducts.length === 0) {
      errors.products = 'At least one product must be selected';
    }
    
    // Check if all selected products have suppliers assigned
    selectedProducts.forEach(product => {
      if (!assignedSuppliers[product]) {
        errors.suppliers = `Supplier not assigned for ${product}`;
      }
    });
    
    setFormErrors(errors);
    return Object.keys(errors).length === 0;
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    
    if (!validateForm()) return;
    
    // Create KPI records for each product-supplier combination
    selectedProducts.forEach(product => {
      const newKPI = {
        month: new Date().toLocaleDateString('en-GB', { 
          day: '2-digit', 
          month: 'short', 
          year: 'numeric' 
        }),
        customer: formData.customer,
        product: product,
        supplier: assignedSuppliers[product],
        uom: formData.unit,
        quantity: 0, // Default to 0, user can edit in table view
        asp: 0 // Default to 0, user can edit in table view
      };
      
      onAddKPI(newKPI);
    });
    
    // Reset form
    setFormData({
      unit: '',
      material: '',
      itemGroup: '',
      item: '',
      customer: '',
      products: [],
      suppliers: {}
    });
    setSelectedProducts([]);
    setAssignedSuppliers({});
    
    setSuccessMessage('KPI setup created successfully!');
    setTimeout(() => setSuccessMessage(''), 3000);
  };

  return (
    <div className="max-w-4xl mx-auto p-6 bg-white rounded-lg shadow-lg">
      <h2 className="text-2xl font-bold text-gray-800 mb-6">KPI Setup Configuration</h2>
      
      {isLoading ? (
        <div className="flex flex-col items-center justify-center p-8">
          <div className="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
          <p className="mt-4 text-gray-600">Loading options...</p>
        </div>
      ) : (
        <form onSubmit={handleSubmit} className="space-y-6">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div className="space-y-4">
              <label className="block text-sm font-medium text-gray-700">Unit of Measure (UOM)</label>
              <select 
                name="unit" 
                value={formData.unit}
                onChange={handleInputChange}
                className={`w-full rounded-md border ${formErrors.unit ? 'border-red-500' : 'border-gray-300'} shadow-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500`}
              >
                <option value="">Select UOM</option>
                {options.units.map(unit => (
                  <option key={unit} value={unit}>{unit}</option>
                ))}
              </select>
              {formErrors.unit && <p className="mt-1 text-sm text-red-600">{formErrors.unit}</p>}
            </div>
            <div className="space-y-4">
              <label className="block text-sm font-medium text-gray-700">Customer</label>
              <select 
                name="customer" 
                value={formData.customer}
                onChange={handleInputChange}
                className={`w-full rounded-md border ${formErrors.customer ? 'border-red-500' : 'border-gray-300'} shadow-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500`}
              >
                <option value="">Select Customer</option>
                {options.customers.map(customer => (
                  <option key={customer} value={customer}>{customer}</option>
                ))}
              </select>
              {formErrors.customer && <p className="mt-1 text-sm text-red-600">{formErrors.customer}</p>}
            </div>
          </div>
          
          <div className="space-y-4">
            
            
            <div className="mt-6">
              <label className="block text-sm font-medium text-gray-700 mb-2">Assigned Products</label>
              {formData.customer ? (
                <div className="grid grid-cols-2 md:grid-cols-3 gap-3">
                  {customerProducts.length > 0 ? (
                    customerProducts.map(product => (
                      <div 
                        key={product} 
                        className={`p-3 rounded-lg border cursor-pointer transition-colors ${selectedProducts.includes(product) 
                          ? 'bg-blue-100 border-blue-500 text-blue-700' 
                          : 'border-gray-300 hover:border-blue-400'}`}
                        onClick={() => handleProductSelect(product)}
                      >
                        {product}
                      </div>
                    ))
                  ) : (
                    <p className="text-gray-500 italic">No products assigned to this customer</p>
                  )}
                </div>
              ) : (
                <p className="text-gray-500 italic">Select a customer to view assigned products</p>
              )}
              {formErrors.products && <p className="mt-1 text-sm text-red-600">{formErrors.products}</p>}
            </div>
          </div>
          
          {selectedProducts.length > 0 && (
            <div className="space-y-4 bg-gray-50 p-4 rounded-lg">
              <h3 className="text-lg font-medium text-gray-900">Supplier Assignment</h3>
              <p className="text-sm text-gray-600">Assign a supplier to each selected product:</p>
              
              <div className="space-y-3">
                {selectedProducts.map(product => (
                  <div key={product} className="flex items-center space-x-4">
                    <div className="w-1/3 font-medium text-gray-700">{product}</div>
                    <select 
                      value={assignedSuppliers[product] || ''}
                      onChange={(e) => handleSupplierAssignment(product, e.target.value)}
                      className={`flex-1 rounded-md border ${!assignedSuppliers[product] ? 'border-red-500' : 'border-gray-300'} shadow-sm px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500`}
                    >
                      <option value="">Select Supplier</option>
                      {options.allSuppliers.map(supplier => (
                        <option key={supplier} value={supplier}>{supplier}</option>
                      ))}
                    </select>
                  </div>
                ))}
              </div>
              
              {formErrors.suppliers && <p className="mt-1 text-sm text-red-600">{formErrors.suppliers}</p>}
            </div>
          )}
          
          <div className="flex justify-end">
            <button 
              type="submit" 
              className="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
            >
              Create KPI Setup
            </button>
          </div>
          
          {successMessage && (
            <div className="mt-4 p-4 bg-green-100 text-green-700 rounded-md">
              {successMessage}
            </div>
          )}
        </form>
      )}
      
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div className="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
          <h3 className="text-3xl font-bold text-gray-800">0</h3>
          <p className="text-gray-600 mt-1">Active KPIs</p>
        </div>
        <div className="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
          <h3 className="text-3xl font-bold text-gray-800">0</h3>
          <p className="text-gray-600 mt-1">Customers</p>
        </div>
        <div className="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
          <h3 className="text-3xl font-bold text-gray-800">0</h3>
          <p className="text-gray-600 mt-1">Products</p>
        </div>
      </div>
    </div>
  );
};

export default KPISetupForm;