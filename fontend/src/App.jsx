import React from 'react';
import { useDispatch, useSelector } from 'react-redux';
import { setActiveTab } from './redux/store';
import KPISetupForm from './components/KPISetupForm';
import KPITableView from './components/KPITableView';
import './styles/globals.css'

const App = () => {
  const dispatch = useDispatch();
  const activeTab = useSelector(state => state.app.activeTab);

  return (
    <div className="min-h-screen bg-gray-100">
      <header className="bg-white shadow">
        <div className="max-w-7xl mx-auto px-4 py-6">
          <div className="flex justify-between items-center">
            <h1 className="text-2xl font-bold text-gray-900">KPI Management System</h1>
            <div className="flex space-x-4">
              <button
                onClick={() => dispatch(setActiveTab('setup'))}
                className={`px-4 py-2 rounded-md ${
                  activeTab === 'setup' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'
                }`}
              >
                KPI Setup
              </button>
              <button
                onClick={() => dispatch(setActiveTab('view'))}
                className={`px-4 py-2 rounded-md ${
                  activeTab === 'view' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'
                }`}
              >
                KPI Table View
              </button>
            </div>
          </div>
        </div>
      </header>
      <main className="max-w-7xl mx-auto px-4 py-8">
        {activeTab === 'setup' ? <KPISetupForm /> : <KPITableView />}
      </main>
    </div>
  );
};

export default App;
