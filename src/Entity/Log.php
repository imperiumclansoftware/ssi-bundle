<?php

namespace ICS\SsiBundle\Entity;

/**
 * File of Log entity
 *
 * @author David Dutas <david.dutas@gmail.com>
 */

use Doctrine\ORM\Mapping as ORM;

/**
 * Log Entity
 *
 * @package SsiBundle\Entity
 * @ORM\Entity(repositoryClass="ICS\SsiBundle\Repository\LogRepository")
 * @ORM\Table(schema="ssi")
 * @ORM\HasLifecycleCallbacks
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="message", type="text")
     */
    private $message;

    /**
     * @ORM\Column(name="context", type="array")
     */
    private $context;

    /**
     * @ORM\Column(name="level", type="smallint")
     */
    private $level;

    /**
     * @ORM\Column(name="level_name", type="string", length=50)
     */
    private $levelName;

    /**
     * @ORM\Column(name="username", type="string", length=50, nullable=true)
     */
    private $username;

    /**
     * @ORM\Column(name="extra", type="array")
     */
    private $extra;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * Get the value of id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     *
     * @return  self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of extra
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * Set the value of extra
     *
     * @return  self
     */
    public function setExtra($extra)
    {
        $this->extra = $extra;

        return $this;
    }

    /**
     * Get the value of levelName
     */
    public function getLevelName()
    {
        return $this->levelName;
    }

    /**
     * Set the value of levelName
     *
     * @return  self
     */
    public function setLevelName($levelName)
    {
        $this->levelName = $levelName;

        return $this;
    }

    /**
     * Get the value of level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set the value of level
     *
     * @return  self
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get the value of context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set the value of context
     *
     * @return  self
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get the value of message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @return  self
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the value of username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }
}
