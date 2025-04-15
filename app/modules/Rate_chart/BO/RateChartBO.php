<?php

namespace App\modules\Rate_chart\BO;

class RateChartBO
{
    protected ?int $userId = null;
    protected array $rateDataArray;
    protected ?int $rateId = null;
    private ?float $weight = null;
    protected ?float $rateAmount = null;
    private ?string $createdDate = null;
    private ?string $updatedDate = null;



    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setRateDataArray(array $rateDataArray): void
    {
        $this->rateDataArray = $rateDataArray;
    }

    public function getRateDataArray(): array
    {
        return $this->rateDataArray;
    }

    public function setRateId(?int $rateId): void
    {
        $this->rateId = $rateId;
    }

    public function getRateId(): ?int
    {
        return $this->rateId;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }

    public function setRateAmount(?float $rateAmount): void
    {
        $this->rateAmount = $rateAmount;
    }
    public function getRateAmount(): ?float
    {
        return $this->rateAmount;
    }

    public function getCreatedDate(): ?string
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?string $createdDate): self
    {
        $this->createdDate = $createdDate;
        return $this;
    }

    public function getUpdatedDate(): ?string
    {
        return $this->updatedDate;
    }

    public function setUpdatedDate(?string $updatedDate): self
    {
        $this->updatedDate = $updatedDate;
        return $this;
    }



    public function toArray(): array
    {
        $data = [];

        if (isset($this->userId)) {
            $data['user_id'] = $this->userId;
        }

        if (isset($this->weight)) {
            $data['weight'] = $this->weight;
        }

        if (isset($this->createdDate)) {
            $data['created_date'] = $this->createdDate;
        }

        if (isset($this->updatedDate)) {
            $data['updated_date'] = $this->updatedDate;
        }

        if (isset($this->rateId)) {
            $data['rate_id'] = $this->rateId;
        }

        if (isset($this->rateAmount)) {
            $data['rate_amount'] = $this->rateAmount;
        }

        return $data;
    }
}
