<?php

namespace Quizmoo\RespondentBundle\Entity;

use \Doctrine\ORM\EntityRepository;

class RankingAnswerRepository extends EntityRepository
{
	public function getRankingChoices($questionId) {
    $qb = $this->_em->createQueryBuilder();
 
    $qb->select('ra')
     ->from('QuizmooRespondentBundle:RankingAnswer', 'ra')
     ->where('ra.rankingQuestion = :id')
     ->setParameter('id', $questionId);
 
    return $qb->getQuery()
              ->getResult();

	}
	 /*//Create query builder 
    $qb = $this->createQueryBuilder('ra')
               ->innerJoin('ra.rankingQuestion', 'rq', 'WITH' , 'rq.id = :question_id' )
               ->setParameter('question_id', $questionId);
    //Get our query
    $q = $qb->getQuery();
    //Return result
    return $q->getResult();*/

    public function countByID($questionId)
    {
            $qb = $this->_em->createQueryBuilder();
            $qb->select('COUNT(ra.id) AS nb')
            ->from('QuizmooRespondentBundle:RankingAnswer', 'ra')
            ->where('ra.rankingQuestion = :id')
            ->setParameter('id', $questionId);

            return $qb->getQuery()->getSingleScalarResult();
        
    }


}

