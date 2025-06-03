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
            materials: ['Material A', 'Material B', 'Material C', 'Material D'],
            itemGroups: ['Group 1', 'Group 2', 'Group 3', 'Group 4'],
            items: ['Item 1', 'Item 2', 'Item 3', 'Item 4'],
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
    if (!formData.material) errors.material = 'Material is required';
    if (!formData.itemGroup) errors.itemGroup = 'Item Group is required';
    if (!formData.item) errors.item = 'Item is required';
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
    <div className="kpi-setup-form">
      <h2>KPI Setup Configuration</h2>
      
      {isLoading ? (
        <div className="loading">
          <div className="spinner"></div>
          <p>Loading options...</p>
        </div>
      ) : (
        <form onSubmit={handleSubmit}>
          <div className="form-section">
            <div className="form-group">
              <label>Unit of Measure (UOM)</label>
              <select 
                name="unit" 
                value={formData.unit}
                onChange={handleInputChange}
                className={formErrors.unit ? 'error' : ''}
              >
                <option value="">Select UOM</option>
                {options.units.map(unit => (
                  <option key={unit} value={unit}>{unit}</option>
                ))}
              </select>
              {formErrors.unit && <p className="error-message">{formErrors.unit}</p>}
            </div>
            
            <div className="form-group">
              <label>Material</label>
              <select 
                name="material" 
                value={formData.material}
                onChange={handleInputChange}
                className={formErrors.material ? 'error' : ''}
              >
                <option value="">Select Material</option>
                {options.materials.map(material => (
                  <option key={material} value={material}>{material}</option>
                ))}
              </select>
              {formErrors.material && <p className="error-message">{formErrors.material}</p>}
            </div>
          </div>
          
          <div className="form-section">
            <div className="form-group">
              <label>Item Group</label>
              <select 
                name="itemGroup" 
                value={formData.itemGroup}
                onChange={handleInputChange}
                className={formErrors.itemGroup ? 'error' : ''}
              >
                <option value="">Select Item Group</option>
                {options.itemGroups.map(group => (
                  <option key={group} value={group}>{group}</option>
                ))}
              </select>
              {formErrors.itemGroup && <p className="error-message">{formErrors.itemGroup}</p>}
            </div>
            
            <div className="form-group">
              <label>Item</label>
              <select 
                name="item" 
                value={formData.item}
                onChange={handleInputChange}
                className={formErrors.item ? 'error' : ''}
              >
                <option value="">Select Item</option>
                {options.items.map(item => (
                  <option key={item} value={item}>{item}</option>
                ))}
              </select>
              {formErrors.item && <p className="error-message">{formErrors.item}</p>}
            </div>
          </div>
          
          <div className="form-section customer-section">
            <div className="form-group">
              <label>Customer</label>
              <select 
                name="customer" 
                value={formData.customer}
                onChange={handleInputChange}
                className={formErrors.customer ? 'error' : ''}
              >
                <option value="">Select Customer</option>
                {options.customers.map(customer => (
                  <option key={customer} value={customer}>{customer}</option>
                ))}
              </select>
              {formErrors.customer && <p className="error-message">{formErrors.customer}</p>}
            </div>
            
            <div className="customer-products">
              <label>Assigned Products</label>
              {formData.customer ? (
                <div className="product-list">
                  {customerProducts.length > 0 ? (
                    customerProducts.map(product => (
                      <div 
                        key={product} 
                        className={`product-item ${selectedProducts.includes(product) ? 'selected' : ''}`}
                        onClick={() => handleProductSelect(product)}
                      >
                        {product}
                      </div>
                    ))
                  ) : (
                    <p>No products assigned to this customer</p>
                  )}
                </div>
              ) : (
                <p>Select a customer to view assigned products</p>
              )}
              {formErrors.products && <p className="error-message">{formErrors.products}</p>}
            </div>
          </div>
          
          {selectedProducts.length > 0 && (
            <div className="form-section supplier-assignment">
              <h3>Supplier Assignment</h3>
              <p>Assign a supplier to each selected product:</p>
              
              {selectedProducts.map(product => (
                <div key={product} className="supplier-row">
                  <div className="product-label">{product}</div>
                  <select 
                    value={assignedSuppliers[product] || ''}
                    onChange={(e) => handleSupplierAssignment(product, e.target.value)}
                    className={!assignedSuppliers[product] ? 'error' : ''}
                  >
                    <option value="">Select Supplier</option>
                    {options.allSuppliers.map(supplier => (
                      <option key={supplier} value={supplier}>{supplier}</option>
                    ))}
                  </select>
                </div>
              ))}
              
              {formErrors.suppliers && <p className="error-message">{formErrors.suppliers}</p>}
            </div>
          )}
          
          <div className="form-actions">
            <button type="submit" className="submit-btn">Create KPI Setup</button>
          </div>
          
          {successMessage && <div className="success-message">{successMessage}</div>}
        </form>
      )}
      
      <div className="stats-summary">
        <div className="stat-card">
          <h3>{kpiData.length}</h3>
          <p>Active KPIs</p>
        </div>
        <div className="stat-card">
          <h3>{new Set(kpiData.map(kpi => kpi.customer)).size}</h3>
          <p>Customers</p>
        </div>
        <div className="stat-card">
          <h3>{new Set(kpiData.map(kpi => kpi.product)).size}</h3>
          <p>Products</p>
        </div>
      </div>
    </div>
  );
};

export default KPISetupForm;