<?php

namespace App\Controller;

use App\Entity\Loteria;
use App\Form\LoteriaType;
use App\Repository\LoteriaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/loteria')]
class LoteriaController extends AbstractController
{
    #[Route('/', name: 'app_loteria_index', methods: ['GET'])]
    public function index(LoteriaRepository $loteriaRepository): Response
    {
        return $this->render('loteria/index.html.twig', [
            'loterias' => $loteriaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_loteria_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $loterium = new Loteria();
        $form = $this->createForm(LoteriaType::class, $loterium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($loterium);
            $entityManager->flush();

            return $this->redirectToRoute('app_loteria_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('loteria/new.html.twig', [
            'loterium' => $loterium,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_loteria_show', methods: ['GET'])]
    public function show(Loteria $loterium): Response
    {
        return $this->render('loteria/show.html.twig', [
            'loterium' => $loterium,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_loteria_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Loteria $loterium, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LoteriaType::class, $loterium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_loteria_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('loteria/edit.html.twig', [
            'loterium' => $loterium,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_loteria_delete', methods: ['POST'])]
    public function delete(Request $request, Loteria $loterium, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$loterium->getId(), $request->request->get('_token'))) {
            $entityManager->remove($loterium);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_loteria_index', [], Response::HTTP_SEE_OTHER);
    }
}
