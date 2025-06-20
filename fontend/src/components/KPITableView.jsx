import React, { useState, useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import {
  fetchKpiData,
  softDeleteRecord,
  restoreRecord,
  bulkUpdateKpi,
  fetchTrashedKpis,
} from "../redux/store";

const KPITableView = () => {
  const dispatch = useDispatch();
  const { kpiData, loading, pagination, trashedKpis, trashedPagination } =
    useSelector((state) => state.app);

  const [showDeleted, setShowDeleted] = useState(false);
  const [groupedData, setGroupedData] = useState({});
  const [isGlobalEditing, setIsGlobalEditing] = useState(false);
  const [editedRows, setEditedRows] = useState({});
  const [successMessage, setSuccessMessage] = useState("");

  useEffect(() => {
    dispatch(fetchKpiData());
  }, [dispatch]);

  useEffect(() => {
    dispatch(
      fetchKpiData({
        page: pagination.currentPage,
        perPage: pagination.perPage,
      })
    );
  }, [dispatch, pagination.currentPage, pagination.perPage]);

  useEffect(() => {
    if (showDeleted) {
      dispatch(fetchTrashedKpis());
    }
  }, [showDeleted, dispatch]);

  const handlePageChange = (newPage) => {
    if (newPage >= 1 && newPage <= pagination.totalPages) {
      dispatch(fetchKpiData({ page: newPage, perPage: pagination.perPage }));
    }
  };

  useEffect(() => {
    const grouped = kpiData.reduce((acc, item) => {
      const key = `${item.month}-${item.customer?.id}-${item.product?.id}`;
      if (!acc[key]) {
        acc[key] = {
          month: item.month,
          customer: item.customer?.name,
          product: item.product?.name,
          items: [],
        };
      }
      acc[key].items.push({
        supplier: item.supplier?.name,
        uom: item.uom,
        quantity: item.quantity,
        asp: item.asp,
        total_value: item.total_value,
        id: item.id,
      });
      return acc;
    }, {});

    setGroupedData(grouped);
  }, [kpiData]);

  const handleSoftDelete = async (id) => {
    await dispatch(softDeleteRecord(id));
    await dispatch(fetchKpiData());
    await dispatch(fetchTrashedKpis());
    setSuccessMessage("Record deleted successfully!");
    setTimeout(() => setSuccessMessage(""), 3000);
    window.scrollTo(0, 0);
  };
  const handleRestore = async (id) => {
    await dispatch(restoreRecord(id));
    await dispatch(fetchKpiData());
    await dispatch(fetchTrashedKpis());
    setSuccessMessage("Record restored successfully!");
    setTimeout(() => setSuccessMessage(""), 3000);
    window.scrollTo(0, 0);
  };

  const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString("en-GB", {
      day: "2-digit",
      month: "short",
      year: "numeric",
    });
  };

  const handleGlobalEditChange = (id, field, value) => {
    setEditedRows((prev) => ({
      ...prev,
      [id]: {
        ...prev[id],
        id,
        [field]:
          field === "quantity" || field === "asp" ? parseFloat(value) : value,
      },
    }));
  };

  const handleSaveAll = async () => {
    try {
      const entries = Object.values(editedRows);
      await dispatch(bulkUpdateKpi(entries)).unwrap();
      await dispatch(fetchKpiData());
      setIsGlobalEditing(false);
      setEditedRows({});
      setSuccessMessage("KPI save successfully!");
      setTimeout(() => setSuccessMessage(""), 3000);
    } catch (error) {
      console.error("Failed to update records:", error);
    }
  };

  return (
    <div className="bg-white rounded-xl shadow-md p-6">
      {successMessage && (
        <div className="my-4 p-3 bg-green-100 text-green-700 rounded-md">
          {successMessage}
        </div>
      )}
      <div className="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <h2 className="text-2xl font-bold text-gray-800">KPI Table View</h2>
        <div className="flex items-center space-x-4">
          {isGlobalEditing ? (
            <>
              <button
                onClick={handleSaveAll}
                className="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600"
              >
                Save All
              </button>
              <button
                onClick={() => {
                  setIsGlobalEditing(false);
                  setEditedRows({});
                }}
                className="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
              >
                Cancel
              </button>
            </>
          ) : (
            <button
              onClick={() => setIsGlobalEditing(true)}
              className="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
            >
              Edit All
            </button>
          )}
          <label className="flex items-center space-x-2">
            <input
              type="checkbox"
              checked={showDeleted}
              onChange={() => setShowDeleted(!showDeleted)}
              className="h-4 w-4 text-blue-600 rounded"
            />
            <span className="text-gray-700">Show Deleted Records</span>
          </label>
        </div>
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
                  {[
                    "Month",
                    "Customer",
                    "Product",
                    "Supplier",
                    "UOM",
                    "Quantity",
                    "ASP",
                    "Total Value",
                    "Actions",
                  ].map((h) => (
                    <th
                      key={h}
                      className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                    >
                      {h}
                    </th>
                  ))}
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {groupedData &&
                  Object.values(groupedData).map((group, groupIndex) =>
                    group.items.map((item, itemIndex) => (
                      <tr
                        key={`${groupIndex}-${itemIndex}`}
                        className={item.deleted_at ? "bg-red-50" : ""}
                      >
                        {itemIndex === 0 ? (
                          <>
                            <td
                              className="px-6 py-4 text-sm font-medium text-gray-900 align-center"
                              rowSpan={group.items.length}
                            >
                              {formatDate(group.month)}
                            </td>
                            <td
                              className="px-6 py-4 text-sm text-gray-500 align-center"
                              rowSpan={group.items.length}
                            >
                              {group.customer}
                            </td>
                            <td
                              className="px-6 py-4 text-sm text-gray-500 align-center"
                              rowSpan={group.items.length}
                            >
                              {group.product}
                            </td>
                          </>
                        ) : null}
                        <td className="px-6 py-4 text-sm text-gray-500">
                          {item.supplier?.name || item.supplier}
                        </td>
                        <td className="px-6 py-4">
                          {isGlobalEditing ? (
                            <select
                              value={editedRows[item.id]?.uom || item.uom}
                              onChange={(e) =>
                                handleGlobalEditChange(
                                  item.id,
                                  "uom",
                                  e.target.value
                                )
                              }
                              className="w-full p-1 border border-gray-300 rounded-md"
                            >
                              {[
                                "Units",
                                "Cases",
                                "Pallets",
                                "Kg",
                                "Liters",
                              ].map((unit) => (
                                <option key={unit} value={unit}>
                                  {unit}
                                </option>
                              ))}
                            </select>
                          ) : (
                            item.uom
                          )}
                        </td>
                        <td className="px-6 py-4">
                          {isGlobalEditing ? (
                            <input
                              type="number"
                              value={
                                editedRows[item.id]?.quantity || item.quantity
                              }
                              onChange={(e) =>
                                handleGlobalEditChange(
                                  item.id,
                                  "quantity",
                                  e.target.value
                                )
                              }
                              min="0"
                              step="1"
                              className="w-full p-1 border border-gray-300 rounded-md"
                            />
                          ) : (
                            item.quantity
                          )}
                        </td>
                        <td className="px-6 py-4">
                          {isGlobalEditing ? (
                            <input
                              type="number"
                              value={editedRows[item.id]?.asp || item.asp}
                              onChange={(e) =>
                                handleGlobalEditChange(
                                  item.id,
                                  "asp",
                                  e.target.value
                                )
                              }
                              min="0"
                              step="0.01"
                              className="w-full p-1 border border-gray-300 rounded-md"
                            />
                          ) : (
                            item.asp
                          )}
                        </td>
                        <td className="px-6 py-4 text-sm text-gray-500">
                          {(
                            (editedRows[item.id]?.quantity || item.quantity) *
                            (editedRows[item.id]?.asp || item.asp)
                          ).toFixed(2)}
                        </td>
                        <td className="px-6 py-4 text-sm text-gray-500">
                          <button
                            onClick={() => handleSoftDelete(item.id)}
                            className="text-red-600 hover:text-red-900"
                          >
                            Delete
                          </button>
                        </td>
                      </tr>
                    ))
                  )}
              </tbody>
            </table>
          </div>

          {Object.keys(groupedData).length === 0 && (
            <div className="py-12 text-center">
              <p className="text-gray-500">
                No KPI records found. Create some in the KPI Setup tab.
              </p>
            </div>
          )}
        </>
      )}

      {!loading && Object.keys(groupedData).length > 0 && (
        <div className="mt-6 flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
          <div className="flex flex-1 justify-between sm:hidden">
            <button
              onClick={() => handlePageChange(pagination.currentPage - 1)}
              disabled={pagination.currentPage === 1}
              className="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
            >
              Previous
            </button>
            <button
              onClick={() => handlePageChange(pagination.currentPage + 1)}
              disabled={pagination.currentPage === pagination.totalPages}
              className="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50"
            >
              Next
            </button>
          </div>
          <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div>
              <p className="text-sm text-gray-700">
                Showing{" "}
                <span className="font-medium">
                  {(pagination.currentPage - 1) * pagination.perPage + 1}
                </span>{" "}
                to{" "}
                <span className="font-medium">
                  {Math.min(
                    pagination.currentPage * pagination.perPage,
                    pagination.totalItems
                  )}
                </span>{" "}
                of <span className="font-medium">{pagination.totalItems}</span>{" "}
                results
              </p>
            </div>
            <div>
              <nav
                className="isolate inline-flex -space-x-px rounded-md shadow-sm"
                aria-label="Pagination"
              >
                <button
                  onClick={() => handlePageChange(pagination.currentPage - 1)}
                  disabled={pagination.currentPage === 1}
                  className="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50"
                >
                  <span className="sr-only">Previous</span>
                  <svg
                    className="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      fillRule="evenodd"
                      d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z"
                      clipRule="evenodd"
                    />
                  </svg>
                </button>
                {[...Array(pagination.totalPages)].map((_, i) => (
                  <button
                    key={i + 1}
                    onClick={() => handlePageChange(i + 1)}
                    className={`relative inline-flex items-center px-4 py-2 text-sm font-semibold ${
                      pagination.currentPage === i + 1
                        ? "z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                        : "text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0"
                    }`}
                  >
                    {i + 1}
                  </button>
                ))}
                <button
                  onClick={() => handlePageChange(pagination.currentPage + 1)}
                  disabled={pagination.currentPage === pagination.totalPages}
                  className="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50"
                >
                  <span className="sr-only">Next</span>
                  <svg
                    className="h-5 w-5"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                  >
                    <path
                      fillRule="evenodd"
                      d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z"
                      clipRule="evenodd"
                    />
                  </svg>
                </button>
              </nav>
            </div>
          </div>
        </div>
      )}

      {showDeleted && (
        <div className="mt-10">
          <h3 className="text-lg font-medium text-gray-900 mb-4">
            Deleted Records
          </h3>
          {trashedKpis.length > 0 ? (
            <div className="overflow-x-auto">
              <table className="min-w-full divide-y divide-gray-200">
                <thead className="bg-gray-50">
                  <tr>
                    {[
                      "Month",
                      "Customer",
                      "Product",
                      "Supplier",
                      "UOM",
                      "Quantity",
                      "ASP",
                      "Total Value",
                      "Actions",
                    ].map((h) => (
                      <th
                        key={h}
                        className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                      >
                        {h}
                      </th>
                    ))}
                  </tr>
                </thead>
                <tbody className="bg-white divide-y divide-gray-200">
                  {trashedKpis.map((item) => (
                    <tr key={item?.code} className="bg-red-50">
                      <td className="px-6 py-4 text-sm text-gray-500">
                        {formatDate(item?.month)}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        {item?.customer?.name || ""}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        {item?.product?.name || ""}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        {item?.supplier?.name || ""}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        {item?.uom || ""}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        {item?.quantity || ""}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        {item?.asp || ""}
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        {item.quantity || 1 * item.asp || 1}
                      </td>
                      <td className="px-6 py-4 text-sm font-medium">
                        <button
                          onClick={() => handleRestore(item?.id)}
                          className="text-green-600 hover:text-green-900"
                        >
                          Restore
                        </button>
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
    </div>
  );
};

export default KPITableView;
