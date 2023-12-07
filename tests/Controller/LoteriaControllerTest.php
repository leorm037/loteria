<?php

namespace App\Test\Controller;

use App\Entity\Loteria;
use App\Repository\LoteriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoteriaControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private LoteriaRepository $repository;
    private string $path = '/loteria/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Loteria::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Loterium index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'loterium[api]' => 'Testing',
            'loterium[slug]' => 'Testing',
            'loterium[nome]' => 'Testing',
            'loterium[dezena]' => 'Testing',
            'loterium[premiar]' => 'Testing',
            'loterium[marcar]' => 'Testing',
            'loterium[createdAt]' => 'Testing',
            'loterium[updatedAt]' => 'Testing',
            'loterium[logo]' => 'Testing',
        ]);

        self::assertResponseRedirects('/loteria/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Loteria();
        $fixture->setApi('My Title');
        $fixture->setSlug('My Title');
        $fixture->setNome('My Title');
        $fixture->setDezena('My Title');
        $fixture->setPremiar('My Title');
        $fixture->setMarcar('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setLogo('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Loterium');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Loteria();
        $fixture->setApi('My Title');
        $fixture->setSlug('My Title');
        $fixture->setNome('My Title');
        $fixture->setDezena('My Title');
        $fixture->setPremiar('My Title');
        $fixture->setMarcar('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setLogo('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'loterium[api]' => 'Something New',
            'loterium[slug]' => 'Something New',
            'loterium[nome]' => 'Something New',
            'loterium[dezena]' => 'Something New',
            'loterium[premiar]' => 'Something New',
            'loterium[marcar]' => 'Something New',
            'loterium[createdAt]' => 'Something New',
            'loterium[updatedAt]' => 'Something New',
            'loterium[logo]' => 'Something New',
        ]);

        self::assertResponseRedirects('/loteria/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getApi());
        self::assertSame('Something New', $fixture[0]->getSlug());
        self::assertSame('Something New', $fixture[0]->getNome());
        self::assertSame('Something New', $fixture[0]->getDezena());
        self::assertSame('Something New', $fixture[0]->getPremiar());
        self::assertSame('Something New', $fixture[0]->getMarcar());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getLogo());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Loteria();
        $fixture->setApi('My Title');
        $fixture->setSlug('My Title');
        $fixture->setNome('My Title');
        $fixture->setDezena('My Title');
        $fixture->setPremiar('My Title');
        $fixture->setMarcar('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setLogo('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/loteria/');
    }
}
