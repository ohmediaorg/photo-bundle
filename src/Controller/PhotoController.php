<?php

namespace OHMedia\PhotoBundle\Controller;

use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\PhotoBundle\Entity\Gallery;
use OHMedia\PhotoBundle\Entity\Photo;
use OHMedia\PhotoBundle\Form\PhotoType;
use OHMedia\PhotoBundle\Repository\PhotoRepository;
use OHMedia\PhotoBundle\Security\Voter\PhotoVoter;
use OHMedia\UtilityBundle\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class PhotoController extends AbstractController
{
    public function __construct(private PhotoRepository $photoRepository)
    {
    }

    #[Route('/gallery/{id}/photo/create', name: 'photo_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        Gallery $gallery,
    ): Response {
        $photo = new Photo();
        $photo->setGallery($gallery);

        $this->denyAccessUnlessGranted(
            PhotoVoter::CREATE,
            $photo,
            'You cannot create a new photo.'
        );

        $form = $this->createForm(PhotoType::class, $photo);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->photoRepository->save($photo, true);

                $this->addFlash('notice', 'The photo was created successfully.');

                return $this->redirectToRoute('gallery_view', [
                    'id' => $gallery->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaPhoto/photo/photo_create.html.twig', [
            'form' => $form->createView(),
            'photo' => $photo,
            'gallery' => $gallery,
        ]);
    }

    #[Route('/gallery/photo/{id}/edit', name: 'photo_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Photo $photo,
    ): Response {
        $this->denyAccessUnlessGranted(
            PhotoVoter::EDIT,
            $photo,
            'You cannot edit this photo.'
        );

        $gallery = $photo->getGallery();

        $form = $this->createForm(PhotoType::class, $photo);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->photoRepository->save($photo, true);

                $this->addFlash('notice', 'The photo was updated successfully.');

                return $this->redirectToRoute('gallery_view', [
                    'id' => $gallery->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaPhoto/photo/photo_edit.html.twig', [
            'form' => $form->createView(),
            'photo' => $photo,
            'gallery' => $gallery,
        ]);
    }

    #[Route('/gallery/photo/{id}/delete', name: 'photo_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        Photo $photo,
    ): Response {
        $this->denyAccessUnlessGranted(
            PhotoVoter::DELETE,
            $photo,
            'You cannot delete this photo.'
        );

        $gallery = $photo->getGallery();

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->photoRepository->remove($photo, true);

                $this->addFlash('notice', 'The photo was deleted successfully.');

                return $this->redirectToRoute('gallery_view', [
                    'id' => $gallery->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaPhoto/photo/photo_delete.html.twig', [
            'form' => $form->createView(),
            'photo' => $photo,
            'gallery' => $gallery,
        ]);
    }
}
