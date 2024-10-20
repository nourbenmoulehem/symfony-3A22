<?php

namespace App\Tests\Controller;

use App\Entity\Livre;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LivreControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/livre/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Livre::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Livre index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'livre[ref]' => 'Testing',
            'livre[title]' => 'Testing',
            'livre[nbrPages]' => 'Testing',
            'livre[picture]' => 'Testing',
            'livre[author]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Livre();
        $fixture->setRef('My Title');
        $fixture->setTitle('My Title');
        $fixture->setNbrPages('My Title');
        $fixture->setPicture('My Title');
        $fixture->setAuthor('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Livre');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Livre();
        $fixture->setRef('Value');
        $fixture->setTitle('Value');
        $fixture->setNbrPages('Value');
        $fixture->setPicture('Value');
        $fixture->setAuthor('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'livre[ref]' => 'Something New',
            'livre[title]' => 'Something New',
            'livre[nbrPages]' => 'Something New',
            'livre[picture]' => 'Something New',
            'livre[author]' => 'Something New',
        ]);

        self::assertResponseRedirects('/livre/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getRef());
        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getNbrPages());
        self::assertSame('Something New', $fixture[0]->getPicture());
        self::assertSame('Something New', $fixture[0]->getAuthor());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Livre();
        $fixture->setRef('Value');
        $fixture->setTitle('Value');
        $fixture->setNbrPages('Value');
        $fixture->setPicture('Value');
        $fixture->setAuthor('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/livre/');
        self::assertSame(0, $this->repository->count([]));
    }
}
