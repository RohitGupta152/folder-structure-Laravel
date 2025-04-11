/* public function createOrder(OrderBO $orderBO)
    {
        try {
            DB::beginTransaction();

            $currentUserId = Auth::id();
            // dd($currentUserId);

            // Check wallet balance
            $currentWalletBalance = $this->userRepositoryInterface->getWalletBalance($currentUserId);

            $walletValidation = $this->orderValidator->validateWalletBalance($currentWalletBalance);
            if (!empty($walletValidation)) {
                return $walletValidation;
            }

            $orderExistsValidation = $this->orderValidator->validateOrderExists($orderBO->getOrderNo(), $currentUserId);
            if (!empty($orderExistsValidation)) {
                return $orderExistsValidation;
            }

            list($totalOrderAmount, $totalProductQuantity) = OrderHelper::calculateTotalAmountAndQuantity($orderBO->getProducts());

            $calculatedChargingWeight = OrderHelper::calculateChargingWeight(
                $orderBO->getWeight(),
                $orderBO->getLength(),
                $orderBO->getWidth(),
                $orderBO->getHeight(),
            );

            $applicableChargeAmount = $this->getApplicableRate($calculatedChargingWeight, $currentUserId);

            if ($applicableChargeAmount === null) {
                return [
                    'status' => 'error',
                    'message' => 'No applicable rate found for this weight.',
                    'status_code' => 400
                ];
            }

            if ($currentWalletBalance < $applicableChargeAmount) {
                return [
                    'status' => 'error',
                    'message' => 'Insufficient Wallet Balance. Kindly Recharge your Wallet.',
                    'status_code' => 400
                ];
            }

            // Deduct wallet balance
            $newBalance = $currentWalletBalance - $applicableChargeAmount;
            $this->userRepositoryInterface->updateWalletBalance($currentUserId, $newBalance);

            // Prepare order data
            $orderBO->prepareOrderData(
                $totalOrderAmount,
                $totalProductQuantity,
                $applicableChargeAmount,
                $calculatedChargingWeight
            );
            // dd($orderBO);

            // Store order and products
            $order = $this->storeOrder($orderBO);

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Order created successfully',
                'status_code' => 200
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => 'Failed to create order: ' . $exception->getMessage(),
                'status_code' => 500
            ];
        }
    }
 */




















 /*     private function storeOrder(OrderBO $orderBO)
    {
        $order = $this->orderRepositoryInterface->createOrder($orderBO->toArray());

        foreach ($orderBO->getProducts() as $product) {
            $this->storeProduct($order->id, $orderBO->getOrderNo(), $product);
        }

        return $order;
    }

    private function storeProduct(int $orderId, string $orderNo, array $product)
    {
        $productData = [
            'order_table_id' => $orderId,
            'order_no' => $orderNo,
            'product_name' => $product['product_name'],
            'price' => $product['price'],
            'quantity' => $product['quantity'],
            'created_date'   => Carbon::now(),
            'updated_date'   => Carbon::now(),
        ];

        $this->productRepositoryInterface->storeProducts($productData);
    } */





























    /*     protected $orderValidator;
    protected $orderHelper;
    protected $orderLogger;

    public function __construct(
        OrderValidator $orderValidator,
        OrderHelper $orderHelper,
        OrderLogger $orderLogger
    ) {
        $this->orderValidator = $orderValidator;
        $this->orderHelper = $orderHelper;
        $this->orderLogger = $orderLogger;
    }

    public function createOrder(OrderBO $orderBO)
    {
        try {
            DB::beginTransaction();

            $currentUserId = Auth::id();

            $currentWalletBalance = $this->orderLogger->getWalletBalance($currentUserId);
            $this->orderValidator->validateWalletBalance($currentWalletBalance);

            $checkExistingOrder = $this->orderLogger->checkExistingOrder($orderBO->getOrderNo(), $currentUserId);
            $orderExists = $checkExistingOrder->isNotEmpty();
            $this->orderValidator->validateOrderExists($orderExists);

            list($totalOrderAmount, $totalProductQuantity) = $this->orderHelper->calculateTotalAmountAndQuantity($orderBO->getProducts());

            $calculatedChargingWeight = $this->orderHelper->calculateChargingWeight(
                $orderBO->getWeight(),
                $orderBO->getLength(),
                $orderBO->getWidth(),
                $orderBO->getHeight()
            );

            $applicableChargeAmount = $this->orderLogger->getApplicableRate($calculatedChargingWeight, $currentUserId);

            // dd($applicableChargeAmount);
            $this->orderValidator->validateApplicableRate($applicableChargeAmount);
            $this->orderValidator->validateChargeAmount($currentWalletBalance, $applicableChargeAmount);

            $newBalance = $currentWalletBalance - $applicableChargeAmount;
            $this->orderLogger->updateWalletBalance($currentUserId, $newBalance);

            $orderBO->prepareOrderData(
                $totalOrderAmount,
                $totalProductQuantity,
                $applicableChargeAmount,
                $calculatedChargingWeight
            );

            $order = $this->orderLogger->createOrderWithProducts($orderBO);

            if ($order) {
                $this->orderLogger->logOrderCreation($orderBO->toArray());
            }

            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Order created successfully',
                'status_code' => 200
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => 'Failed to create order: ' . $exception->getMessage(),
                'status_code' => 500
            ];
        }
    } */











