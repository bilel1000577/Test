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

class CreateMatrixChoiceCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('answer:matrixofchoice:create')
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
				$answer = new \Quizmoo\RespondentBundle\Entity\MatrixOfChoiceAnswer();
				$answer->setMatrixOfChoiceQuestion($question);
				$entityManager->persist($answer);
				
				//getting the ranks of Answers
				$dialog = $this->getHelperSet()->get('dialog');
				//$count = count($question->getAnswerOptions());
				foreach($question->getAnswerOptions() as $answerOption)
				{
				$matrixChoiceEntity  = new \Quizmoo\RespondentBundle\Entity\MatrixChoice();
				$type = trim($answerOption->getAnswerText()); 
				if ($type == 'c'){
					$matrixChoiceEntity->setAnswerOptionCol($answerOption);

				} else {

					$matrixChoiceEntity->setAnswerOptionRow($answerOption);
				}
				$matrixChoiceEntity->setMatrixOfChoiceAnswer($answer);
				$entityManager->persist($matrixChoiceEntity);
				


				}
				
				$entityManager->flush();
				$output->writeln("Matrix Of choice persisted with success check your DB");
				
				
			} else {
				$output->writeln('<error> no question with id : ' . $id . '<error>');
			}
		} else {
			$output->writeln('<error> you must give a questionnaire Id<error>');
		}
	}

}

?>
