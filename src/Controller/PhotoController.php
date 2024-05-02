<?php

namespace OHMedia\PhotoBundle\Controller;

use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\PhotoBundle\Entity\Gallery;
use OHMedia\PhotoBundle\Entity\Photo;
use OHMedia\PhotoBundle\Form\PhotoType;
use OHMedia\PhotoBundle\Repository\PhotoRepository;
use OHMedia\PhotoBundle\Security\Voter\PhotoVoter;
use OHMedia\SecurityBundle\Form\DeleteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class PhotoController extends AbstractController
{
    #[Route('/gallery/{id}/photo/create', name: 'gallery_photo_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        Gallery $gallery,
        PhotoRepository $galleryPhotoRepository
    ): Response {
        $galleryPhoto = new Photo();
        $galleryPhoto->setGallery($gallery);

        $this->denyAccessUnlessGranted(
            PhotoVoter::CREATE,
            $galleryPhoto,
            'You cannot create a new photo.'
        );

        $form = $this->createForm(PhotoType::class, $galleryPhoto);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $galleryPhotoRepository->save($galleryPhoto, true);

            $this->addFlash('notice', 'The photo was created successfully.');

            return $this->redirectToRoute('gallery_view', [
                'id' => $gallery->getId(),
            ]);
        }

        return $this->render('@OHMediaPhoto/gallery/photo/gallery_photo_create.html.twig', [
            'form' => $form->createView(),
            'gallery_photo' => $galleryPhoto,
            'gallery' => $gallery,
        ]);
    }

    #[Route('/gallery/photo/{id}/edit', name: 'gallery_photo_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Photo $galleryPhoto,
        PhotoRepository $galleryPhotoRepository
    ): Response {
        $this->denyAccessUnlessGranted(
            PhotoVoter::EDIT,
            $galleryPhoto,
            'You cannot edit this gallery photo.'
        );

        $gallery = $galleryPhoto->getGallery();

        $form = $this->createForm(PhotoType::class, $galleryPhoto);

        $form->add('submit', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $galleryPhotoRepository->save($galleryPhoto, true);

            $this->addFlash('notice', 'The gallery photo was updated successfully.');

            return $this->redirectToRoute('gallery_view', [
                'id' => $gallery->getId(),
            ]);
        }

        return $this->render('@OHMediaPhoto/gallery/photo/gallery_photo_edit.html.twig', [
            'form' => $form->createView(),
            'gallery_photo' => $galleryPhoto,
            'gallery' => $gallery,
        ]);
    }

    #[Route('/gallery/photo/{id}/delete', name: 'gallery_photo_delete', methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        Photo $galleryPhoto,
        PhotoRepository $galleryPhotoRepository
    ): Response {
        $this->denyAccessUnlessGranted(
            PhotoVoter::DELETE,
            $galleryPhoto,
            'You cannot delete this gallery photo.'
        );

        $gallery = $galleryPhoto->getGallery();

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $galleryPhotoRepository->remove($galleryPhoto, true);

            $this->addFlash('notice', 'The gallery photo was deleted successfully.');

            return $this->redirectToRoute('gallery_view', [
                'id' => $gallery->getId(),
            ]);
        }

        return $this->render('@OHMediaPhoto/gallery/photo/gallery_photo_delete.html.twig', [
            'form' => $form->createView(),
            'gallery_photo' => $galleryPhoto,
            'gallery' => $gallery,
        ]);
    }
}
