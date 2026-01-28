<?php

declare(strict_types=1);

namespace App\Modules\Users\Factory;

use App\Auth\DBAL\Entity\User;
use App\Domain\DTOValueResolver;
use App\Domain\Exception\InvalidArgumentException;
use App\Modules\Users\DTO\UserDTO;
use App\Modules\Users\DTO\UserDTOCollection;
use App\Modules\Users\DTO\UsersCreateResponseDTO;
use App\Modules\Users\DTO\UsersDeleteResponseDTO;
use App\Modules\Users\DTO\UsersInfoResponseDTO;
use App\Modules\Users\DTO\UsersListResponseDTO;
use App\Modules\Users\DTO\UsersUpdateResponseDTO;

class DTOFactory
{
    public function createUserDTO(User $user): UserDTO
    {
        return new UserDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getFullName(),
            $user->getIsActive(),
            $user->getIsAdmin(),
            $user->getIsOffice(),
        );
    }

    /**
     * @param User[] $users
     *
     * @throws InvalidArgumentException
     */
    public function createUserDTOCollection(array $users): UserDTOCollection
    {
        $collection = new UserDTOCollection();

        foreach ($users as $user) {
            $collection->add($this->createUserDTO($user));
        }

        return $collection;
    }

    /**
     * @param User[] $users
     *
     * @throws InvalidArgumentException
     */
    public function createUsersListResponseDTO(array $users, int $pageCount): UsersListResponseDTO
    {
        /** @var UserDTO[] $userDTOs */
        $userDTOs = $this->createUserDTOCollection($users)->toArray();

        return new UsersListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $userDTOs,
            $pageCount,
        );
    }

    public function createUsersInfoResponseDTO(User $user): UsersInfoResponseDTO
    {
        return new UsersInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createUserDTO($user),
        );
    }

    public function createUsersCreateResponseDTO(User $user): UsersCreateResponseDTO
    {
        return new UsersCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createUserDTO($user),
        );
    }

    public function createUsersUpdateResponseDTO(User $user): UsersUpdateResponseDTO
    {
        return new UsersUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createUserDTO($user),
        );
    }

    public function createUsersDeleteResponseDTO(): UsersDeleteResponseDTO
    {
        return new UsersDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }
}
