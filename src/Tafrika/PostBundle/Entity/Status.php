<?php

namespace Tafrika\PostBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Status
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Tafrika\PostBundle\Entity\StatusRepository")
 */
class Status extends Post
{
    const TYPE = 'STATUS';

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
     * @ORM\Column(name="content", type="text")
     * @Assert\Length(min=10,
     *              minMessage="يا معلم تيتر لازمو فوق 10 حروف")
     */
    private $content;




    /**
     * Set content
     *
     * @param string $content
     * @return Status
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }
}
