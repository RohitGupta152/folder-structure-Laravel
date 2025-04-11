// protected $orderRepository;

// public function __construct(OrderRepositoryInterface $orderRepository)
// {
//     $this->orderRepository = $orderRepository;
// }

// public function validateWalletBalance(float $walletBalance): array
// {
//     if ($walletBalance <= 0) {
//         return [
//             'status' => 'error',
//             'message' => $walletBalance == 0
//                 ? 'Recharge your Wallet Balance.'
//                 : 'Negative Wallet Balance. Kindly Recharge your Wallet.',
//             'status_code' => 400
//         ];
//     }

//     return [];
// }

// public function validateOrderExists(string $orderNumber, int $userId): array
// {
//     $existingOrder = $this->orderRepository->checkExistingOrder($orderNumber, $userId);

//     if ($existingOrder->isNotEmpty()) {
//         return [
//             'status' => 'error',
//             'message' => 'You cannot use this Order ID again for the same user.',
//             'status_code' => 400
//         ];
//     }

//     return [];
// }