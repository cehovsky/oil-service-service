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
use App\Modules\OilService\DTO\TermDTO;
use App\Modules\OilService\DTO\TermCreateResponseDTO;
use App\Modules\OilService\DTO\TermUpdateResponseDTO;
use App\Modules\OilService\DTO\TermDeleteResponseDTO;
use App\Modules\OilService\DTO\TermInfoResponseDTO;
use App\Modules\OilService\DTO\TermListResponseDTO;
use App\Modules\OilService\DTO\RouteDTO;
use App\Modules\OilService\DTO\RouteCreateResponseDTO;
use App\Modules\OilService\DTO\RouteUpdateResponseDTO;
use App\Modules\OilService\DTO\RouteDeleteResponseDTO;
use App\Modules\OilService\DTO\RouteInfoResponseDTO;
use App\Modules\OilService\DTO\RouteListResponseDTO;
use App\Modules\OilService\DTO\CarDTO;
use App\Modules\OilService\DTO\CarCreateResponseDTO;
use App\Modules\OilService\DTO\CarUpdateResponseDTO;
use App\Modules\OilService\DTO\CarDeleteResponseDTO;
use App\Modules\OilService\DTO\CarInfoResponseDTO;
use App\Modules\OilService\DTO\CarListResponseDTO;
use App\OilService\DBAL\Entity\Car;
use App\OilService\DBAL\Entity\Form;
use App\OilService\DBAL\Entity\Route;
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
        $route = $form->getRoute();

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
            $route?->getId()->__toString(),
            $route?->getDate()->format('Y-m-d'),
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

    public function createTermDTO(Term $term): TermDTO
    {
        return new TermDTO(
            $term->getId()->__toString(),
            $term->getDate()->format('Y-m-d'),
            $term->getTimeSlot()->value,
            $term->getIsActive(),
            $term->getMaxCount(),
            $term->getCreatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * @param Term[] $terms
     */
    public function createTermListResponseDTO(array $terms, int $pageCount): TermListResponseDTO
    {
        $termDTOs = [];

        foreach ($terms as $term) {
            $termDTOs[] = $this->createTermDTO($term);
        }

        return new TermListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $termDTOs,
            $pageCount,
        );
    }

    public function createTermInfoResponseDTO(Term $term): TermInfoResponseDTO
    {
        return new TermInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createTermDTO($term),
        );
    }

    public function createTermCreateResponseDTO(Term $term): TermCreateResponseDTO
    {
        return new TermCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createTermDTO($term),
        );
    }

    public function createTermUpdateResponseDTO(Term $term): TermUpdateResponseDTO
    {
        return new TermUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createTermDTO($term),
        );
    }

    public function createTermDeleteResponseDTO(): TermDeleteResponseDTO
    {
        return new TermDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createRouteDTO(Route $route): RouteDTO
    {
        $car = $route->getCar();
        $termIds = [];

        foreach ($route->getTerms() as $term) {
            $termIds[] = $term->getId()->__toString();
        }

        return new RouteDTO(
            $route->getId()->__toString(),
            $car?->getId()->__toString(),
            $car ? sprintf('%s (%s)', $car->getLabel(), $car->getLicensePlate()) : null,
            $route->getIsActive(),
            $route->getDate()->format('Y-m-d'),
            $route->getCreatedAt()->format(\DateTimeInterface::ATOM),
            $termIds,
        );
    }

    /**
     * @param Route[] $routes
     */
    public function createRouteListResponseDTO(array $routes, int $pageCount): RouteListResponseDTO
    {
        $routeDTOs = [];

        foreach ($routes as $route) {
            $routeDTOs[] = $this->createRouteDTO($route);
        }

        return new RouteListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $routeDTOs,
            $pageCount,
        );
    }

    public function createRouteInfoResponseDTO(Route $route): RouteInfoResponseDTO
    {
        return new RouteInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createRouteDTO($route),
        );
    }

    public function createRouteCreateResponseDTO(Route $route): RouteCreateResponseDTO
    {
        return new RouteCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createRouteDTO($route),
        );
    }

    public function createRouteUpdateResponseDTO(Route $route): RouteUpdateResponseDTO
    {
        return new RouteUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createRouteDTO($route),
        );
    }

    public function createRouteDeleteResponseDTO(): RouteDeleteResponseDTO
    {
        return new RouteDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }

    public function createCarDTO(Car $car): CarDTO
    {
        return new CarDTO(
            $car->getId()->__toString(),
            $car->getLabel(),
            $car->getIdent(),
            $car->getLicensePlate(),
            $car->getStatus()->value,
            $car->getCreatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * @param Car[] $cars
     */
    public function createCarListResponseDTO(array $cars, int $pageCount): CarListResponseDTO
    {
        $carDTOs = [];

        foreach ($cars as $car) {
            $carDTOs[] = $this->createCarDTO($car);
        }

        return new CarListResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $carDTOs,
            $pageCount,
        );
    }

    public function createCarInfoResponseDTO(Car $car): CarInfoResponseDTO
    {
        return new CarInfoResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createCarDTO($car),
        );
    }

    public function createCarCreateResponseDTO(Car $car): CarCreateResponseDTO
    {
        return new CarCreateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createCarDTO($car),
        );
    }

    public function createCarUpdateResponseDTO(Car $car): CarUpdateResponseDTO
    {
        return new CarUpdateResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
            $this->createCarDTO($car),
        );
    }

    public function createCarDeleteResponseDTO(): CarDeleteResponseDTO
    {
        return new CarDeleteResponseDTO(
            DTOValueResolver::RESULT_SUCCESS,
            time(),
        );
    }
}
