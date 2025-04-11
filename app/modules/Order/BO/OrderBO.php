<?php

namespace App\modules\Order\BO;

use Carbon\Carbon;

class OrderBO
{
    protected ?int $user_id = null;
    protected ?string $order_no = null;
    protected ?string $customer_name = null;
    protected ?string $email = null;
    protected ?string $contact_no = null;
    protected ?string $address1 = null;
    protected ?string $address2 = null;
    protected ?string $pin_code = null;
    protected ?string $city = null;
    protected ?string $state = null;
    protected ?string $country = null;
    protected ?float $weight = null;
    protected ?float $length = null;
    protected ?float $width = null;
    protected ?float $height = null;
    protected ?float $total_amount = null;
    protected ?int $total_qty = null;
    protected ?float $charged_amount = null;
    protected ?float $charged_weight = null;
    protected ?string $created_date = null;
    protected ?string $updated_date = null;
    protected ?array $products = null;




    // Getters
    public function getUserId(): ?int
    {
        return $this->user_id;
    }
    public function getOrderNo(): ?string
    {
        return $this->order_no;
    }
    public function getCustomerName(): ?string
    {
        return $this->customer_name;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function getContactNo(): ?string
    {
        return $this->contact_no;
    }
    public function getAddress1(): ?string
    {
        return $this->address1;
    }
    public function getAddress2(): ?string
    {
        return $this->address2;
    }
    public function getPinCode(): ?string
    {
        return $this->pin_code;
    }
    public function getCity(): ?string
    {
        return $this->city;
    }
    public function getState(): ?string
    {
        return $this->state;
    }
    public function getCountry(): ?string
    {
        return $this->country;
    }
    public function getWeight(): ?float
    {
        return $this->weight;
    }
    public function getLength(): ?float
    {
        return $this->length;
    }
    public function getWidth(): ?float
    {
        return $this->width;
    }
    public function getHeight(): ?float
    {
        return $this->height;
    }
    public function getTotalAmount(): ?float
    {
        return $this->total_amount;
    }
    public function getTotalQty(): ?int
    {
        return $this->total_qty;
    }
    public function getChargedAmount(): ?float
    {
        return $this->charged_amount;
    }
    public function getChargedWeight(): ?float
    {
        return $this->charged_weight;
    }
    public function getCreatedDate(): ?string
    {
        return $this->created_date;
    }
    public function getUpdatedDate(): ?string
    {
        return $this->updated_date;
    }
    public function getProducts(): ?array
    {
        return $this->products;
    }


















    // Setters
    public function setUserId(?int $user_id): self
    {
        $this->user_id = $user_id;
        return $this;
    }
    public function setOrderNo(?string $order_no): self
    {
        $this->order_no = $order_no;
        return $this;
    }
    public function setCustomerName(?string $customer_name): self
    {
        $this->customer_name = $customer_name;
        return $this;
    }
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function setContactNo(?string $contact_no): self
    {
        $this->contact_no = $contact_no;
        return $this;
    }
    public function setAddress1(?string $address1): self
    {
        $this->address1 = $address1;
        return $this;
    }
    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;
        return $this;
    }
    public function setPinCode(?string $pin_code): self
    {
        $this->pin_code = $pin_code;
        return $this;
    }
    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }
    public function setState(?string $state): self
    {
        $this->state = $state;
        return $this;
    }
    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }
    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }
    public function setLength(?float $length): self
    {
        $this->length = $length;
        return $this;
    }
    public function setWidth(?float $width): self
    {
        $this->width = $width;
        return $this;
    }
    public function setHeight(?float $height): self
    {
        $this->height = $height;
        return $this;
    }
    public function setTotalAmount(?float $amount): self
    {
        $this->total_amount = $amount;
        return $this;
    }
    public function setTotalQty(?int $qty): self
    {
        $this->total_qty = $qty;
        return $this;
    }
    public function setChargedAmount(?float $charged): self
    {
        $this->charged_amount = $charged;
        return $this;
    }
    public function setChargedWeight(?float $chargedWeight): self
    {
        $this->charged_weight = $chargedWeight;
        return $this;
    }
    public function setCreatedDate(?string $date): self
    {
        $this->created_date = $date;
        return $this;
    }
    public function setUpdatedDate(?string $date): self
    {
        $this->updated_date = $date;
        return $this;
    }
    public function setProducts(?array $products): self
    {
        $this->products = $products;
        return $this;
    }






    // Convert to array method
    /*     public function toArray(): array
    {
        return [
            'user_id' => $this->user_id,
            'order_no' => $this->order_no,
            'customer_name' => $this->customer_name,
            'email' => $this->email,
            'contact_no' => $this->contact_no,
            'address1' => $this->address1,
            'address2' => $this->address2,
            'pin_code' => $this->pin_code,
            'city' => $this->city,
            'state' => $this->state,
            'country' => $this->country,
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'total_amount' => $this->total_amount,
            'total_qty' => $this->total_qty,
            'charged_amount' => $this->charged_amount,
            'charged_weight' => $this->charged_weight,
            'created_date' => $this->created_date,
            'updated_date' => $this->updated_date,
        ];
    } */

    public function toArray(): array
    {
        $data = [];

        if (isset($this->user_id))         $data['user_id'] = $this->user_id;
        if (isset($this->order_no))        $data['order_no'] = $this->order_no;
        if (isset($this->customer_name))   $data['customer_name'] = $this->customer_name;
        if (isset($this->email))           $data['email'] = $this->email;
        if (isset($this->contact_no))      $data['contact_no'] = $this->contact_no;
        if (isset($this->address1))        $data['address1'] = $this->address1;
        if (isset($this->address2))        $data['address2'] = $this->address2;
        if (isset($this->pin_code))        $data['pin_code'] = $this->pin_code;
        if (isset($this->city))            $data['city'] = $this->city;
        if (isset($this->state))           $data['state'] = $this->state;
        if (isset($this->country))         $data['country'] = $this->country;
        if (isset($this->weight))          $data['weight'] = $this->weight;
        if (isset($this->length))          $data['length'] = $this->length;
        if (isset($this->width))           $data['width'] = $this->width;
        if (isset($this->height))          $data['height'] = $this->height;
        if (isset($this->total_amount))    $data['total_amount'] = $this->total_amount;
        if (isset($this->total_qty))       $data['total_qty'] = $this->total_qty;
        if (isset($this->charged_amount))  $data['charged_amount'] = $this->charged_amount;
        if (isset($this->charged_weight))  $data['charged_weight'] = $this->charged_weight;
        if (isset($this->created_date))    $data['created_date'] = $this->created_date;
        if (isset($this->updated_date))    $data['updated_date'] = $this->updated_date;

        return $data;
    }




    // Additional method to prepare order data 
    // Setter auto-set calculations + timestamps
    public function prepareOrderData(float $totalAmount, int $totalQuantity, float $chargedAmount, float $chargingWeight): self
    {
        $this->total_amount = $totalAmount;
        $this->total_qty = $totalQuantity;
        $this->charged_amount = $chargedAmount;
        $this->charged_weight = $chargingWeight;
        $this->created_date = Carbon::now();
        $this->updated_date = Carbon::now();

        return $this;
    }
}
