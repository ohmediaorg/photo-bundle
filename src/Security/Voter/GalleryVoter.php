<?php

namespace OHMedia\PhotoBundle\Security\Voter;

use OHMedia\PhotoBundle\Entity\Gallery;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;
use OHMedia\WysiwygBundle\Service\Wysiwyg;

class GalleryVoter extends AbstractEntityVoter
{
    public const INDEX = 'index';
    public const CREATE = 'create';
    public const VIEW = 'view';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    public function __construct(private Wysiwyg $wysiwyg)
    {
    }

    protected function getAttributes(): array
    {
        return [
            self::INDEX,
            self::CREATE,
            self::VIEW,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return Gallery::class;
    }

    protected function canIndex(Gallery $gallery, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(Gallery $gallery, User $loggedIn): bool
    {
        return true;
    }

    protected function canView(Gallery $gallery, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(Gallery $gallery, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(Gallery $gallery, User $loggedIn): bool
    {
        $shortcode = sprintf('gallery(%d)', $gallery->getId());

        return !$this->wysiwyg->shortcodesInUse($shortcode);
    }
}
