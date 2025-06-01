<?php

namespace App\Command;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:show-article',
    description: 'Displays the details of a specific article',
)]
class ShowArticleCommand extends Command
{
    public function __construct(private EntityManagerInterface $em,)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Displays the details of a specific article')
            ->setHelp('Demonstration of custom commands created by Symfony Console component.');;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        //List of articles : title + id
        $output->writeln([
            '',
            ' --------------------- ',
            ' Articles id and title ',
            ' --------------------- ',
            ''
        ]);
        $articles = $this->em->getRepository(Article::class)->findAll();
        if (count($articles) > 0) {
            foreach ($articles as $a) {
                // $line = str_pad($a->getTitle(), 20) . '| id [' . $a->getId() . ']';
                $line = str_pad('[' . $a->getId() . ']', 6) . '| ' . $a->getTitle();
                $choices[] = $line;
                $output->writeln($line);
            }
        };

        // select article from id
        $questionArticle = new Question('<comment>Insert the number of the article of your interest: </comment>');
        //validation
        $questionArticle->setValidator(function ($answer) {
            if ('' === trim($answer)) {
                throw new \Exception('You must choose an article!');
            }
            return $answer;
        });

        $article_id = $helper->ask($input, $output, $questionArticle);
        $article = $this->em->getRepository(article::class)->find($article_id);
        if (!$article) {
            $output->writeln('<error>Article not found, try again</error>');
            return Command::FAILURE;
        }

        //get author fields
        $author = $article->getAuthor(); // object
        if ($author !== null) {
            $authorName = $author->getName();
            $authorEmail = $author->getEmail();
        };

        //print output
        $output->writeln(
            [
                '',
                '<question>' . $article->getTitle() . '</question>',
                '',
                $article->getContent(),
                '',
                '<info>published at : </info>' . $article->getPublishedAt()->format('Y-m-d H:i:s'),
                '<info>written by : </info>' . $authorName . ' - ' . $authorEmail,
                '',
            ]
        );

        return Command::SUCCESS;
    }
}
