<?php

namespace OHMedia\PhotoBundle\Controller;

use Doctrine\DBAL\Connection;
use OHMedia\BackendBundle\Routing\Attribute\Admin;
use OHMedia\BootstrapBundle\Service\Paginator;
use OHMedia\PhotoBundle\Entity\Gallery;
use OHMedia\PhotoBundle\Entity\Photo;
use OHMedia\PhotoBundle\Form\GalleryType;
use OHMedia\PhotoBundle\Repository\GalleryRepository;
use OHMedia\PhotoBundle\Repository\PhotoRepository;
use OHMedia\PhotoBundle\Security\Voter\GalleryVoter;
use OHMedia\PhotoBundle\Security\Voter\PhotoVoter;
use OHMedia\UtilityBundle\Form\DeleteType;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Admin]
class GalleryController extends AbstractController
{
    private const CSRF_TOKEN_REORDER = 'photo_reorder';

    public function __construct(
        private GalleryRepository $galleryRepository,
        private PhotoRepository $photoRepository,
        private Connection $connection,
        private Paginator $paginator,
        private RequestStack $requestStack
    ) {
    }

    #[Route('/galleries', name: 'gallery_index', methods: ['GET'])]
    public function index(): Response
    {
        $newGallery = new Gallery();

        $this->denyAccessUnlessGranted(
            GalleryVoter::INDEX,
            $newGallery,
            'You cannot access the list of galleries.'
        );

        $qb = $this->galleryRepository->createQueryBuilder('g');
        $qb->orderBy('g.name', 'asc');

        return $this->render('@OHMediaPhoto/gallery/gallery_index.html.twig', [
            'pagination' => $this->paginator->paginate($qb, 20),
            'new_gallery' => $newGallery,
            'attributes' => $this->getAttributes(),
        ]);
    }

    #[Route('/gallery/create', name: 'gallery_create', methods: ['GET', 'POST'])]
    public function create(): Response
    {
        $gallery = new Gallery();

        $this->denyAccessUnlessGranted(
            GalleryVoter::CREATE,
            $gallery,
            'You cannot create a new gallery.'
        );

        $form = $this->createForm(GalleryType::class, $gallery);

        $form->add('save', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->galleryRepository->save($gallery, true);

                $this->addFlash('notice', 'The gallery was created successfully.');

                return $this->redirectToRoute('gallery_view', [
                    'id' => $gallery->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaPhoto/gallery/gallery_create.html.twig', [
            'form' => $form->createView(),
            'gallery' => $gallery,
        ]);
    }

    #[Route('/gallery/{id}', name: 'gallery_view', methods: ['GET'])]
    public function view(
        #[MapEntity(id: 'id')] Gallery $gallery,
    ): Response {
        $this->denyAccessUnlessGranted(
            GalleryVoter::VIEW,
            $gallery,
            'You cannot view this gallery.'
        );

        $newPhoto = new Photo();
        $newPhoto->setGallery($gallery);

        return $this->render('@OHMediaPhoto/gallery/gallery_view.html.twig', [
            'gallery' => $gallery,
            'attributes' => $this->getAttributes(),
            'new_photo' => $newPhoto,
            'csrf_token_name' => self::CSRF_TOKEN_REORDER,
        ]);
    }

    #[Route('/gallery/{id}/photos/reorder', name: 'photo_reorder_post', methods: ['POST'])]
    public function reorderPost(
        #[MapEntity(id: 'id')] Gallery $gallery,
    ): Response {
        $this->denyAccessUnlessGranted(
            GalleryVoter::INDEX,
            $gallery,
            'You cannot reorder the photos.'
        );

        $request = $this->requestStack->getCurrentRequest();

        $csrfToken = $request->request->get(self::CSRF_TOKEN_REORDER);

        if (!$this->isCsrfTokenValid(self::CSRF_TOKEN_REORDER, $csrfToken)) {
            return new JsonResponse('Invalid CSRF token.', 400);
        }

        $photos = $request->request->all('order');

        $this->connection->beginTransaction();

        try {
            foreach ($photos as $ordinal => $id) {
                $photo = $this->photoRepository->find($id);

                if ($photo) {
                    $photo->setOrdinal($ordinal);

                    $this->photoRepository->save($photo, true);
                }
            }

            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();

            return new JsonResponse('Data unable to be saved.', 400);
        }

        return new JsonResponse();
    }

    #[Route('/gallery/{id}/edit', name: 'gallery_edit', methods: ['GET', 'POST'])]
    public function edit(
        #[MapEntity(id: 'id')] Gallery $gallery,
    ): Response {
        $this->denyAccessUnlessGranted(
            GalleryVoter::EDIT,
            $gallery,
            'You cannot edit this gallery.'
        );

        $form = $this->createForm(GalleryType::class, $gallery);

        $form->add('save', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->galleryRepository->save($gallery, true);

                $this->addFlash('notice', 'The gallery was updated successfully.');

                return $this->redirectToRoute('gallery_view', [
                    'id' => $gallery->getId(),
                ]);
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaPhoto/gallery/gallery_edit.html.twig', [
            'form' => $form->createView(),
            'gallery' => $gallery,
        ]);
    }

    #[Route('/gallery/{id}/delete', name: 'gallery_delete', methods: ['GET', 'POST'])]
    public function delete(
        #[MapEntity(id: 'id')] Gallery $gallery,
    ): Response {
        $this->denyAccessUnlessGranted(
            GalleryVoter::DELETE,
            $gallery,
            'You cannot delete this gallery.'
        );

        $form = $this->createForm(DeleteType::class, null);

        $form->add('delete', SubmitType::class);

        $form->handleRequest($this->requestStack->getCurrentRequest());

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $this->galleryRepository->remove($gallery, true);

                $this->addFlash('notice', 'The gallery was deleted successfully.');

                return $this->redirectToRoute('gallery_index');
            }

            $this->addFlash('error', 'There are some errors in the form below.');
        }

        return $this->render('@OHMediaPhoto/gallery/gallery_delete.html.twig', [
            'form' => $form->createView(),
            'gallery' => $gallery,
        ]);
    }

    public static function getAttributes(): array
    {
        return [
            'gallery' => [
                'view' => GalleryVoter::VIEW,
                'create' => GalleryVoter::CREATE,
                'delete' => GalleryVoter::DELETE,
                'edit' => GalleryVoter::EDIT,
            ],
            'photo' => [
                'create' => PhotoVoter::CREATE,
                'delete' => PhotoVoter::DELETE,
                'edit' => PhotoVoter::EDIT,
            ],
        ];
    }
}
