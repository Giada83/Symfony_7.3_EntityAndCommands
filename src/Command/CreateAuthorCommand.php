<?php

namespace App\Command;

use App\Entity\Author; //import entity
use Doctrine\ORM\EntityManagerInterface; //save values on db
use Symfony\Component\Validator\Validator\ValidatorInterface; //validation
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question; // Symfony Question Helper 

// register the command
#[AsCommand(
    name: 'app:create-author', //
    description: 'Creates a new author',
    hidden: false,
)]

class CreateAuthorCommand extends Command
{
    protected function configure(): void
    {
        $this
            // the command description shown when running "php bin/console list"
            ->setDescription('Creates a new author')
            // the command help shown when running the command with the "--help" option
            ->setHelp('Demonstration of custom commands created by Symfony Console component.');
    }

    //Dependency Injection
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // receive user input
        $helper = $this->getHelper('question');

        //anonimous reusable function
        $notEmptyValidator = function ($answer) {
            if ('' === trim($answer)) {
                throw new \Exception('This field is required');
            }
            return $answer;
        };

        //field 'name'
        $questionName = new Question('Insert author name (required) : ');
        //default empty field validation with QuestionHelper
        $questionName->setValidator($notEmptyValidator);
        // $questionName->setValidator(function ($answer) {
        //     if ('' === trim($answer)) {
        //         throw new \Exception('Name is required');
        //     }
        //     return $answer;
        // });
        $questionName->setMaxAttempts(3); //maximum number of attempts [test]
        $name = $helper->ask($input, $output, $questionName);

        //field 'email'
        $questionEmail = new Question('Insert email (required) : ');
        $questionEmail->setValidator($notEmptyValidator);
        $email = $helper->ask($input, $output, $questionEmail);

        //new author entity
        $author = new Author();
        $author->setName($name);
        $author->setEmail($email);

        //validations
        // $errors = $this->validator->validate($author);
        // if (count($errors) > 0) {
        //     $output->writeln((string) '<error>ATTENTION : ' . $errors . '</error>');
        //     return Command::FAILURE;
        // }
        $errors = $this->validator->validate($article);
        if (count($errors) > 0) {
            $output->writeln('<error>ATTENTION:</error>');
            foreach ($errors as $error) {
                $output->writeln('<error> - ' . $error->getPropertyPath() . ': ' . $error->getMessage() . '</error>');
            }
            return Command::FAILURE;
        }

        //save on db
        $this->em->persist($author);
        $this->em->flush();

        //success message
        $output->writeln('Saved new author with id ' . $author->getId());

        return Command::SUCCESS;
    }
}
