<?php

/**
 * Description of CreateMTBQCommand
 *
 * @author test
 */

namespace Quizmoo\QuestionnaireBundle\Command;


use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class CreateMTBQCommand extends ContainerAwareCommand {

	protected function configure() {
		$this
			->setName('question:mtb:create')
			->setDescription('Create Multiple TextBox Questionnaire ')
			->addArgument(
				'questionnaireId', InputArgument::REQUIRED, ' '
		);
	}

	protected function execute(InputInterface $input, OutputInterface $output) {

		$id = $input->getArgument('questionnaireId');
		if (is_numeric($id)) {
			$entityManager = $this->getContainer()->get('doctrine')->getEntityManager();
			$questionnaire = $entityManager->getRepository('QuizmooQuestionnaireBundle:Questionnaire')->find($id);
			if ($questionnaire) {
				$stbQuestion = new \Quizmoo\QuestionnaireBundle\Entity\SingleTextBoxQuestion();

				$dialog = $this->getHelperSet()->get('dialog');
				$questionText = $dialog->ask(
					$output,
					'Please enter the text of the question (a default value will be given) :',
					'Single text Box question autogenerated'
					);
				$stbQuestion->setQuestionText($questionText);

				
				//$type = $entityManager->getRepository('QuizmooQuestionnaireBundle:QuestionType')->getTypeByName('Single TextBox Question');
				$type = "SingleTextBoxQuestion";
				$stbQuestion->setQuestionType($type);
				$stbQuestion->setQuestionnaire($questionnaire);
				$entityManager->persist($stbQuestion);
				$output->writeln($stbQuestion->getId());
				$entityManager->flush();
				
			} else {
				$output->writeln('<error> no questionnaire with id : ' . $id . '<error>');
			}
		} else {
			$output->writeln('<error> you must give a questionnaire Id<error>');
		}
	}

}

?>
