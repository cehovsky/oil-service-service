<?php

declare(strict_types=1);

namespace App\Modules\OilService\Factory;

use App\Domain\DTOValueResolver;
use App\Domain\Exception\InvalidArgumentException;
use App\Modules\OilService\DTO\FormCreateResponseDTO;
use App\Modules\OilService\DTO\FormDeleteResponseDTO;
use App\Modules\OilService\DTO\FormDTO;
use App\Modules\OilService\DTO\FormDTOCollection;
use App\Modules\OilService\DTO\FormInfoResponseDTO;
use App\Modules\OilService\DTO\FormListResponseDTO;
use App\Modules\OilService\DTO\FormUpdateResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserCreateResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserDeleteResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserDTO;
use App\Modules\OilService\DTO\OilServiceUserDTOCollection;
use App\Modules\OilService\DTO\OilServiceUserInfoResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserListDTO;
use App\Modules\OilService\DTO\OilServiceUserListResponseDTO;
use App\Modules\OilService\DTO\OilServiceUserUpdateResponseDTO;
use App\Modules\OilService\DTO\AvailableTermDTO;
use App\Modules\OilService\DTO\AvailableTermListResponseDTO;
use App\OilService\DBAL\Entity\Form;
use App\OilService\DBAL\Entity\Term;
use App\OilService\DBAL\Entity\User;

class DTOFactory
{
    public function createFormCreateResponseDTO(): FormCreateResponseDTO
    {
        return new FormCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            true,
        );
    }

    public function createOilServiceUserDTO(User $user): OilServiceUserDTO
    {
        return new OilServiceUserDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getFullName(),
            $user->getCreatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function createOilServiceUserListDTO(User $user): OilServiceUserListDTO
    {
        return new OilServiceUserListDTO(
            $user->getId()->__toString(),
            $user->getEmail(),
            $user->getPhone(),
            $user->getFullName(),
            $user->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $user->getForms()->count(),
        );
    }

    public function createFormDTO(Form $form): FormDTO
    {
        $term = $form->getTerm();

        return new FormDTO(
            $form->getId()->__toString(),
            $form->getFormattedIdent(),
            $form->getFullName(),
            $form->getPhone(),
            $form->getEmail(),
            $form->getCarModel(),
            $form->getLicensePlate(),
            $form->getAddress(),
            $form->getNote(),
            $form->getIsCompany(),
            $form->getCompanyName(),
            $form->getCompanyIdentificationNumber(),
            $form->getCompanyTaxId(),
            $form->getCompanyAddress(),
            $form->getStatus()->value,
            $form->getRealizationTimeSlot()->value,
            $form->getRealizationDate()->format('Y-m-d'),
            $form->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $term?->getId()->__toString(),
            $term?->getDate()->format('Y-m-d'),
            $term?->getTimeSlot()->value,
            $this->createOilServiceUserDTO($form->getUser()),
        );
    }

    /**
     * @param Form[] $forms
     *
     * @throws InvalidArgumentException
     */
    public function createFormDTOCollection(array $forms): FormDTOCollection
    {
        $collection = new FormDTOCollection();

        foreach ($forms as $form) {
            $collection->add($this->createFormDTO($form));
        }

        return $collection;
    }

    /**
     * @param Form[] $forms
     *
     * @throws InvalidArgumentException
     */
    public function createFormListResponseDTO(array $forms, int $pageCount): FormListResponseDTO
    {
        /** @var FormDTO[] $formDTOs */
        $formDTOs = $this->createFormDTOCollection($forms)->toArray();

        return new FormListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $formDTOs,
            $pageCount,
        );
    }

    public function createFormInfoResponseDTO(Form $form): FormInfoResponseDTO
    {
        return new FormInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createFormDTO($form),
        );
    }

    public function createFormUpdateResponseDTO(Form $form): FormUpdateResponseDTO
    {
        return new FormUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createFormDTO($form),
        );
    }

    public function createFormDeleteResponseDTO(): FormDeleteResponseDTO
    {
        return new FormDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createAvailableTermDTO(Term $term): AvailableTermDTO
    {
        return new AvailableTermDTO(
            $term->getDate()->format('Y-m-d'),
            $term->getTimeSlot()->value,
        );
    }

    /**
     * @param Term[] $terms
     */
    public function createAvailableTermListResponseDTO(array $terms): AvailableTermListResponseDTO
    {
        $termDTOs = [];

        foreach ($terms as $term) {
            $termDTOs[] = $this->createAvailableTermDTO($term);
        }

        return new AvailableTermListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $termDTOs,
        );
    }

    /**
     * @param User[] $users
     *
     * @throws InvalidArgumentException
     */
    public function createOilServiceUserDTOCollection(array $users): OilServiceUserDTOCollection
    {
        $collection = new OilServiceUserDTOCollection();

        foreach ($users as $user) {
            $collection->add($this->createOilServiceUserListDTO($user));
        }

        return $collection;
    }

    /**
     * @param User[] $users
     *
     * @throws InvalidArgumentException
     */
    public function createOilServiceUserListResponseDTO(array $users, int $pageCount): OilServiceUserListResponseDTO
    {
        /** @var OilServiceUserListDTO[] $userDTOs */
        $userDTOs = $this->createOilServiceUserDTOCollection($users)->toArray();

        return new OilServiceUserListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $userDTOs,
            $pageCount,
        );
    }

    public function createOilServiceUserInfoResponseDTO(User $user): OilServiceUserInfoResponseDTO
    {
        return new OilServiceUserInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createOilServiceUserDTO($user),
        );
    }

    public function createOilServiceUserCreateResponseDTO(User $user): OilServiceUserCreateResponseDTO
    {
        return new OilServiceUserCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createOilServiceUserDTO($user),
        );
    }

    public function createOilServiceUserUpdateResponseDTO(User $user): OilServiceUserUpdateResponseDTO
    {
        return new OilServiceUserUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createOilServiceUserDTO($user),
        );
    }

    public function createOilServiceUserDeleteResponseDTO(): OilServiceUserDeleteResponseDTO
    {
        return new OilServiceUserDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }
}
