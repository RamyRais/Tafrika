<?php

namespace Tafrika\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tafrika\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) */
    protected $facebook_id;

    /** @ORM\Column(name="google_id", type="string", length=255, nullable=true) */
    protected $google_id;

    /**
     *
     * @ORM\ManyToMany(targetEntity="User", mappedBy="followed")
     */
    private $followers;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="followers")
     * @ORM\JoinTable(name="subscribed",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="followed_id", referencedColumnName="id")}
     *      )
     */
    private $followed;

    public function __construct()
    {
        parent::__construct();
        $this->addRole("ROLE_USER");
        $this->followed = new \Doctrine\Common\Collections\ArrayCollection();
        $this->followers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set facebook_id
     *
     * @param string $facebookId
     * @return User
     */
    public function setFacebookId($facebookId)
    {
        $this->facebook_id = $facebookId;

        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }

    /**
     * Set google_id
     *
     * @param string $googleId
     * @return User
     */
    public function setGoogleId($googleId)
    {
        $this->google_id = $googleId;

        return $this;
    }

    /**
     * Get google_id
     *
     * @return string
     */
    public function getGoogleId()
    {
        return $this->google_id;
    }

    /**
     * Add followers
     *
     * @param \Tafrika\UserBundle\Entity\User $followers
     * @return User
     */
    public function addfollowers($followers)
    {
        $this->followers->add($followers);

        return $this;
    }

    /**
     * Remove followers
     *
     * @param \Tafrika\UserBundle\Entity\User $followers
     */
    public function removefollowers($followers)
    {
        $this->followers->removeElement($followers);
    }

    /**
     * Get followers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getfollowers()
    {
        return $this->followers;
    }

    /**
     * Add followed
     *
     * @param \Tafrika\UserBundle\Entity\User $followed
     * @return User
     */
    public function addfollowed($followed)
    {
        if( !$this->isFollowed($followed) && $followed instanceof User) {
            $this->followed->add($followed);
            $followed->addfollowers($this);
            return $this;
        }

    }

    /**
     * Remove followed
     *
     * @param \Tafrika\UserBundle\Entity\User $followed
     */
    public function removefollowed($followed)
    {
        if( $this->isFollowed($followed) && $followed instanceof User) {
            $this->followed->removeElement($followed);
            $followed->removefollowers($this);
        }
    }

    /**
     * Get followed
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getfollowed()
    {
        return $this->followed;
    }

    public function isFollowed($user){
        return $this->getfollowed()->contains($user);
    }
}
