<?php
namespace ICS\SsiBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(schema="ssi")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 */
abstract class AccountProfile
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
     * @ORM\OneToOne(targetEntity=Account::class, mappedBy="profile")
     */
    private $account;

    /**
     * Get identifier
     *
     * @return  integer
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set identifier
     *
     * @param  integer  $id  Identifier
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of account
     */ 
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * Set the value of account
     *
     * @return  self
     */ 
    public function setAccount($account)
    {
        $this->account = $account;

        return $this;
    }

    public function __toString()
    {
        return $this->getAccount()->getUsername();
    }
}