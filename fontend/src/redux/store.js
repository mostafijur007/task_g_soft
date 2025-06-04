import {
  configureStore,
  createSlice,
  createAsyncThunk,
} from "@reduxjs/toolkit";
import axios from "axios";

const API_URL = "http://localhost:8000/api";

// Async Thunks
export const fetchOptions = createAsyncThunk("app/fetchOptions", async () => {
  const response = await axios.get(`${API_URL}/customers`);
  return response.data;
});
export const fetchCustomerProducts = createAsyncThunk(
  "app/fetchCustomerProducts",
  async (customerId) => {
    const response = await axios.get(
      `${API_URL}/customer-products/${customerId}`
    );
    return response.data;
  }
);
export const fetchKpiData = createAsyncThunk(
  "app/fetchKpiData",
  async ({ page = 1, perPage = 10 } = {}) => {
    const response = await axios.get(`${API_URL}/kpis?page=${page}&per_page=${perPage}`);
    return response.data;
  }
);
export const createBulkKpi = createAsyncThunk(
  "app/createBulkKpi",
  async (kpiRecords) => {
    const response = await axios.post(`${API_URL}/kpis/bulk`, {
      entries: kpiRecords,
    });
    return response.data;
  }
);
export const updateKpiRecord = createAsyncThunk(
  "app/updateKpiRecord",
  async ({ id, data }) => {
    const response = await axios.put(`${API_URL}/kpi/${id}`, data);
    return response.data;
  }
);
export const softDeleteRecord = createAsyncThunk(
  "app/softDeleteRecord",
  async (id) => {
    await axios.delete(`${API_URL}/kpi/${id}`);
    return id;
  }
);
export const restoreRecord = createAsyncThunk(
  "app/restoreRecord",
  async (id) => {
    await axios.post(`${API_URL}/kpi/${id}/restore`);
    return id;
  }
);

export const fetchSuppliers = createAsyncThunk(
  "app/fetchSuppliers",
  async () => {
    const response = await axios.get(`${API_URL}/suppliers`);
    return response.data;
  }
);

export const assignSupplierToProducts = createAsyncThunk(
  "app/assignSupplierToProducts",
  async ({ supplier_ids, product_id }) => {
    const response = await axios.post(`${API_URL}/product-suppliers/${product_id}`, {
      supplier_ids,
    });
    return { product_id, supplier_ids };
  }
);

// Slice
const appSlice = createSlice({
  name: "app",
  initialState: {
    options: {
      units: ["pcs", "kg", "litre", "meter", "box"],
      customers: [],
      allProducts: [],
      allSuppliers: [],
    },
    customerProducts: [],
    kpiData: [],
    pagination: {
      currentPage: 1,
      perPage: 10,
      totalPages: 1,
      totalItems: 0
    },
    deletedRecords: [],
    loading: false,
    error: null,
    activeTab: "setup",
  },
  reducers: {
    setActiveTab: (state, action) => {
      state.activeTab = action.payload;
    },
  },
  extraReducers: (builder) => {
    builder
      .addCase(fetchOptions.pending, (state) => {
        state.loading = true;
      })
      .addCase(fetchOptions.fulfilled, (state, action) => {
        state.options.customers = action.payload;
        state.loading = false;
      })
      .addCase(fetchCustomerProducts.fulfilled, (state, action) => {
        state.customerProducts = action.payload;
      })
      .addCase(fetchKpiData.fulfilled, (state, action) => {
        state.kpiData = action.payload.data;
        state.pagination = {
          currentPage: action.payload.meta.current_page,
          perPage: action.payload.meta.per_page,
          totalPages: action.payload.meta.last_page,
          totalItems: action.payload.meta.total
        };
      })
      .addCase(createBulkKpi.fulfilled, (state, action) => {
        state.kpiData = [...state.kpiData, ...action.payload.data];
      })
      .addCase(updateKpiRecord.fulfilled, (state, action) => {
        const updated = action.payload;
        state.kpiData = state.kpiData.map((r) =>
          r.id === updated.id ? updated : r
        );
      })
      .addCase(softDeleteRecord.fulfilled, (state, action) => {
        const id = action.payload;
        const record = state.kpiData.find((r) => r.id === id);
        if (record) {
          state.kpiData = state.kpiData.filter((r) => r.id !== id);
          state.deletedRecords.push({
            ...record,
            deleted_at: new Date().toISOString(),
          });
        }
      })
      .addCase(restoreRecord.fulfilled, (state, action) => {
        const id = action.payload;
        const record = state.deletedRecords.find((r) => r.id === id);
        if (record) {
          state.deletedRecords = state.deletedRecords.filter(
            (r) => r.id !== id
          );
          state.kpiData.push({ ...record, deleted_at: null });
        }
      })
      .addCase(fetchSuppliers.fulfilled, (state, action) => {
        state.options.allSuppliers = action.payload;
      })
      .addCase(assignSupplierToProducts.fulfilled, (state, action) => {
        const { product_id, supplier_ids } = action.payload;
        if (!state.productSuppliers) state.productSuppliers = {};
        state.productSuppliers[product_id] = supplier_ids;
      })
      .addCase(assignSupplierToProducts.rejected, (state, action) => {
        state.error = action.error.message;
      });
  },
});

export const { setActiveTab } = appSlice.actions;

const store = configureStore({ reducer: { app: appSlice.reducer } });
export default store;
