<?php

namespace App\DataFixtures;

use DateTime;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $users = [];
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setUsername($faker->userName)
                ->setEmail($faker->email)
                ->setPassword($faker->password)
                ->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $users[] = $user;
        }

        // create 3 categories
        for ($i = 0; $i < 3; $i++) {
            $category = new Category();
            $category->setTitle($faker->word)
                ->setDescription($faker->paragraph());

            $manager->persist($category);

            // create 10 articles for each category
            for ($j = 0; $j < 4; $j++) {
                $article = new Article();

                $article->setTitle($faker->sentence)
                    ->setContent($faker->paragraph(100))
                    ->setSummary($faker->paragraph(1))
                    ->setImage($faker->imageUrl())
                    ->setAuthor($faker->randomElement($users))
                    ->setCreatedAt($faker->dateTimeBetween('-6 months'))
                    ->setCategory($category);

                $manager->persist($article);
                // create a random number of comments for each article
                for ($k = 0; $k < mt_rand(2, 20); $k++) {
                    $comment = new Comment();

                    $comment->setContent($faker->paragraph)
                        ->setAuthor($faker->randomElement($users))
                        ->setCreatedAt($faker->dateTimeBetween(' -' . (new DateTime())->diff($article->getCreatedAt())->days . ' days'))
                        ->setArticle($article);

                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}
