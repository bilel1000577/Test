<?php

/**
 * Description of CreateMTBQCommand
 *
 * @author test
 */

namespace Quizmoo\RespondentBundle\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CreateRankingAnswerCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('answer:ranking:create')
			->setDescription('Create Ranking answer command ')
			->addArgument(
				'questionId', InputArgument::REQUIRED, 'To wich Question this answer will be linked ?'
		);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$id = $input->getArgument('questionId');
		if (is_numeric($id)) {
			$output->writeln('finding question with id ' . $id . '....');
			
			
			$entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
			$question = $entityManager->getRepository('QuizmooQuestionnaireBundle:Question')->find($id);
			if ($question) {

				// creating new answer 
				$answer = new \Quizmoo\RespondentBundle\Entity\RankingAnswer();
				$answer->setRankingQuestion($question);
				$entityManager->persist($answer);
				
				//getting the ranks of Answers
				$dialog = $this->getHelperSet()->get('dialog');
				$count = count($question->getAnswerOptions());
				
				foreach($question->getAnswerOptions() as $answerOption)
				{
				$rankEntity  = new \Quizmoo\RespondentBundle\Entity\Rank();
				$message = trim($answerOption->getAnswerText()); 
				$rank = $dialog->ask(
					$output,
					'Rank <'.$message.'> [1..'.$count.']: ',
					'1'
					);
				$rankEntity->setRank($rank);
				$rankEntity->setAnswerOption($answerOption);
				$rankEntity->setRankingAnswer($answer);
				$entityManager->persist($rankEntity);
				


				}
				
				$entityManager->flush();
				
			} else {
				$output->writeln('<error> no question with id : ' . $id . '<error>');
			}
		} else {
			$output->writeln('<error> you must give a questionnaire Id<error>');
		}

	}

}

?>
