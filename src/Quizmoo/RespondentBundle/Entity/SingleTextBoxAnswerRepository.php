<?php

namespace Quizmoo\RespondentBundle\Entity;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\EntityRepository;

class SingleTextBoxAnswerRepository extends EntityRepository
{
	public function getAnswers($questionId) {
    $qb = $this->_em->createQueryBuilder();
 
    $qb->select('stba')
     ->from('QuizmooRespondentBundle:SingleTextBoxAnswer', 'stba')
     ->where('stba.singleTexBoxQuestion = :id')
     ->setParameter('id', $questionId);
 	return $qb->getQuery()
              ->getResult();
 	}


    public function countByID($questionId)
    {
            $qb = $this->_em->createQueryBuilder();
            $qb->select('COUNT(stba.id) AS nb')
            ->from('QuizmooRespondentBundle:SingleTextBoxAnswer', 'stba')
            ->where('stba.singleTexBoxQuestion = :id')
            ->setParameter('id', $questionId);

            return $qb->getQuery()->getSingleScalarResult();
        
    }


    public function getJsonAnswers($questionId) {
    $qb = $this->_em->createQueryBuilder();
    $qb->select('stba')
     ->from('QuizmooRespondentBundle:SingleTextBoxAnswer', 'stba')
     ->where('stba.singleTexBoxQuestion = :id')
     ->setParameter('id', $questionId);
     return $qb->getQuery()
              ->getResult();

    }

}

