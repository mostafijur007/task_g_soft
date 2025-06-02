<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignSuppliersRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
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
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer"),
     *                 @OA\Property(property="name", type="string", example="Sample Product"),
     *                 @OA\Property(property="code", type="string", example="PROD-0001"),
     *                 @OA\Property(property="description", type="string", nullable=true, example="This is a sample product description"),
     *                 @OA\Property(property="uom", type="string", description="Unit of Measurement"),
     *                 @OA\Property(property="created_at", type="string", format="datetime"),
     *                 @OA\Property(property="updated_at", type="string", format="datetime"),
     *                 @OA\Property(property="deleted_at", type="string", format="datetime", nullable=true),

     *                 @OA\Property(
     *                     property="suppliers",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="code", type="string"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 ),

     *                 @OA\Property(
     *                     property="customers",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="code", type="string"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $products = $this->service->getAll();
        return $this->success(
            ProductResource::collection($products),
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
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string", nullable=true),
     *             @OA\Property(property="uom", type="string"),
     *             @OA\Property(property="created_at", type="string", format="datetime"),
     *             @OA\Property(property="updated_at", type="string", format="datetime")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
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
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string", nullable=true),
     *             @OA\Property(property="uom", type="string"),
     *             @OA\Property(property="created_at", type="string", format="datetime"),
     *             @OA\Property(property="updated_at", type="string", format="datetime"),
     *             @OA\Property(
     *                 property="customers",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="code", type="string"),
     *                     @OA\Property(property="name", type="string")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="suppliers",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="code", type="string"),
     *                     @OA\Property(property="name", type="string")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
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
            return $this->error('Server down', 500);
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
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="description", type="string", nullable=true),
     *             @OA\Property(property="uom", type="string"),
     *             @OA\Property(property="created_at", type="string", format="datetime"),
     *             @OA\Property(property="updated_at", type="string", format="datetime")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
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
     *             @OA\Property(property="message", type="string", example="Deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found"
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
     *             @OA\Property(property="message", type="string", example="Suppliers assigned successfully")
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
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="code", type="string", example="SUP-001"),
     *                 @OA\Property(property="name", type="string", example="XYZ Supplier")
     *             )
     *         )
     *     )
     * )
     */
    public function getAssignedSuppliers($productId)
    {
        try {
            $suppliers = $this->service->getAssignedSuppliers($productId);

            return $this->success($suppliers, 'Assigned suppliers retrieved successfully');
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
     *             @OA\Property(property="message", type="string", example="Supplier removed successfully")
     *         )
     *     )
     * )
     */
    public function removeSupplier($productId, $supplierId)
    {
        try {
            $this->service->removeSupplier($productId, $supplierId);

            return $this->success(
                null,
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
