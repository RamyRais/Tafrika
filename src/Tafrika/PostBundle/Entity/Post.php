<?php

namespace Tafrika\PostBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tafrika\PostBundle\Entity\PostRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *                  "image" = "Image",
 *                  "video" = "Video",
 *                  "status" = "Status"})
 * @ORM\MappedSuperclass
 */
class Post
{
    const TYPE = 'POST';
    const MAX_SIGNAL_NUMBER_NSFW = 500;
    const MAX_SIGNAL_NUMBER_PORN = 500;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\Length(min=5, max=255,
     *              minMessage="يا معلم تيتر لازمو فوق 5 حروف",
     *              maxMessage="يا معلم تيتر لازمو أقل من 255 حرف")
     *
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\DateTime()
     */
    private $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="likes", type="integer")
     * @Assert\EqualTo( value = 0)
     */
    private $likes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="nsfw", type="boolean")
     * @Assert\Type(type="bool")
     *
     */
    private $NSFW;

    /**
     * @@var integer
     *
     * @ORM\Column(name="signal_nsfw", type="integer")
     * @Assert\EqualTo( value = 0)
     */
    private $signal_nsfw;

    /**
     * @@var integer
     *
     * @ORM\Column(name="signal_porn", type="integer")
     * @Assert\EqualTo( value = 0)
     */
    private $signal_porn;

    /**
     * @Gedmo\Slug(fields={"title"})
     * @ORM\Column(length=255, unique=true)
     */
    private $slug;

    /**
     *
     * @var type User
     * @ORM\ManyToOne(targetEntity="Tafrika\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id",referencedColumnName="id")
     * @Assert\Valid()
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Tafrika\PostBundle\Entity\Comment", mappedBy="post",
     *                cascade={"remove"})
     */
    private $comments;

    public function __construct() {
        $this->likes = 0;
        $this->createdAt = new \DateTime('now',new \DateTimeZone("UTC"));
        $this->signal_nsfw = 0;
        $this->signal_porn = 0;
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
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Post
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set likes
     *
     * @param integer $likes
     * @return Post
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * Get likes
     *
     * @return integer 
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * Set user
     *
     * @param \Tafrika\UserBundle\Entity\User $user
     * @return Post
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Tafrika\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    public static function get_type()
    {
        $c = get_called_class();
        return $c::TYPE;
    }

    /**
     * Add comments
     *
     * @param \Tafrika\PostBundle\Entity\Comment $comments
     * @return Post
     */
    public function addComment($comments)
    {
        $this->comments->add($comments);

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \Tafrika\PostBundle\Entity\Comment $comments
     */
    public function removeComment($comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set NSFW
     *
     * @param boolean $nSFW
     * @return Post
     */
    public function setNSFW($nSFW)
    {
        $this->NSFW = $nSFW;

        return $this;
    }

    /**
     * Get NSFW
     *
     * @return boolean 
     */
    public function getNSFW()
    {
        return $this->NSFW;
    }

    /**
     * Set signal_nsfw
     *
     * @param integer $signalNsfw
     * @return Post
     */
    public function setSignalNsfw($signalNsfw)
    {
        $this->signal_nsfw = $signalNsfw;

        return $this;
    }

    /**
     * Get signal_nsfw
     *
     * @return integer 
     */
    public function getSignalNsfw()
    {
        return $this->signal_nsfw;
    }

    /**
     * Set signal_porn
     *
     * @param integer $signalPorn
     * @return Post
     */
    public function setSignalPorn($signalPorn)
    {
        $this->signal_porn = $signalPorn;

        return $this;
    }

    /**
     * Get signal_porn
     *
     * @return integer 
     */
    public function getSignalPorn()
    {
        return $this->signal_porn;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
