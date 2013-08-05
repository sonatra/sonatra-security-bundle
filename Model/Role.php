<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\SecurityBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * This is the domain class for the Role object.
 *
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
abstract class Role implements RoleHierarchisableInterface, \Serializable
{
    protected $id;
    protected $name;
    protected $parents;
    protected $children;
    protected $authorization;

    /**
     * Constructor.
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;

        $this->parents = new ArrayCollection();
        $this->children = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRole()
    {
        return $this->name;
    }

    /**
     * Gets the role name.
     *
     * @return string the role name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the role name.
     *
     * @param string $name
     *
     * @return Role the current object
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addParent(RoleHierarchisableInterface $role)
    {
        $role->addChild($this);
        $this->parents->add($role);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeParent(RoleHierarchisableInterface $parent)
    {
        if ($this->getParents()->contains($parent)) {
            $this->getParents()->removeElement($parent);
            $parent->getChildren()->removeElement($this);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParents()
    {
        return $this->parents;
    }

    /**
     * {@inheritdoc}
     */
    public function getParentNames()
    {
        $names = array();

        foreach ($this->getParents() as $parent) {
            $names[] = $parent->getRole();
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParent($name)
    {
        return in_array($name, $this->getParentNames());
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(RoleHierarchisableInterface $role)
    {
        $this->children->add($role);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(RoleHierarchisableInterface $child)
    {
        if ($this->getChildren()->contains($child)) {
            $this->getChildren()->removeElement($child);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrenNames()
    {
        $names = array();

        foreach ($this->getChildren() as $child) {
            $names[] = $child->getRole();
        }

        return $names;
    }

    /**
     * {@inheritdoc}
     */
    public function hasChild($name)
    {
        return in_array($name, $this->getChildrenNames());
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorization($authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthorization()
    {
        return $this->authorization;
    }

    /**
     * Serializes the content of the current Role objects.
     *
     * @return string
     */
    public function serialize()
    {
        return json_encode(array($this->name, $this->id));
    }

    /**
     * Unserializes the given string in the current Role object.
     *
     * @param serialized
     */
    public function unserialize($serialized)
    {
        list($this->name, $this->id) = json_decode($serialized);
    }

    /**
     * Convert the role to string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getRole();
    }

    /**
     * Clone the role.
     */
    public function __clone()
    {
        $this->id = null;
    }
}