<?php


declare (strict_types=1);

namespace App\Unit;

use App\DataFixtures\UtilisateurFixtures;
use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
 
class UtlisateurRepositoryTest extends KernelTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    public function setUp() : void
    {
        parent::setUp();

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        // or with Symfony < 5.3
        // static::bootKernel();
        // $this->databaseTool = self::$container->get(DatabaseToolCollection::class)->get();
    }
    
    public function testCount()
    {
        //On charge les fixtures
        // add all your fixtures classes that implement
        // Doctrine\Common\DataFixtures\FixtureInterface
        $this->databaseTool->loadFixtures([UtilisateurFixtures::class]);

        // you can now run your functional tests with a populated database
        // ...

        //On accede au Container
        $users = self::getContainer()->get(UtilisateurRepository::class)->count([]);
        $this->assertEquals(10,$users);
        
    }
}
