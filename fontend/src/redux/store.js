import { configureStore, createSlice, createAsyncThunk } from '@reduxjs/toolkit';
import axios from 'axios';

const API_URL = 'http://localhost:8000/api';

// Async Thunks
export const fetchOptions = createAsyncThunk('app/fetchOptions', async () => {
    const response = await axios.get(`${API_URL}/customers`);
    return response.data;
});
export const fetchCustomerProducts = createAsyncThunk(
    'app/fetchCustomerProducts',
    async (customerId) => {
        const response = await axios.get(`${API_URL}/customer-products/${customerId}`);
        return response.data;
    }
);
export const fetchKpiData = createAsyncThunk('app/fetchKpiData', async () => {
    const response = await axios.get(`${API_URL}/kpi`);
    return response.data;
});
export const createBulkKpi = createAsyncThunk('app/createBulkKpi', async (kpiRecords) => {
    const response = await axios.post(`${API_URL}/kpi/bulk`, kpiRecords);
    return response.data;
});
export const updateKpiRecord = createAsyncThunk('app/updateKpiRecord', async ({ id, data }) => {
    const response = await axios.put(`${API_URL}/kpi/${id}`, data);
    return response.data;
});
export const softDeleteRecord = createAsyncThunk('app/softDeleteRecord', async (id) => {
    await axios.delete(`${API_URL}/kpi/${id}`);
    return id;
});
export const restoreRecord = createAsyncThunk('app/restoreRecord', async (id) => {
    await axios.post(`${API_URL}/kpi/${id}/restore`);
    return id;
});

// Slice
const appSlice = createSlice({
    name: 'app',
    initialState: {
        options: { units: ['pcs', 'kg', 'litre', 'meter', 'box'], customers: [], allProducts: [], allSuppliers: [] },
        customerProducts: [],
        kpiData: [],
        deletedRecords: [],
        loading: false,
        error: null,
        activeTab: 'setup'
    },
    reducers: {
        setActiveTab: (state, action) => { state.activeTab = action.payload; }
    },
    extraReducers: (builder) => {
        builder
            .addCase(fetchOptions.pending, (state) => { state.loading = true; })
            .addCase(fetchOptions.fulfilled, (state, action) => {
                state.options.customers = action.payload;
                state.loading = false;
            })
            .addCase(fetchCustomerProducts.fulfilled, (state, action) => {
                state.customerProducts = action.payload;
            })
            .addCase(fetchKpiData.fulfilled, (state, action) => {
                const active = action.payload.filter(r => !r.deleted_at);
                const deleted = action.payload.filter(r => r.deleted_at);
                state.kpiData = active;
                state.deletedRecords = deleted;
            })
            .addCase(createBulkKpi.fulfilled, (state, action) => {
                state.kpiData = [...state.kpiData, ...action.payload];
            })
            .addCase(updateKpiRecord.fulfilled, (state, action) => {
                const updated = action.payload;
                state.kpiData = state.kpiData.map(r => r.id === updated.id ? updated : r);
            })
            .addCase(softDeleteRecord.fulfilled, (state, action) => {
                const id = action.payload;
                const record = state.kpiData.find(r => r.id === id);
                if (record) {
                    state.kpiData = state.kpiData.filter(r => r.id !== id);
                    state.deletedRecords.push({ ...record, deleted_at: new Date().toISOString() });
                }
            })
            .addCase(restoreRecord.fulfilled, (state, action) => {
                const id = action.payload;
                const record = state.deletedRecords.find(r => r.id === id);
                if (record) {
                    state.deletedRecords = state.deletedRecords.filter(r => r.id !== id);
                    state.kpiData.push({ ...record, deleted_at: null });
                }
            });
    }
});

export const { setActiveTab } = appSlice.actions;

const store = configureStore({ reducer: { app: appSlice.reducer } });
export default store;
