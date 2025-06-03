// src/App.js
import React, { useState, useEffect } from 'react';
import KPISetupForm from './components/KPISetupForm';
import KPITableView from './components/KPITableView';
import KPIEntryForm from './components/KPIEntryForm';
import './styles/globals.css'

function App() {
  // const [activeTab, setActiveTab] = useState('setup');
  // const [kpiData, setKpiData] = useState([]);
  // const [deletedRecords, setDeletedRecords] = useState([]);

  // // Load sample data
  // useEffect(() => {
  //   const sampleData = [
  //     {
  //       id: 1,
  //       month: '30 Jan 2025',
  //       customer: 'Unilever',
  //       product: 'Product A',
  //       supplier: 'Supplier B',
  //       uom: 'Units',
  //       quantity: 2,
  //       asp: 2,
  //       totalValue: 4,
  //       isDeleted: false
  //     },
  //     {
  //       id: 2,
  //       month: '30 Jan 2025',
  //       customer: 'Unilever',
  //       product: 'Product A',
  //       supplier: 'Supplier C',
  //       uom: 'Units',
  //       quantity: 2,
  //       asp: 1,
  //       totalValue: 2,
  //       isDeleted: false
  //     },
  //     {
  //       id: 3,
  //       month: '31 Jan 2025',
  //       customer: 'Unilever',
  //       product: 'Product B',
  //       supplier: 'Supplier A',
  //       uom: 'Cases',
  //       quantity: 2,
  //       asp: 2,
  //       totalValue: 4,
  //       isDeleted: false
  //     },
  //     {
  //       id: 4,
  //       month: '31 Jan 2025',
  //       customer: 'Unilever',
  //       product: 'Product B',
  //       supplier: 'Supplier B',
  //       uom: 'Cases',
  //       quantity: 2,
  //       asp: 1,
  //       totalValue: 2,
  //       isDeleted: false
  //     },
  //     {
  //       id: 5,
  //       month: '31 Jan 2025',
  //       customer: 'Nestle',
  //       product: 'Product C',
  //       supplier: 'Supplier C',
  //       uom: 'Pallets',
  //       quantity: 2,
  //       asp: 2,
  //       totalValue: 4,
  //       isDeleted: false
  //     }
  //   ];
    
  //   setKpiData(sampleData);
    
  //   // Simulate soft-deleted records
  //   setDeletedRecords([
  //     {
  //       id: 6,
  //       month: '28 Jan 2025',
  //       customer: 'Deleted Customer',
  //       product: 'Deleted Product',
  //       supplier: 'Deleted Supplier',
  //       uom: 'Units',
  //       quantity: 5,
  //       asp: 3,
  //       totalValue: 15,
  //       isDeleted: true
  //     }
  //   ]);
  // }, []);

  // const handleAddKPI = (newKPI) => {
  //   // Generate a unique ID for the new record
  //   const newId = Math.max(0, ...kpiData.map(item => item.id)) + 1;
    
  //   setKpiData([...kpiData, {
  //     ...newKPI,
  //     id: newId,
  //     totalValue: newKPI.quantity * newKPI.asp,
  //     isDeleted: false
  //   }]);
  // };

  // const handleUpdateKPI = (updatedKPI) => {
  //   setKpiData(kpiData.map(item => 
  //     item.id === updatedKPI.id ? 
  //       {...updatedKPI, totalValue: updatedKPI.quantity * updatedKPI.asp} : 
  //       item
  //   ));
  // };

  // const handleSoftDelete = (id) => {
  //   const record = kpiData.find(item => item.id === id);
  //   if (record) {
  //     // Remove from main table and add to deleted records
  //     setKpiData(kpiData.filter(item => item.id !== id));
  //     setDeletedRecords([...deletedRecords, {...record, isDeleted: true}]);
  //   }
  // };

  // const handleRestore = (id) => {
  //   const record = deletedRecords.find(item => item.id === id);
  //   if (record) {
  //     // Remove from deleted records and add back to main table
  //     setDeletedRecords(deletedRecords.filter(item => item.id !== id));
  //     setKpiData([...kpiData, {...record, isDeleted: false}]);
  //   }
  // };

  return (
    <div className="app-container">
      {/* <header className="app-header">
        <h1>KPI Management Dashboard</h1>
        <nav className="tabs">
          <button 
            className={activeTab === 'setup' ? 'active' : ''}
            onClick={() => setActiveTab('setup')}
          >
            KPI Setup
          </button>
          <button 
            className={activeTab === 'view' ? 'active' : ''}
            onClick={() => setActiveTab('view')}
          >
            KPI Table View
          </button>
        </nav>
      </header>
      
      <main className="app-main">
        {activeTab === 'setup' ? (
          <KPISetupForm 
            onAddKPI={handleAddKPI} 
            kpiData={kpiData} 
          />
        ) : (
          <KPITableView 
            kpiData={kpiData} 
            deletedRecords={deletedRecords}
            onUpdateKPI={handleUpdateKPI}
            onSoftDelete={handleSoftDelete}
            onRestore={handleRestore}
          />
        )}
      </main>
      
      <footer className="app-footer">
        <p>© 2025 KPI Management System | Built with React</p>
      </footer> */}
      <KPIEntryForm />
    </div>
  );
}

export default App;