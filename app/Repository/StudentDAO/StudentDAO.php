<?php 

namespace App\Repository\StudentDAO;

class StudentDAO
{

    
    protected ?int $id = null;
    protected ?string $name = null;
    protected ?string $email = null;
    protected ?int $age = null;
    protected ?string $course = null;
    protected ?string $created_date = null;
    protected ?string $updated_date = null;


    // Getters
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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function getCourse(): ?string
    {
        return $this->course;
    }

    public function getCreatedDate(): ?string
    {
        return $this->created_date;
    }

    public function getUpdatedDate(): ?string
    {
        return $this->updated_date;
    }

    // Setters
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

    public function setAge(?int $age): self
    {
        $this->age = $age;
        return $this;
    }

    public function setCourse(?string $course): self
    {
        $this->course = $course;
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

    /**
     * Convert the BO to an array. @Object - Array
     *
     * @return array
     */
    
     // public function toArray(): array
    // {
    //     return [
    //         'id'            => $this->id,
    //         'name'          => ucwords(strtolower($this->name)),
    //         'email'         => strtolower($this->email),
    //         'age'           => (int) $this->age,
    //         'course'        => strtoupper($this->course),
    //         'created_date'  => $this->created_date ?? now(),
    //         'updated_date'  => $this->updated_date ?? now(),
    //     ];
    // }

    public function toArray(): array
    {
        $data = [];

        if (isset($this->id)) {
            $data['id'] = $this->id;
        }

        if (isset($this->name)) {
            $data['name'] = ucwords(strtolower($this->name));
        }

        if (isset($this->email)) {
            $data['email'] = strtolower($this->email);
        }

        if (isset($this->age)) {
            $data['age'] = (int) $this->age;
        }

        if (isset($this->course)) {
            $data['course'] = strtoupper($this->course);
        }

        $data['created_date'] = $this->created_date ?? now();
        $data['updated_date'] = $this->updated_date ?? now();

        return $data;
    }

    /**
     * Create a StudentBO from an array.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $student = new self();
        $student->setId($data['id'] ?? null);
        $student->setName($data['name'] ?? null);
        $student->setEmail($data['email'] ?? null);
        $student->setAge($data['age'] ?? null);
        $student->setCourse($data['course'] ?? null);
        $student->setCreatedDate($data['created_date'] ?? null);
        $student->setUpdatedDate($data['updated_date'] ?? null);

        return $student;
    }

}