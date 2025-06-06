<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignSuppliersRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SupplierResource;
use App\Services\ProductService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints for Product management"
 * )
 */
class ProductController extends Controller
{
    use ApiResponseTrait;

    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }



    /**
     * @OA\Get(
     *     path="/api/products",
     *     summary="Get all products",
     *     tags={"Products"},
     *     @OA\Response(
     *         response=200,
     *         description="List of products",
     *         @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Products retrieved successfully"),
     *              @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="http://domain_name.com/api/products?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://domainname.com/api/products?page=4"),
     *                 @OA\Property(property="prev", type="string", nullable=true, example=null),
     *                 @OA\Property(property="next", type="string", example="http://domain_name.com/api/products?page=2")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=4),
     *                 @OA\Property(property="path", type="string", example="http://domainname.com/api/products"),
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
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer"),
     *                      @OA\Property(property="name", type="string", example="Sample Product"),
     *                      @OA\Property(property="code", type="string", example="PROD-0001"),
     *                      @OA\Property(property="description", type="string", nullable=true, example="This is a sample product description"),
     *                      @OA\Property(property="uom", type="string", description="Unit of Measurement"),
     *                      @OA\Property(property="created_at", type="string", format="datetime"),
     *                      @OA\Property(property="updated_at", type="string", format="datetime"),
     * 
     *                      @OA\Property(
     *                          property="suppliers",
     *                          type="array",
     *                          @OA\Items(
     *                              type="object",
     *                              @OA\Property(property="id", type="integer", example="1"),
     *                              @OA\Property(property="code", type="string", example="SUP-0001"),
     *                              @OA\Property(property="name", type="string", example="zaman")
     *                          )
     *                      ),

     *                      @OA\Property(
     *                          property="customers",
     *                          type="array",
     *                          @OA\Items(
     *                              type="object",
     *                              @OA\Property(property="id", type="integer", example="1"),
     *                              @OA\Property(property="code", type="string", example="CUS-0001"),
     *                              @OA\Property(property="name", type="string", example="Mostafijur")
     *                          )
     *                      )
     *                  )
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
    public function index()
    {
        $products = $this->service->getAll();
        return $this->success(
            ProductResource::collection($products)->response()->getData(true),
            'Products retrieved successfully',
            201
        );
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Create a new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "name", "uom"},
     *             @OA\Property(property="name", type="string", example="Sample Product"),
     *             @OA\Property(property="description", type="string", nullable=true, example="This is a sample product description"),
     *             @OA\Property(property="uom", type="string", description="Unit of Measurement", example="5"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product created successfully"),
     *              @OA\Property(
     *                  property="data", 
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string", example="Sample Product"),
     *                  @OA\Property(property="code", type="string", example="PROD-0001"),
     *                  @OA\Property(property="description", type="string", nullable=true, example="This is a sample product description"),
     *                  @OA\Property(property="uom", type="string", description="Unit of Measurement"),
     *                  @OA\Property(property="created_at", type="string", format="datetime"),
     *                  @OA\Property(property="updated_at", type="string", format="datetime"),
     *              ), 
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
     *                 @OA\Property(property="uom", type="array", @OA\Items(type="string", example="The uom field is required."))
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
    public function store(StoreProductRequest $request)
    {
        $product = $this->service->store($request->validated());
        return $this->success(
            new ProductResource($product),
            'Product created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     summary="Get a specific product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product details",
     *         @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Product retrieved successfully"),
     *              @OA\Property(
     *                  property="data", 
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="code", type="string"),
     *                  @OA\Property(property="name", type="string"),
     *                  @OA\Property(property="description", type="string", nullable=true),
     *                  @OA\Property(property="uom", type="string"),
     *                  @OA\Property(property="created_at", type="string", format="datetime"),
     *                  @OA\Property(property="updated_at", type="string", format="datetime"),
     *                  @OA\Property(
     *                      property="customers",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="code", type="string"),
     *                          @OA\Property(property="name", type="string")
     *                      )
     *                  ),
     *                  @OA\Property(
     *                      property="suppliers",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="id", type="integer"),
     *                          @OA\Property(property="code", type="string"),
     *                          @OA\Property(property="name", type="string")
     *                      )
     *                  )
     *              ),   
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
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
    public function show(string $id)
    {
        try {
            $product = $this->service->find($id);
            return $this->success(
                new ProductResource($product),
                'Product retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error('An error occurred', 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     summary="Update a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"code", "name", "uom"},
     *             @OA\Property(property="name", type="string", example="Updated Product Name"),
     *             @OA\Property(property="description", type="string", nullable=true, example="Updated product description"),
     *             @OA\Property(property="uom", type="string", description="Unit of Measurement", example="3")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(
     *                  property="data", 
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="name", type="string", example="Sample Product"),
     *                  @OA\Property(property="code", type="string", example="PROD-0001"),
     *                  @OA\Property(property="description", type="string", nullable=true, example="This is a sample product description"),
     *                  @OA\Property(property="uom", type="string", description="Unit of Measurement"),
     *                  @OA\Property(property="created_at", type="string", format="datetime"),
     *                  @OA\Property(property="updated_at", type="string", format="datetime"),
     *              ),
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
     *                 @OA\Property(property="uom", type="array", @OA\Items(type="string", example="The uom field is required."))
     *             ),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),
     *    @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
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
    public function update(StoreProductRequest $request, string $id)
    {
        try {
            $product = $this->service->update($id, $request->validated());
            return $this->success(
                new ProductResource($product),
                'Product updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Product not found', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     summary="Delete a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Deleted successfully"),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
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
    public function destroy(string $id)
    {
        try {
            $this->service->delete($id);

            return $this->success(
                null,
                'Product deleted successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to delete product', [
                'product_id' => $id,
                'error' => $e->getMessage(),
            ]);

            return $this->error('An unexpected error occurred', 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/product-suppliers/{productId}",
     *     summary="Assign suppliers to a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"supplier_ids"},
     *             @OA\Property(
     *                 property="supplier_ids",
     *                 type="array",
     *                 @OA\Items(type="integer", example=1)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Suppliers assigned successfully",
     *         @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=true),
     *              @OA\Property(property="message", type="string", example="Suppliers assigned successfully"),
     *              @OA\Property(
     *                  property="data",   
     *                  type="object",
     *                  @OA\Property(property="id", type="integer"),
     *                  @OA\Property(property="code", type="string", example="SUP-0001"),
     *                  @OA\Property(property="name", type="string", example="Sample Product"),
     *                  @OA\Property(property="email", type="string", example="example@gmail.com"),
     *                  @OA\Property(property="phone", type="string", description="+88017548547"),
     *                  @OA\Property(property="created_at", type="string", format="datetime"),
     *                  @OA\Property(property="updated_at", type="string", format="datetime"),
     *                  
     *              )
     *         )
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
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
    public function assignSuppliers(AssignSuppliersRequest $request, $productId)
    {
        try {
            $this->service->assignSuppliers($productId, $request->validated('supplier_ids'));

            return $this->success(
                null,
                'Suppliers assigned successfully'
            );
        } catch (\Exception $e) {

            Log::error('Error assigning suppliers', ['error' => $e->getMessage()]);
            return $this->error('An unexpected error occurred', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/product-suppliers/{productId}",
     *     summary="Get all suppliers assigned to a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of assigned suppliers",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Assigned suppliers retrieved successfully"),
     *             @OA\Property(
     *                  property="data", 
     *                  type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="id", type="integer", example=1),
     *                      @OA\Property(property="code", type="string", example="SUP-001"),
     *                      @OA\Property(property="name", type="string", example="XYZ Supplier")
     *                  )
     *              )
     *         )
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function getAssignedSuppliers($productId)
    {
        try {
            $suppliers = $this->service->getAssignedSuppliers($productId);

            return $this->success( $suppliers, 'Assigned suppliers retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Failed to get assigned suppliers', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return $this->error('An unexpected error occurred', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/product-suppliers/{productId}/{supplierId}",
     *     summary="Remove a supplier from a product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         required=true,
     *         description="ID of the product",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="supplierId",
     *         in="path",
     *         required=true,
     *         description="ID of the supplier to remove",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Supplier removed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Supplier removed successfully"),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),
     *      @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
     *             @OA\Property(property="errors", type="string", example=""),
     *             @OA\Property(property="data", type="string", example="")
     *         )
     *     )
     * )
     */
    public function removeSupplier($productId, $supplierId)
    {
        try {
            $this->service->removeSupplier($productId, $supplierId);

            return $this->success(
                "",
                'Supplier removed successfully'
            );
        } catch (\Exception $e) {
            Log::error('Failed to remove supplier', [
                'product_id' => $productId,
                'supplier_id' => $supplierId,
                'error' => $e->getMessage(),
            ]);

            return $this->error('An unexpected error occurred', 500);
        }
    }
}
