<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartRepository::class)
 */
class Cart
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=user::class, inversedBy="product_id", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;

    /**
     * @ORM\ManyToMany(targetEntity=products::class, inversedBy="carts")
     */
    private $product;

    public function __construct()
    {
        $this->product = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?user
    {
        return $this->user_id;
    }

    public function setUserId(user $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return Collection<int, products>
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(products $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product[] = $product;
        }

        return $this;
    }

    public function removeProduct(products $product): self
    {
        $this->product->removeElement($product);

        return $this;
    }
}
