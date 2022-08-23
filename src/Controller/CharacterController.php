<?php

namespace App\Controller;

use App\Entity\Character;
use App\Form\CharacterType;
use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CharacterController extends AbstractController
{
    #[Route('/', name: 'app_character_index', methods: ['GET'])]
    public function index(CharacterRepository $characterRepository): Response
    {
        return $this->render('character/index.html.twig', [
            'characters' => $characterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_character_new', methods: ['GET', 'POST'])]
    public function new(
        Request             $request,
        CharacterRepository $characterRepository,
        SluggerInterface    $slugger
    ): Response
    {
        $character = new Character();
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('pictureFile')->getData();
            if ($pictureFile) {
                $newFilename = $this->saveFile($pictureFile, $slugger);
                $character->setPicture($newFilename);
            }

            $characterRepository->add($character, true);

            return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('character/new.html.twig', compact('character', 'form'));
    }

    #[Route('/{id}', name: 'app_character_show', methods: ['GET'])]
    public function show(Character $character): Response
    {
        return $this->render('character/show.html.twig', compact('character'));
    }

    #[Route('/{id}/edit', name: 'app_character_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request             $request,
        Character           $character,
        CharacterRepository $characterRepository,
        SluggerInterface    $slugger
    ): Response
    {
        $form = $this->createForm(CharacterType::class, $character);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pictureFile = $form->get('pictureFile')->getData();

            if ($pictureFile) {
                $newFilename = $this->saveFile($pictureFile, $slugger);
                $character->setPicture($newFilename);
            }

            $characterRepository->add($character, true);

            return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('character/edit.html.twig', compact('character', 'form'));
    }

    #[Route('/{id}', name: 'app_character_delete', methods: ['POST'])]
    public function delete(Request $request, Character $character, CharacterRepository $characterRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $character->getId(), $request->request->get('_token'))) {
            $characterRepository->remove($character, true);
        }

        return $this->redirectToRoute('app_character_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/search', name: 'ajax_search', methods: ['GET'])]
    public function search(Request $request, CharacterRepository $characterRepository)
    {
        $requestString = $request->get('q');
        $characterRepository->findByName($requestString);
    }

    private function saveFile($pictureFile, SluggerInterface $slugger): string
    {
        $originalFilename = pathinfo($pictureFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureFile->guessExtension();

        try {
            $pictureFile->move(
                $this->getParameter('pictures_directory'),
                $newFilename
            );
        } catch (FileException $e) {
            echo $e->getMessage();
        }

        return $newFilename;
    }
}
