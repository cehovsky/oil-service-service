<?php

declare(strict_types=1);

namespace App\OilService;

use App\OilService\DBAL\Entity\Form;
use App\OilService\DBAL\Entity\Route;
use App\OilService\DBAL\Entity\User;
use App\OilService\DBAL\Enum\FormStatusEnum;
use App\OilService\DBAL\Enum\RealizationTimeSlotEnum;
use App\Domain\Exception\InvalidDataException;
use App\OilService\DBAL\Repository\RouteRepository;
use App\OilService\DBAL\Repository\UserRepository;
use App\OilService\Factory\EntityFactory;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FormService
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly RouteRepository $routeRepository,
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function createFormWithUser(
        string $fullName,
        string $phone,
        string $email,
        string $carModel,
        string $licensePlate,
        string $address,
        ?string $note,
        bool $isCompany,
        ?string $companyName,
        ?string $companyIdentificationNumber,
        ?string $companyTaxId,
        ?string $companyAddress,
        FormStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        ?Route $route = null,
    ): Form {
        if ($route !== null) {
            // When route is provided, use its date
            // TimeSlot stays from user choice
            $realizationDate = $route->getDate();
        }

        $user = $this->findOrCreateUser($email, $phone, $fullName);

        $form = $this->entityFactory->createForm(
            $fullName,
            $phone,
            $email,
            $carModel,
            $licensePlate,
            $address,
            $note,
            $isCompany,
            $companyName,
            $companyIdentificationNumber,
            $companyTaxId,
            $companyAddress,
            $status,
            $realizationTimeSlot,
            $realizationDate,
            $user,
            $route,
        );

        $this->entityManager->persist($form);
        $this->entityManager->flush();

        return $form;
    }

    public function updateForm(
        Form $form,
        string $fullName,
        string $phone,
        string $email,
        string $carModel,
        string $licensePlate,
        string $address,
        ?string $note,
        FormStatusEnum $status,
        RealizationTimeSlotEnum $realizationTimeSlot,
        DateTimeImmutable $realizationDate,
        bool $isCompany,
        ?string $companyName,
        ?string $companyIdentificationNumber,
        ?string $companyTaxId,
        ?string $companyAddress,
        string $userEmail,
        bool $routeProvided,
        ?string $routeId,
    ): Form {
        $route = $form->getRoute();

        if ($routeProvided) {
            $route = $this->findRoute($routeId);
        }

        $form->setFullName($fullName);
        $form->setPhone($phone);
        $form->setEmail($email);
        $form->setCarModel($carModel);
        $form->setLicensePlate($licensePlate);
        $form->setAddress($address);
        $form->setNote($note);
        $form->setIsCompany($isCompany);
        $form->setCompanyName($companyName);
        $form->setCompanyIdentificationNumber($companyIdentificationNumber);
        $form->setCompanyTaxId($companyTaxId);
        $form->setCompanyAddress($companyAddress);
        $form->setStatus($status);
        $form->setRoute($route);

        if ($route !== null) {
            $form->setRealizationDate($route->getDate());
            $form->setRealizationTimeSlot($realizationTimeSlot);
        } else {
            $form->setRealizationTimeSlot($realizationTimeSlot);
            $form->setRealizationDate($realizationDate);
        }

        if ($form->getUser()->getEmail() !== $userEmail) {
            $user = $this->findOrCreateUser($userEmail, $phone, $fullName);
            $form->setUser($user);
        }

        $this->entityManager->flush();

        return $form;
    }

    public function deleteForm(Form $form): void
    {
        $this->entityManager->remove($form);
        $this->entityManager->flush();
    }

    public function createRealizationDate(string $realizationDate): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $realizationDate);

        if ($date === false) {
            throw new InvalidDataException('Invalid realization date format.');
        }

        return $date;
    }

    private function findOrCreateUser(string $email, string $phone, string $fullName): User
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user !== null) {
            return $user;
        }

        $user = $this->entityFactory->createUser($email, $phone, $fullName);
        $this->entityManager->persist($user);

        return $user;
    }

    private function findRoute(?string $routeId): ?Route
    {
        if ($routeId === null) {
            return null;
        }

        $route = $this->routeRepository->find($routeId);

        if ($route === null) {
            throw new NotFoundHttpException();
        }

        return $route;
    }
}
