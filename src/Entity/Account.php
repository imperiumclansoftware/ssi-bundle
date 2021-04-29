<?php

namespace ICS\SsiBundle\Entity;

/**
 * File of Account entity
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Doctrine\ORM\Mapping as ORM;
use ICS\SsiBundle\Annotation\Log;
use ICS\SsiBundle\Repository\AccountRepository;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class represent an user account for application access
 *
 * @package SsiBundle\Entity
 * @ORM\Entity(repositoryClass=AccountRepository::class)
 * @ORM\Table(schema="ssi")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @Log(actions={"all"},property="logMessage")
 */
class Account implements UserInterface
{
    /**
     * Identifier
     *
     * @var integer
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * Userame
     *
     * @var string
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;
    /**
     *  EMail address
     *
     * @var string
     * @ORM\Column(type="string", length=255, unique=true, nullable=true)
     */
    private $email;
    /**
     * User firstname
     *
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;
    /**
     *  User lastname
     *
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;
    /**
     * Account Roles
     *
     * @var string[]
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * Encrypt password
     *
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * Account is autocreate by keycloak
     *
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $keycloakCreate=false;

    /**
     * @return int User identifier
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        return (string) $this->password;
    }

    /**
     * Set the value of password
     *
     * @param string $password
     * @return self
     */
    public function setPassword($password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Add role to Account roles
     *
     * @param string $role
     * @return void
     */
    public function AddRole($role)
    {
        $this->roles[] = $role;
        array_unique($this->roles);
    }

    /**
     * Message for log integration
     *
     * @return string
     */
    public function getLogMessage()
    {
        return $this->username.' (#'.$this->getId().')';
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Set the value of id.
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of firstName
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set the value of firstName
     *
     * @return  self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get the value of lastName
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set the value of lastName
     *
     * @return  self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * String Account representation
     *
     * @return string
     */
    public function __toString()
    {
        $name=$this->firstName.' '.$this->lastName;

        if($name=="")
        {
            $name= $this->getUsername();
        }
        return $name;
    }

    /**
     * Get the value of keycloakCreate
     */
    public function getKeycloakCreate()
    {
        return $this->keycloakCreate;
    }

    /**
     * Set the value of keycloakCreate
     *
     * @return  self
     */
    public function setKeycloakCreate($keycloakCreate)
    {
        $this->keycloakCreate = $keycloakCreate;

        return $this;
    }
}
