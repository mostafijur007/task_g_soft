<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="KPI Management API",
 *      description="Customer, Product, Supplier and KPIEntry CRUD APIs",
 *      @OA\Contact(
 *          email="admin@example.com"
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
        return response()->json($this->service->find($id));
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
}
