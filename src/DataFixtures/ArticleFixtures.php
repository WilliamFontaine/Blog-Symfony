<?php

namespace App\DataFixtures;

use DateTime;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // create 3 categories
        for ($i = 0; $i < 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->word)
                ->setDescription($faker->paragraph());

            $manager->persist($category);

            // create 10 articles for each category
            for ($j = 0; $j < 10; $j++) {
                $article = new Article();

                $article->setTitle($faker->sentence)
                    ->setContent($faker->paragraph(10))
                    ->setSummary($faker->paragraph(1))
                    ->setImage($faker->imageUrl())
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);

                $manager->persist($article);
                // create a random number of comments for each article
                for ($k = 0; $k < mt_rand(0, 10); $k++) {
                    $comment = new Comment();

                    $comment->setContent($faker->paragraph)
                        ->setAuthor($faker->name)
                        ->setCreatedAt($faker->dateTimeBetween(' -' . (new DateTime())->diff($article->getCreatedAt())->days . ' days'))
                        ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
