<?php

declare(strict_types=1);

namespace App\OilService;

use App\OilService\DBAL\Entity\User;
use App\OilService\DBAL\Repository\CustomerCarRepository;
use App\OilService\Factory\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;

class OilServiceUserService
{
    public function __construct(
        private readonly EntityFactory $entityFactory,
        private readonly EntityManagerInterface $entityManager,
        private readonly CustomerCarRepository $customerCarRepository,
        private readonly CustomerCarService $customerCarService,
    ) {
    }

    /**
     * @param string[]|null $customerCarIds
     */
    public function createUser(string $email, string $phone, string $fullName, ?array $customerCarIds = null): User
    {
        $user = $this->entityFactory->createUser($email, $phone, $fullName);

        $this->syncCustomerCars($user, $customerCarIds);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @param string[]|null $customerCarIds
     */
    public function updateUser(User $user, string $email, string $phone, string $fullName, ?array $customerCarIds = null): User
    {
        $user->setEmail($email);
        $user->setPhone($phone);
        $user->setFullName($fullName);

        $this->syncCustomerCars($user, $customerCarIds);

        $this->entityManager->flush();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    /**
     * @param string[]|null $customerCarIds
     */
    private function syncCustomerCars(User $user, ?array $customerCarIds): void
    {
        if ($customerCarIds === null) {
            return;
        }

        $ids = array_values(array_unique(array_filter($customerCarIds, 'is_string')));
        $cars = $ids === [] ? [] : $this->customerCarRepository->findBy([
            'id' => $ids,
        ]);

        $currentCars = $user->getCars()->toArray();

        foreach ($currentCars as $currentCar) {
            if (!in_array($currentCar->getId()->__toString(), $ids, true)) {
                $currentCar->setUser(null);
            }
        }

        foreach ($cars as $car) {
            $this->customerCarService->assignUser($car, $user);
        }
    }
}
