<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\KPIEntryRequest;
use App\Http\Requests\StoreKPIEntryRequest;
use App\Http\Resources\KpiEntryResource;
use App\Services\KPIEntryService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="KPIs",
 *     description="Operations related to KPI management"
 * )
 */
class KPIEntryController extends Controller
{
    use ApiResponseTrait;

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
        $data = $request->has('month')
            ? $this->service->getByMonth($request->month)
            : $this->service->getAll();

        return $this->success(
            KpiEntryResource::collection($data),
            'Kpi entry retrieved successfully',
            201
        );
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

    /**
     * @OA\Post(
     *     path="/api/kpi/bulk",
     *     summary="Create multiple KPI entries",
     *     description="Bulk store multiple KPI entries at once.",
     *     tags={"KPIs"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"entries"},
     *             @OA\Property(
     *                 property="entries",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"customer_id","product_id","supplier_id","month","uom","quantity","asp","total_value"},
     *                     @OA\Property(property="customer_id", type="integer", example=1),
     *                     @OA\Property(property="product_id", type="integer", example=5),
     *                     @OA\Property(property="supplier_id", type="integer", example=2),
     *                     @OA\Property(property="month", type="string", format="date", example="2025-06-01"),
     *                     @OA\Property(property="uom", type="string", maxLength=10, example="units"),
     *                     @OA\Property(property="quantity", type="number", format="float", minimum=0, example=100),
     *                     @OA\Property(property="asp", type="number", format="float", minimum=0, example=45.5),
     *                     @OA\Property(property="total_value", type="number", format="float", minimum=0, example=4550)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="KPI entries created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="KPI entries created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=101),
     *                     @OA\Property(property="code", type="string", example="KPI-2025-001"),
     *                     @OA\Property(
     *                         property="customer",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=11),
     *                         @OA\Property(property="name", type="string", example="Acme Corp")
     *                     ),
     *                     @OA\Property(
     *                         property="product",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=7),
     *                         @OA\Property(property="name", type="string", example="Premium Widget")
     *                     ),
     *                     @OA\Property(
     *                         property="supplier",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="name", type="string", example="Global Supplies Ltd.")
     *                     ),
     *                     @OA\Property(property="month", type="string", example="2025-06"),
     *                     @OA\Property(property="uom", type="string", example="units"),
     *                     @OA\Property(property="quantity", type="integer", example=1200),
     *                     @OA\Property(property="asp", type="number", format="float", example=42.5),
     *                     @OA\Property(property="total_value", type="number", format="float", example=51000),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-10T14:22:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-20T10:33:00Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function bulkStore(StoreKPIEntryRequest $request)
    {
        $entries = $this->service->bulkStore($request->validated()['entries']);
        return $this->success(
            KpiEntryResource::collection($entries),
            'KPI entries created successfully',
            201
        );
    }


    /**
     * @OA\Get(
     *     path="/api/kpis/trashed",
     *     summary="Get all trashed KPI entries",
     *     tags={"KPIs"},
     *     @OA\Response(
     *         response=200,
     *         description="Get all trashed KPI entries",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Kpi trashed entry retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=101),
     *                     @OA\Property(property="code", type="string", example="KPI-2025-001"),
     *                     @OA\Property(
     *                         property="customer",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=11),
     *                         @OA\Property(property="name", type="string", example="Acme Corp")
     *                     ),
     *                     @OA\Property(
     *                         property="product",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=7),
     *                         @OA\Property(property="name", type="string", example="Premium Widget")
     *                     ),
     *                     @OA\Property(
     *                         property="supplier",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="name", type="string", example="Global Supplies Ltd.")
     *                     ),
     *                     @OA\Property(property="month", type="string", example="2025-06"),
     *                     @OA\Property(property="uom", type="string", example="units"),
     *                     @OA\Property(property="quantity", type="integer", example=1200),
     *                     @OA\Property(property="asp", type="number", format="float", example=42.5),
     *                     @OA\Property(property="total_value", type="number", format="float", example=51000),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-10T14:22:00Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-20T10:33:00Z")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function trashed(Request $request)
    {
        $data = $this->service->getTrashed();

        return $this->success(
            KpiEntryResource::collection($data),
            'Kpi trashed entry retrieved successfully',
            201
        );
    }
}
