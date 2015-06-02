<?php

namespace Tafrika\PostBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile as UploadedFile;
use Imagine\Gd\Imagine as Imagine;
use Imagine\Image\Box as Box;

/**
 * Image
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Image extends Post
{
    const TYPE = 'IMAGE';

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
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @var UploadedFile
     * @Assert\Image(
     *          maxSize = "2M",
     *          maxSizeMessage="تصوير كبيرة برشا لحد لأقصى 2 ميجا",
     *          mimeTypes = {"image/jpeg", "image/gif", "image/png"},
     *          mimeTypesMessage = "الفرما المقبولة JPEG، PNG، GIF")
     */
    private $file;

    private $filenameForRemove;

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->file) {
            $title = $this->getTitle();
            $title = preg_replace('/\s+/','_',$title);
            $this->path = $title.".".$this->file->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->file) {
            return;
        }

        // vous devez lancer une exception ici si le fichier ne peut pas
        // être déplacé afin que l'entité ne soit pas persistée dans la
        // base de données comme le fait la méthode move() de UploadedFile
        $file_path = $this->getUploadRootDir().'/'.$this->getId()."_".$this->getPath();
        if($this->file->guessExtension() != 'gif') {
            $this->file->move($this->getUploadRootDir().'/', $this->getId()."_".$this->getPath());
            $imagine = new Imagine();
            $image = $imagine->open($file_path);
            $imageWidth = $image->getSize()->getWidth();
            if ($imageWidth <= 580) {
                $imageHeight = $image->getSize()->getHeight();
                $ratio = $imageHeight / $imageWidth;
                $imageWidth = 580;
                $imageHeight = $imageWidth * $ratio;
                $image->resize(new Box($imageWidth, $imageHeight));
                unlink($file_path);
                $image->save($file_path);
            }
            unset($this->file);
        }else{
            $this->file->move($this->getUploadRootDir().'/', $this->getId()."_".$this->getPath());
//            $api = new Api("A7GkUuBl1fTdUXfVafMSLnGH7jDMomN4YpCcQJ_KmFCBIkdhClweHp_1QdrQE2V87SyzciEpCc5j0XhJG4Y4qA");
//            $api->convert([
//                "input" => "upload",
//                "output" => [
//                    "ftp" => [
//                        "host" => "ftp.cluster011.ovh.net",
//                        "user" => "tafrikacna",
//                        "password" => "JvPqyujUaRxA",
//                        "path" => $file_path,
//                    ],
//                ],
//                "inputformat" => "gif",
//                "outputformat" => "mp4",
//                "file" => fopen($file_path, 'r'),
//            ])
//                ->wait()
//                ->download();
        }

    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove()
    {
        $this->filenameForRemove = $this->getAbsolutePath();
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($this->filenameForRemove) {
            unlink($this->filenameForRemove);
        }
    }

    public function getAbsolutePath()
    {
        return null === $this->path ? null : $this->getUploadRootDir().'/'.$this->getId().'_'.$this->getPath();
    }

    public function getWebPath()
    {
        return null === $this->path ? null : $this->getUploadDir().'/'.$this->getId().'_'.$this->getPath();
    }

    protected function getUploadRootDir()
    {
        // le chemin absolu du répertoire où les documents uploadés doivent être sauvegardés
        return __DIR__.'/../../../../web/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'uploads/user/'.$this->getUser()->getId().'/images';
    }



    /**
     * Set path
     *
     * @param string $path
     * @return Image
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set file
     *
     * @param string $file
     * @return Content
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
}
