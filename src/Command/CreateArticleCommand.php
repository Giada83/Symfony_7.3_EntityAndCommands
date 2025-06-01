<?php

namespace App\Command;

use App\Entity\Author;
use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\validator;
// use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(
    name: 'app:create-article',
    description: 'Creates a new article',
    hidden: false,
)]

class CreateArticleCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $validator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new article')
            ->setHelp('Demonstration of custom commands created by Symfony Console component.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        //anonimous reusable function
        $notEmptyValidator = function ($answer) {
            if ('' === trim($answer)) {
                throw new \Exception('This field is required');
            }
            return $answer;
        };

        $questionTitle = new Question('Insert title(required) : ');
        $questionTitle->setValidator($notEmptyValidator);
        $title = $helper->ask($input, $output, $questionTitle);

        $questionContent = new Question('Write your article : ');
        $questionContent->setValidator($notEmptyValidator);
        $content = $helper->ask($input, $output, $questionContent);

        //Author Selection
        $output->writeln([
            '',
            ' ----------- ',
            ' Author List ',
            ' ----------- ',
            ''
        ]);

        $authors = $this->em->getRepository(Author::class)->findAll();
        if (count($authors) > 0) {
            foreach ($authors as $a) {
                $line = str_pad($a->getName(), 20) . '| id: ' . $a->getId();
                $choices[] = $line;
                $output->writeln($line);
            }
        }
        //Insert value into the author_id field
        $questionAuthor = new Question('<comment>Insert the \'id\' of the author: </comment>');
        $questionAuthor->setValidator($notEmptyValidator);
        $author_id = $helper->ask($input, $output, $questionAuthor);
        $author = $this->em->getRepository(Author::class)->find($author_id);
        if (!$author) {
            $output->writeln('<error>Author not found</error>');
            return Command::FAILURE;
        }

        // New Article
        $article = new Article();
        $article->setTitle($title);
        $article->setContent($content);
        $article->setPublishedAt(new \DateTime());
        $article->setAuthor($author);

        //validations
        $errors = $this->validator->validate($article);
        if (count($errors) > 0) {
            $output->writeln('<error>ATTENTION:</error>');
            foreach ($errors as $error) {
                $output->writeln('<error> - ' . $error->getPropertyPath() . ': ' . $error->getMessage() . '</error>');
            }
            return Command::FAILURE;
        }

        // Saving data into the DB
        $this->em->persist($article);
        $this->em->flush();

        // Confirmation message
        $output->writeln('Saved new article with id: ' . $article->getId());

        return Command::SUCCESS;
    }
}
