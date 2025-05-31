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
// use Symfony\Component\Console\Question\ChoiceQuestion; \\non usato

#[AsCommand(
    name: 'app:create-article',
    description: 'Creates a new article',
    hidden: false,
)]

class CreateArticleCommand extends Command
{
    public function __construct(private EntityManagerInterface $em,)
    {
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
        $questionTitle = new Question('Insert title(required) : ');
        $title = $helper->ask($input, $output, $questionTitle);

        $questionContent = new Question('Write your article : ');
        $content = $helper->ask($input, $output, $questionContent);

        //selection autori
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
        //inserimento valore campo autore_id
        $questionAuthor = new Question('<comment>Insert the \'id\' of the author: </comment>');
        $author_id = $helper->ask($input, $output, $questionAuthor);
        $author = $this->em->getRepository(Author::class)->find($author_id);
        if (!$author) {
            $output->writeln('<error>Author not found</error>');
            return Command::FAILURE;
        }

        // nuovo ogetto Article
        $article = new Article();
        $article->setTitle($title);
        $article->setContent($content);
        $article->setPublishedAt(new \DateTime());
        $article->setAuthor($author);

        // salvataggio nel db
        $this->em->persist($article);
        $this->em->flush();

        // messaggio di successo
        $output->writeln('Saved new article with id: ' . $article->getId());

        return Command::SUCCESS;
    }
}
