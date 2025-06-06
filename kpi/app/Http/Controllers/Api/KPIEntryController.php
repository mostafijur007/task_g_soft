<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BulkUpdateKpiRequest;
use App\Http\Requests\KPIEntryRequest;
use App\Http\Requests\StoreKPIEntryRequest;
use App\Http\Resources\KpiEntryResource;
use App\Services\KPIEntryService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="KPIs retrieved successfully"),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="http://domain_name.com/api/kpis?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://domainname.com/api/kpis?page=4"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", example="http://domain_name.com/api/kpis?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=4),
     *                 @OA\Property(property="path", type="string", example="http://domainname.com/api/kpis"),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="to", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=55),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true),
     *                         @OA\Property(property="label", type="string"),
     *                         @OA\Property(property="active", type="boolean")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                      type="object",
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="name", type="string", example="Sales Target"),
     *                      @OA\Property(property="value", type="number", format="float", example=85.5),
     *                      @OA\Property(property="month", type="string", example="2025-06"),
     *                      @OA\Property(property="created_at", type="string", format="date-time"),
     *                      @OA\Property(property="updated_at", type="string", format="date-time")
     *                  )
     *             )
     *         ),
     *     ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $kpis = $request->has('month')
            ? $this->service->getByMonth($request->month)
            : $this->service->getAll();

        return $this->success(
            KpiEntryResource::collection($kpis)->response()->getData(true),
            'KPIs retrieved successfully',
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
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="KPI created successfully"),
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Sales Target"),
     *                 @OA\Property(property="value", type="number", format="float", example=90.5),
     *                 @OA\Property(property="month", type="string", example="2025-06"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *          response=422, 
     *          description="Validation Error",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="customer_id", type="array", @OA\Items(type="string", example="The customer id field is required.")),
     *                 @OA\Property(property="product_id", type="array", @OA\Items(type="string", example="The product id field is required.")),
     *                 @OA\Property(property="supplier_id", type="array", @OA\Items(type="string", example="The supplier id field is required.")),
     *                 @OA\Property(property="month", type="array", @OA\Items(type="string", example="The month field is required.")),
     *                 @OA\Property(property="uom", type="array", @OA\Items(type="string", example="The uom field is required.")),
     *                 @OA\Property(property="quantity", type="array", @OA\Items(type="string", example="The quantity field is required.")),
     *                 @OA\Property(property="asp", type="array", @OA\Items(type="string", example="The asp field is required.")),
     *                 @OA\Property(property="total_value", type="array", @OA\Items(type="string", example="The total value field is required.")),
     *             ),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),
     *    @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function store(KPIEntryRequest $request)
    {
        $kpi = $this->service->store($request->validated());
        return $this->success(
            new KpiEntryResource($kpi),
            'KPI created successfully',
            201
        );
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
     *         description="KPI retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="KPI retrieved successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Sales Target"),
     *                 @OA\Property(property="value", type="number", format="float", example=85.5),
     *                 @OA\Property(property="month", type="string", example="2025-06"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="KPI not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="KPI not found"),
     *             @OA\Property(property="data", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $kpi = $this->service->find($id);
            return $this->success(
                new KpiEntryResource($kpi),
                'KPI retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error('KPI not found', 404);
        }
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
     *         response=200,
     *         description="KPI updated successfully",
     *         @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="KPI updated successfully"),
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="code", type="string", example="KPI-0001"),
     *                 @OA\Property(property="month", type="string", example="2025-06-10"),
     *                 @OA\Property(property="uom", type="string", example="kg"),
     *                 @OA\Property(property="quantity", type="number", example=90),
     *                 @OA\Property(property="asp", type="number", example=5),
     *                 @OA\Property(property="total_value", type="number", format="float", example=90.5),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response=422, 
     *          description="Validation Error",
     *          @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="customer_id", type="array", @OA\Items(type="string", example="The customer id field is required.")),
     *                 @OA\Property(property="product_id", type="array", @OA\Items(type="string", example="The product id field is required.")),
     *                 @OA\Property(property="supplier_id", type="array", @OA\Items(type="string", example="The supplier id field is required.")),
     *                 @OA\Property(property="month", type="array", @OA\Items(type="string", example="The month field is required.")),
     *                 @OA\Property(property="uom", type="array", @OA\Items(type="string", example="The uom field is required.")),
     *                 @OA\Property(property="quantity", type="array", @OA\Items(type="string", example="The quantity field is required.")),
     *                 @OA\Property(property="asp", type="array", @OA\Items(type="string", example="The asp field is required.")),
     *                 @OA\Property(property="total_value", type="array", @OA\Items(type="string", example="The total value field is required.")),
     *             ),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),     
     *     @OA\Response(
     *         response=404,
     *         description="KPI not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="KPI not found"),
     *             @OA\Property(property="data", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function update(KPIEntryRequest $request, $id)
    {
        try {
            $kpi = $this->service->update($id, $request->validated());
            return $this->success(
                new KpiEntryResource($kpi),
                'KPI updated successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error('KPI not found.', 404);
        } catch (Exception $e) {
            return $this->error('An unexpected error occurred.', 500);
        }
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
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Kpi entry Deleted successfully"),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="KPI not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="KPI not found"),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->service->delete($id);
            return $this->success(
                "",
                'KPI deleted successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error('KPI not found.', 404);
        } catch (Exception $e) {
            return $this->error('An unexpected error occurred.', 500);
        }
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
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
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
     *     path="/api/kpi/trashed",
     *     summary="Get all trashed KPI entries",
     *     tags={"KPIs"},
     *     @OA\Response(
     *         response=200,
     *         description="Get all trashed KPI entries",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Kpi trashed entry retrieved successfully"),
     *              @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="http://domain_name.com/api/trashed?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://domainname.com/api/trashed?page=4"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", example="http://domain_name.com/api/trashed?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=4),
     *                 @OA\Property(property="path", type="string", example="http://domainname.com/trashed/kpis"),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="to", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=55),
     *                 @OA\Property(
     *                     property="links",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="url", type="string", nullable=true),
     *                         @OA\Property(property="label", type="string"),
     *                         @OA\Property(property="active", type="boolean")
     *                     )
     *                 )
     *             ),
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
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function trashed(Request $request)
    {
        $data = $this->service->getTrashed();

        return $this->success(
            KpiEntryResource::collection($data)->response()->getData(true),
            'Kpi trashed entry retrieved successfully',
            201
        );
    }

    /**
     * @OA\Put(
     *     path="/api/kpi/bulk-update",
     *     tags={"KPIs"},
     *     summary="Bulk update KPI entries",
     *     description="Updates multiple KPI entries by ID with provided fields",
     *     operationId="bulkUpdateKPIEntries",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"entries"},
     *             @OA\Property(
     *                 property="entries",
     *                 type="array",
     *                 minItems=1,
     *                 @OA\Items(
     *                     type="object",
     *                     required={"id"},
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="quantity", type="integer", minimum=0, example=50),
     *                     @OA\Property(property="asp", type="number", format="float", minimum=0, example=22.5),
     *                     @OA\Property(property="uom", type="string", maxLength=50, example="pcs")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bulk KPI entries updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="KPI entries updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                    @OA\Property(property="id", type="integer", example=1),
     *                    @OA\Property(property="code", type="string", example="KPI-0001"),
     *                    @OA\Property(property="month", type="string", example="2025-06-10"),
     *                    @OA\Property(property="uom", type="string", example="kg"),
     *                    @OA\Property(property="quantity", type="number", example=90),
     *                    @OA\Property(property="asp", type="number", example=5),
     *                    @OA\Property(property="total_value", type="number", format="float", example=90.5),
     *                    @OA\Property(property="created_at", type="string", format="date-time"),
     *                   @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="entries", type="array", @OA\Items(type="string", example="You must provide at least one entry to update.")),
     *                 @OA\Property(property="entries.0.id", type="array", @OA\Items(type="string", example="The entries.0.id field is required."))
     *             ),
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function bulkUpdate(BulkUpdateKpiRequest $request)
    {
        $entries = $this->service->bulkUpdate($request->validated()['entries']);

        return $this->success(
            KpiEntryResource::collection($entries)->response()->getData(true),
            'KPI entries updated successfully.',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/kpi/{id}/restore",
     *     summary="Restore a soft-deleted KPI entry",
     *     tags={"KPIs"},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the soft-deleted KPI entry",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="KPI Entry restored successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="KPI Entry restored successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=5),
     *                 @OA\Property(property="code", type="string", example="KPI-0005"),
     *                 @OA\Property(property="month", type="string", example="2025-06-01"),
     *                 @OA\Property(property="uom", type="string", example="pcs"),
     *                 @OA\Property(property="quantity", type="integer", example=100),
     *                 @OA\Property(property="asp", type="number", format="float", example=10.25),
     *                 @OA\Property(property="total_value", type="number", format="float", example=1025.00),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred"),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function restore($id)
    {
        $kpi = $this->service->restore($id);
        return $this->success(
            new KpiEntryResource($kpi),
            'KPI Entry restored successfully.',
            200
        );
    }
}
