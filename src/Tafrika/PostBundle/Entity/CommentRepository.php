<?php

namespace Tafrika\PostBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * CommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentRepository extends EntityRepository
{
    public function findCommentByPost($page, $commentPerPage, $post){
        if($page<1){
            throw new \InvalidArgumentException("L'argument page ne peut pas être inférieur à 1");
        }

        $query = $this->createQueryBuilder('c');
        $query->leftJoin('c.user','user')
            ->addSelect('user')
            ->where('c.post = :post')
              ->setParameter('post', $post)
              ->orderBy('c.createdAt','DESC');
        $query->setFirstResult( ($page - 1)* $commentPerPage);
        $query->setMaxResults($commentPerPage);
        return $query->getQuery()->getArrayResult();
    }
}
