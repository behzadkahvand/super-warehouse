<?php

namespace App\Entity;

use App\Entity\Common\Timestampable;
use App\Repository\AdminRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Table(name="admins")
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 *
 * * @UniqueEntity(
 *     fields={"email"},
 *     message="This email already exists.",
 * )
 *
 * @UniqueEntity(
 *     fields={"mobile"},
 *     message="This mobile already exists.",
 * )
 */
class Admin implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @Groups({
     *     "admin.list",
     *     "admin.read",
     *     "pullList.read",
     *     "pick.list.index",
     *     "pullList.locator.assign",
     *     "locator.index",
     *     "pick.list.bug.report.read",
     * })
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"admin.list", "admin.read", "pick.list.index", "pullList.locator.assign", "pick.list.bug.report.read",})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Groups({"admin.list", "admin.read", "pick.list.index", "pullList.locator.assign", "pick.list.bug.report.read",})
     */
    private $family;

    /**
     * @ORM\Column(type="boolean")
     *
     * @Groups({"admin.list", "admin.read"})
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Groups({"admin.list", "admin.read","pullList.read",})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     *
     * @Groups({"admin.list", "admin.read"})
     */
    private $mobile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity=PullList::class, mappedBy="locator", fetch="EXTRA_LAZY")
     */
    private $pullLists;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family;
    }

    public function setFamily(string $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(string $mobile): self
    {
        $this->mobile = $mobile;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles()
    {
        return ['ROLE_ADMIN'];
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return Collection|PullList[]
     */
    public function getPullLists(): Collection
    {
        return $this->pullLists;
    }

    /**
     * @Groups({"locator.index",})
     */
    public function getFullName(): ?string
    {
        return $this->getName() . ' ' . $this->getFamily();
    }

    /**
     * @Groups({"locator.index",})
     */
    public function getPullListsCount(): int
    {
        return $this->getPullLists()->count();
    }
}
