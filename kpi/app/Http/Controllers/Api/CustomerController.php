<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="KPI Management API",
 *      description="Customer, Product, Supplier and KPIEntry CRUD APIs",
 *      @OA\Contact(
 *          email="mostafijur.til@gmail.com"
 *      )
 * )
 *
 * @OA\Tag(
 *     name="Customers",
 *     description="Customer API Endpoints"
 * )
 * 
 *   @OA\Schema(
 *     schema="CustomerResource",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="code", type="string", example="CUS-0001"),
 *     @OA\Property(property="name", type="string", example="ACME Inc"),
 *     @OA\Property(property="email", type="string", format="email", nullable=true),
 *     @OA\Property(property="phone", type="string", nullable=true),
 *     @OA\Property(property="address", type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="datetime"),
 * )
 */
class CustomerController extends Controller
{
    use ApiResponseTrait;

    /**
     * @var CustomerService
     */
    protected $service;

    public function __construct(CustomerService $service)
    {
        $this->service = $service;
    }


    /**
     * @OA\Get(
     *     path="/api/customers",
     *     tags={"Customers"},
     *     summary="Get list of customers",
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/CustomerResource")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="An error occurred")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $customers = $this->service->getAll();
        return CustomerResource::collection($customers);
    }

    /**
     * @OA\Post(
     *     path="/api/customers",
     *     tags={"Customers"},
     *     summary="Create a new customer",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Md Mostafijur Rahman"),
     *             @OA\Property(property="email", type="string", format="email", example="mostafijur.til@gmail.com"),
     *             @OA\Property(property="phone", type="string", example="01521245318"),
     *             @OA\Property(property="address", type="string", example="Mirpur, Dhaka, Bangladesh")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(ref="#/components/schemas/CustomerResource")
     *     ),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->service->store($request->validated());
        return $this->success(
            new CustomerResource($customer),
            'Customer created successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/api/customers/{id}",
     *     tags={"Customers"},
     *     summary="Get customer details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/CustomerResource")
     *     ),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(string $id)
    {
        try {
            $customer = $this->service->find($id);
            return $this->success(
                new CustomerResource($customer),
                'Customer retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Customer not found', 404);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/customers/{id}",
     *     tags={"Customers"},
     *     summary="Update customer details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Md Mostafijur Rahman"),
     *             @OA\Property(property="email", type="string", format="email", example="updated.mostafijur@gmail.com"),
     *             @OA\Property(property="phone", type="string", example="01738896884"),
     *             @OA\Property(property="address", type="string", example="Updated Address, Dhaka, Bangladesh")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(ref="#/components/schemas/CustomerResource")
     *     ),
     *     @OA\Response(response=404, description="Not Found"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function update(StoreCustomerRequest $request, string $id)
    {
        try {
            $customer = $this->service->update($id, $request->validated());
            return $this->success(
                new CustomerResource($customer),
                'Customer updated successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Customer not found', 404);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/customers/{id}",
     *     tags={"Customers"},
     *     summary="Soft delete a customer",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(string $id)
    {
        try {
            $this->service->delete($id);
            return $this->success(
                null,
                'Customer deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->error('Customer not found', 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/customers/restore/{id}",
     *     tags={"Customers"},
     *     summary="Restore a soft-deleted customer",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Success"),
     *     @OA\Response(response=404, description="Not Found")
     * )
     */
    public function restore($id)
    {
        try {
            $customer = $this->service->restore($id);
            return $this->success(
                new CustomerResource($customer),
                'Customer restored successfully'
            );
        } catch (Exception $e) {
            return $this->error('Customer not found', 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/customer-products/{customerId}",
     *     summary="Assign products to a customer",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="customerId",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"product_ids"},
     *             @OA\Property(
     *                 property="product_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Products assigned",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Products assigned")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function assignProducts(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'product_ids' => 'required|array',
                'product_ids.*' => 'integer|exists:products,id',
            ]);

            $this->service->assignProducts($id, $validated['product_ids']);

            return $this->success(
                null,
                'Products assigned successfully.'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error('Customer not found.', 404);
        } catch (Exception $e) {
            return $this->error('An unexpected error occurred.', 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/customer-products/{customerId}",
     *     summary="Get products assigned to a customer",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="customerId",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of assigned products",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="code", type="string", example="PROD-0001"),
     *                 @OA\Property(property="name", type="string", example="Sample Product"),
     *                 @OA\Property(property="uom", type="string", example="pcs"),
     *                 @OA\Property(property="description", type="string", nullable=true, example="This is a sample product"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-01T12:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-01-02T12:00:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function getAssignedProducts($id)
    {
        try {
            $products = $this->service->getAssignedProducts($id);

            return $this->success(
                $products,
                'Assigned products retrieved successfully'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error('Customer not found', 404);
        } catch (\Exception $e) {
            return $this->error('An unexpected error occurred', 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/customer-products/{customerId}/{productId}",
     *     summary="Remove a product from a customer",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="customerId",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         required=true,
     *         description="Product ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product removed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Product removed")
     *         )
     *     )
     * )
     */
    public function removeProduct($customerId, $productId)
    {
        try {
            $this->service->removeProduct($customerId, $productId);

            return $this->success(
                null,
                'Product removed successfully from customer'
            );
        } catch (ModelNotFoundException $e) {
            return $this->error('Customer or product not found', 404);
        } catch (\Exception $e) {
            return $this->error('An error occurred while removing the product', 500);
        }
    }
}
