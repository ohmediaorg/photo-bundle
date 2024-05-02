<?php

namespace OHMedia\PhotoBundle\Security\Voter;

use OHMedia\PhotoBundle\Entity\Photo;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class PhotoVoter extends AbstractEntityVoter
{
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function getAttributes(): array
    {
        return [
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return Photo::class;
    }

    protected function canCreate(Photo $galleryPhoto, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(Photo $galleryPhoto, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(Photo $galleryPhoto, User $loggedIn): bool
    {
        return true;
    }
}
