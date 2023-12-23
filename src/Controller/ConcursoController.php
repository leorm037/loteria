<?php

/*
 *     This file is part of Loteria.
 *
 *     (c) Leonardo Rodrigues Marques <leonardo@rodriguesmarques.com.br>
 *
 *     This source file is subject to the MIT license that is bundled
 *     with this source code in the file LICENSE.
 */

namespace App\Controller;

use App\DTO\PaginatorDTO;
use App\Repository\ConcursoRepository;
use App\Repository\LoteriaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/concurso', name: 'app_concurso_')]
class ConcursoController extends AbstractController
{
    private ConcursoRepository $concursoRepository;
    private LoteriaRepository $loteriaRepository;

    public function __construct(
        ConcursoRepository $concursoRepository,
        LoteriaRepository $loteriaRepository
    ) {
        $this->concursoRepository = $concursoRepository;
        $this->loteriaRepository = $loteriaRepository;
    }

    #[Route('/', name: 'index')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $loterias = $this->loteriaRepository->list();

        $paginator = new PaginatorDTO();
        $result = null;
        $loteria = null;

        $page = $request->get('page', 1);
        $loteriaId = $request->get('loteria');
        $maxResult = $request->get('maxResult', 10);

        $paginator
                ->setPage($page)
                ->setMaxResult($maxResult)
        ;

        if ($loteriaId) {
            $uuid = Uuid::fromString($loteriaId);

            $loteria = $this->loteriaRepository->findById($uuid);

            $result = $this->concursoRepository->listPesquisar(
                $loteria,
                $paginator->getFirstResult(),
                $paginator->getMaxResult()
            );
        }

        $paginator
                ->setResult($result)
        ;

        return $this->render('concurso/index.html.twig', [
                    'paginator' => $paginator,
                    'loterias' => $loterias,
        ]);
    }
}
