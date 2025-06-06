<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Resources\SupplierResource;
use App\Services\SupplierService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Suppliers",
 *     description="API Endpoints for Supplier management"
 * )
 */
class SupplierController extends Controller
{

    use ApiResponseTrait;

    /**
     * @var SupplierService
     */
    protected $service;

    public function __construct(SupplierService $service)
    {
        $this->service = $service;
    }


    /**
     * @OA\Get(
     *     path="/api/suppliers",
     *     summary="Get all suppliers",
     *     tags={"Suppliers"},
     *     @OA\Response(
     *         response=200,
     *         description="List of suppliers",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Suppliers retrieved successfully"),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="http://domain_name.com/api/suppliers?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://domainname.com/api/suppliers?page=4"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", example="http://domain_name.com/api/suppliers?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=4),
     *                 @OA\Property(property="path", type="string", example="http://domainname.com/api/suppliers"),
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
     *              @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                    @OA\Property(property="id", type="integer"),
     *                    @OA\Property(property="code", type="string", example="SUP-0001"),
     *                    @OA\Property(property="name", type="string"),
     *                    @OA\Property(property="email", type="string", nullable=true),
     *                    @OA\Property(property="phone", type="string", nullable=true),
     *                    @OA\Property(property="created_at", type="string", format="datetime"),
     *                    @OA\Property(property="updated_at", type="string", format="datetime"),
     *                 )
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
    public function index()
    {
        $suppliers = $this->service->getAll();
        return $this->success(
            SupplierResource::collection($suppliers)->response()->getData(true),
            'Suppliers retrieved successfully',
            200
        );
    }

    /**
     * @OA\Post(
     *     path="/api/suppliers",
     *     summary="Create a new supplier",
     *     tags={"Suppliers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "name"},
     *             @OA\Property(property="name", type="string", example="Supplier Name"),
     *             @OA\Property(property="email", type="string", nullable=true, example="supplier@gmail.com"),
     *             @OA\Property(property="phone", type="string", nullable=true, example="+1234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Supplier created successfully",
     *         @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Supplier created successfully"),
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="code", type="string"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="email", type="string", nullable=true),
     *                  @OA\Property(property="phone", type="string", nullable=true),
     *                  @OA\Property(property="created_at", type="string", format="datetime"),
     *                  @OA\Property(property="updated_at", type="string", format="datetime")
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
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required."))
     *             ),
     *             @OA\Property(property="data", type="string", example="")
     *         )
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
    public function store(StoreSupplierRequest $request)
    {
        $supplier = $this->service->store($request->validated());
        return $this->success(
            new SupplierResource($supplier),
            'Supplier created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/suppliers/{id}",
     *     summary="Get a specific supplier",
     *     tags={"Suppliers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier details",
     *         @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Supplier retrieved successfully"),
     *              @OA\Property(
     *                 property="data",
     *                 type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="code", type="string"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="email", type="string", nullable=true),
     *                  @OA\Property(property="phone", type="string", nullable=true),
     *                  @OA\Property(property="created_at", type="string", format="datetime"),
     *                  @OA\Property(property="updated_at", type="string", format="datetime")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Supplier not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Supplier not found"),
     *             @OA\Property(property="data", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred."),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function show(string $id)
    {
        try {
            $supplier = $this->service->find($id);
            return $this->success(
                new SupplierResource($supplier),
                'Supplier retrieved successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error('Supplier not found.', 404);
        } catch (\Exception $e) {
            return $this->error('An unexpected error occurred.', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/suppliers/{id}",
     *     summary="Update a supplier",
     *     tags={"Suppliers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "name"},
     *             @OA\Property(property="name", type="string", example="Updated Supplier Name"),
     *             @OA\Property(property="email", type="string", nullable=true, example="updateSupplier@gmail.com"),
     *             @OA\Property(property="phone", type="string", nullable=true, example="+0987654321")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Supplier updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="code", type="string"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="email", type="string", nullable=true),
     *                  @OA\Property(property="phone", type="string", nullable=true),
     *                  @OA\Property(property="created_at", type="string", format="datetime"),
     *                  @OA\Property(property="updated_at", type="string", format="datetime")
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
     *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="The name field is required.")),
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email must be a valid email address."))
     *             ),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Supplier not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Supplier not found"),
     *             @OA\Property(property="data", type="string", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred."),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function update(StoreSupplierRequest $request, string $id)
    {
        try {
            $supplier = $this->service->update($id, $request->validated());
            return $this->success(
                new SupplierResource($supplier),
                'Supplier updated successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error('Supplier not found.', 404);
        } catch (\Exception $e) {
            return $this->error('An unexpected error occurred.', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/suppliers/{id}",
     *     summary="Delete a supplier",
     *     tags={"Suppliers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Deleted successfully"),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Supplier not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Supplier not found"),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred."),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function destroy(string $id)
    {
        try {
            $this->service->delete($id);
            return $this->success(
                '',
                'Supplier deleted successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error('Supplier not found.', 404);
        } catch (\Exception $e) {
            return $this->error('An unexpected error occurred.', 500);
        }
    }

     /**
     * @OA\Post(
     *     path="/api/suppliers/restore/{id}",
     *     tags={"Suppliers"},
     *     summary="Restore a soft-deleted supplier",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Supplier ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *      response=200, 
     *      description="Success",
     *       @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Supplier restored successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="string",
     *                 example=""
     *             )
     *         )
     *      ),
     *     @OA\Response(
     *      response=404, 
     *      description="Not Found",
     *       @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Supplier not found"),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *      ),
     *      @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred."),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function restore($id)
    {
        try {
            $this->service->restore($id);
            return $this->success(
                '',
                'Supplier restored successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error('Supplier not found.', 404);
        } catch (Exception $e) {
            return $this->error('An unexpected error occurred.', 500);
        }
    }
}
