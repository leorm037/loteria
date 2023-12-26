<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\Tests;

use App\Tests\Builder\UsuarioBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConcursoControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private string $path = '/concurso/';

    protected function setUp(): void
    {
        $this->client = static::createClient([], ['HTTPS' => true]);
        $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testIndex(): void
    {
        $admin = UsuarioBuilder::getAdminMariaDb($this->entityManager);

        $this->client->loginUser($admin);

        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Loteria :: Concurso');
    }
}
