<?php

namespace App\Modules\Auth\BO;

class AuthBO
{
    protected ?int $id = null;
    protected ?string $name = null;
    protected ?string $email = null;
    protected ?string $password = null;
    protected ?string $created_date = null;
    protected ?string $updated_date = null;
    protected ?int $user_type = 3;


    // Getter
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function getUserType(): ?int
    {
        return $this->user_type;
    }
    public function getCreatedDate(): ?string
    {
        return $this->created_date;
    }
    public function getUpdatedDate(): ?string
    {
        return $this->updated_date;
    }



    // Setter
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }
    public function setUserType(?int $user_type): self
    {
        $this->user_type = $user_type ?? 3;
        return $this;
    }
    public function setCreatedDate(?string $created_date): self
    {
        $this->created_date = $created_date;
        return $this;
    }
    public function setUpdatedDate(?string $updated_date): self
    {
        $this->updated_date = $updated_date;
        return $this;
    }




    public function toArray(): array
    {
        $data = [];

        if (isset($this->id)) {
            $data['id'] = ucwords(strtolower($this->id));
        }

        if (isset($this->name)) {
            $data['name'] = ucwords(strtolower($this->name));
        }

        if (isset($this->email)) {
            $data['email'] = strtolower($this->email);
        }

        if (isset($this->password)) {
            $data['password'] = bcrypt($this->password);
        }

        if (isset($this->user_type)) {
            $data['user_type'] = $this->user_type;
        }

        if (isset($this->created_date)) {
            $data['created_date'] = $this->created_date;
        }

        if (isset($this->updated_date)) {
            $data['updated_date'] = $this->updated_date;
        }

        return $data;
    }
}
