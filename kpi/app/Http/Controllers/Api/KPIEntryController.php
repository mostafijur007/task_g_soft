<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KPIEntryRequest;
use App\Http\Requests\StoreKPIEntryRequest;
use App\Services\KPIEntryService;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="KPIs",
 *     description="Operations related to KPI management"
 * )
 */
class KPIEntryController extends Controller
{
    protected $service;

    public function __construct(KPIEntryService $service)
    {
        $this->service = $service;
    }

    /**
     * @OA\Get(
     *     path="/api/kpis",
     *     summary="Get all KPI entries or filter by month",
     *     tags={"KPIs"},
     *     @OA\Parameter(
     *         name="month",
     *         in="query",
     *         description="Filter by month (format: YYYY-MM)",
     *         required=false,
     *         @OA\Schema(type="string", example="2025-06")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of KPI entries",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Sales Target"),
     *                 @OA\Property(property="value", type="number", format="float", example=85.5),
     *                 @OA\Property(property="month", type="string", example="2025-06"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        if ($request->has('month')) {
            return response()->json($this->service->getByMonth($request->month));
        }

        return response()->json($this->service->getAll());
    }

    /**
     * @OA\Post(
     *     path="/api/kpis",
     *     summary="Store a new KPI entry",
     *     tags={"KPIs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"customer_id", "supplier_id", "uom", "product_id", "quantity", "asp", "total_value"},
     *             @OA\Property(property="customer_id", type="number", example="1"),
     *             @OA\Property(property="month", type="string", example="2025-06"),
     *             @OA\Property(property="product_id", type="number", example="1"),
     *             @OA\Property(property="supplier_id", type="number", example="1"),
     *             @OA\Property(property="uom", type="string", example="kg"),
     *             @OA\Property(property="quantity", type="number", example="1"),
     *             @OA\Property(property="asp", type="number", example="5"),
     *             @OA\Property(property="total_value", type="number", example="5"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="KPI created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Sales Target"),
     *             @OA\Property(property="value", type="number", format="float", example=90.5),
     *             @OA\Property(property="month", type="string", example="2025-06"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function store(KPIEntryRequest $request)
    {
        return response()->json($this->service->store($request->validated()), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/kpis/{id}",
     *     summary="Get a specific KPI entry",
     *     tags={"KPIs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="KPI found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Sales Target"),
     *             @OA\Property(property="value", type="number", format="float", example=85.5),
     *             @OA\Property(property="month", type="string", example="2025-06"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(response=404, description="KPI not found")
     * )
     */
    public function show($id)
    {
        return response()->json($this->service->find($id));
    }

    /**
     * @OA\Put(
     *     path="/api/kpis/{id}",
     *     summary="Update a specific KPI entry",
     *     tags={"KPIs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "value", "month"},
     *             @OA\Property(property="name", type="string", example="Sales Target"),
     *             @OA\Property(property="value", type="number", format="float", example=90.5),
     *             @OA\Property(property="month", type="string", example="2025-06")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="KPI updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="Sales Target"),
     *             @OA\Property(property="value", type="number", format="float", example=90.5),
     *             @OA\Property(property="month", type="string", example="2025-06"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     )
     * )
     */
    public function update(KPIEntryRequest $request, $id)
    {
        return response()->json($this->service->update($id, $request->validated()));
    }

    /**
     * @OA\Delete(
     *     path="/api/kpis/{id}",
     *     summary="Delete a specific KPI entry",
     *     tags={"KPIs"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['message' => 'Deleted successfully']);
    }

    public function bulkStore(StoreKPIEntryRequest $request)
    {
        $this->service->bulkStore($request->validated()['entries']);
        return response()->json(['message' => 'KPI entries created successfully']);
    }
}
