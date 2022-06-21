<?php

namespace App\DataFixtures;

use App\Entity\Article;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $article = new Article();
        $article->setTitle('article de test')
                ->setSummary('résumé de test')
                ->setContent('Contenu de test')
                ->setCreatedAt(new DateTime());
        $manager->persist($article);

        $manager->flush();
    }
}
