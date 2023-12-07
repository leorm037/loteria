<?php

namespace App\Controller;

use App\Entity\Bolao;
use App\Form\BolaoFormType;
use App\Repository\BolaoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/bolao', name: 'app_bolao_')]
class BolaoController extends AbstractController {

    private BolaoRepository $bolaoRepository;

    public function __construct(
            BolaoRepository $bolaoRepository
    ) {
        $this->bolaoRepository = $bolaoRepository;
    }

    #[Route('/', name: 'index')]
    public function index(): Response {
        $usuario = $this->getUser();

        $boloes = $this->bolaoRepository->list($usuario);

        return $this->render('bolao/index.html.twig', [
                    'boloes' => $boloes
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $bolao = new Bolao();

        $form = $this->createForm(BolaoFormType::class, $bolao);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $usuario = $this->getUser();

            $bolao->setUsuario($usuario);

            $this->bolaoRepository->save($bolao, true);

            $this->addFlash('success', 'O bolão foi salvo com sucesso.');

            return $this->redirectToRoute('app_bolao_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('bolao/new.html.twig', [
                    'form' => $form,
                    'bolao' => $bolao,
        ]);
    }
}
