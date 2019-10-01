<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email", message="validators.user.email.not_repeat")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="validators.user.name.not_blank")
     * @Assert\NotNull(message="validators.user.name.not_null")
     * @Assert\Length(
     *     min = 2,
     *     max = 20,
    *     minMessage = "validators.user.name.lengthMin",
     *     maxMessage = "validators.user.name.lengthMax"
     * )
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="validators.user.surname.not_blank")
     * @Assert\NotNull(message="validators.user.surname.not_null")
     * @Assert\Length(
     *     min = 4,
     *     max = 50,
     *     minMessage = "validators.user.surname.lengthMin",
     *     maxMessage = "validators.user.surname.lengthMax"
     * )
     */
    private $apellidos;
//  "The email {{ value }} is not a valid email."
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="validators.user.email.not_blank")
     * @Assert\NotNull(message="validators.user.email.not_null")
     * @Assert\Email(
     *     message = "validators.user.email.email",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="validators.user.fechaNa.not_blank")
     * @Assert\NotNull(message="validators.user.fechaNa.not_null")
     * @Assert\LessThanOrEqual("today")
     * @Assert\DateTime
     */
    private $fechaNacimiento;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="validators.user.pass.not_blank")
     * @Assert\NotNull(message="validators.user.pass.not_null")
     * @Assert\Length(
     *     min = 5,
     *     max = 50,
     *     minMessage = "validators.user.surname.lengthMin",
     *     maxMessage = "validators.user.surname.lengthMax"
     * )
     */
    private $password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(string $apellidos): self
    {
        $this->apellidos = $apellidos;

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

    public function getFechaNacimiento(): ?\DateTimeInterface
    {
        return $this->fechaNacimiento;
    }

    public function setFechaNacimiento(\DateTimeInterface $fechaNacimiento): self
    {
        $this->fechaNacimiento = $fechaNacimiento;

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
    
}
