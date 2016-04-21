<?php

namespace Quizmoo\RespondentBundle\Entity;

use Doctrine\ORM\EntityRepository;

class MultipleChoiceAnswerRepository extends EntityRepository
{
	public function getMultipleChoiceAnswers($questionId) {
	  //Create query builder 
    $qb = $this->createQueryBuilder('mca')
               ->innerJoin('mca.multipleChoiceQuestion', 'mcq', 'WITH' , 'mcq.id = :question_id' )
               ->setParameter('question_id', $questionId);
    //Get our query
    $q = $qb->getQuery();
    //Return result
    return $q->getResult();
	}

   public function countByID($questionId)
    {
            $qb = $this->_em->createQueryBuilder();
            $qb->select('COUNT(mca.id) AS nb')
            ->from('QuizmooRespondentBundle:MultipleChoiceAnswer', 'mca')
            ->where('mca.multipleChoiceQuestion = :id')
            ->setParameter('id', $questionId);

            return $qb->getQuery()->getSingleScalarResult();
        
    }


}
