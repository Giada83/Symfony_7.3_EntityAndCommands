<?php

namespace App\Command;

use App\Entity\Author; //importa entità
use Doctrine\ORM\EntityManagerInterface; //salva valori nel db
use Symfony\Component\Validator\Validator\ValidatorInterface; //legge validazioni dell'entità
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question; // Question Helper di Symfony

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
        // cattura input dell'utente
        $helper = $this->getHelper('question');

        //campo 'name'
        $questionName = new Question('Insert author name (required) : ');
        //validazione campo vuoto di default con QuestionHelper
        $questionName->setValidator(function ($answer) {
            if ('' === trim($answer)) {
                throw new \Exception('Name is required');
            }
            return $answer;
        });
        //numero massimo di tentativi [prova]
        $questionName->setMaxAttempts(3);
        $name = $helper->ask($input, $output, $questionName);

        //campo 'email'
        $questionEmail = new Question('Insert email (required) : ');
        $questionEmail->setValidator(function ($answer) {
            if ('' === trim($answer)) {
                throw new \Exception('Email is required');
            }
            return $answer;
        });
        $email = $helper->ask($input, $output, $questionEmail);

        //creazione entità
        $author = new Author();
        $author->setName($name);
        $author->setEmail($email);

        $errors = $this->validator->validate($author);
        if (count($errors) > 0) {
            $output->writeln((string) 'ATTENTION : ' . $errors);
            return Command::FAILURE;
        }

        //salvataggi nel db
        $this->em->persist($author);
        $this->em->flush();

        //messaggio di successo
        $output->writeln('Saved new author with id ' . $author->getId());

        return Command::SUCCESS;
    }
}
