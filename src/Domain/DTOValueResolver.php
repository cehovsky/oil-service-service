<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Error\ErrorCollection;
use App\Domain\Error\ErrorFactory;
use App\Domain\Exception\InvalidDataException;
use App\Domain\Exception\InvalidResponseException;
use App\Domain\Exception\ValidationException;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DTOValueResolver
{
    public const string REQUEST_SERIALIZE_FORMAT = JsonEncoder::FORMAT;

    public const string RESPONSE_SERIALIZE_FORMAT = JsonEncoder::FORMAT;

    public const string RESULT_SUCCESS = 'OK';

    public const string RESULT_ERROR = 'Error';

    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
        private readonly ErrorFactory $errorFactory,
    ) {
    }

    /**
     * @template T of object
     * @param class-string<T> $dtoClassName
     *
     * @return T
     *
     * @throws InvalidDataException
     * @throws ValidationException
     */
    public function resolveRequest(
        Request $request,
        string $dtoClassName,
        string $format = self::REQUEST_SERIALIZE_FORMAT,
    ): object {
        /** @var T $object */
        $object = $this->resolve($request->getContent(), $dtoClassName, $format);

        return $object;
    }

    /**
     * @throws InvalidDataException
     * @throws ValidationException
     * @throws InvalidResponseException
     */
    public function resolveResponse(
        ResponseInterface $response,
        string $dtoClassName,
        string $format = self::RESPONSE_SERIALIZE_FORMAT,
    ): object {
        try {
            return $this->resolve($response->getContent(false), $dtoClassName, $format);
        } catch (
            TransportExceptionInterface
            | RedirectionExceptionInterface
            | ClientExceptionInterface
            | ServerExceptionInterface $e
        ) {
            throw new InvalidResponseException($e);
        }
    }

    /**
     * @throws InvalidDataException
     * @throws ValidationException
     */
    private function resolve(
        string $content,
        string $dtoClassName,
        string $format,
    ): object {
        try {
            $dto = $this->serializer->deserialize($content, $dtoClassName, $format);
            assert(is_object($dto));
        } catch (NotNormalizableValueException | NotEncodableValueException $e) {
            throw new InvalidDataException($e);
        }

        $dto = $this->sanitizeAndValidateStrictProperties($dto);

        $this->validateDTO($dto);

        return $dto;
    }

    /**
     * @param Constraint|array<int, Constraint>|null $constraints
     * @param string|GroupSequence|array<int, GroupSequence>|null $groups
     *
     * @throws ValidationException
     */
    public function validateDTO(
        mixed $dto,
        null | Constraint | array $constraints = null,
        null | string | GroupSequence | array $groups = null,
    ): void {
        $violations = $this->validator->validate(
            $dto,
            $constraints,
            $groups,
        );

        if ($violations->count() > 0) {
            $errorCollection = $this
                ->errorFactory
                ->createErrorCollectionFromConstraintViolationList(
                    $violations,
                );

            throw new ValidationException(
                'Invalid',
                0,
                null,
                $errorCollection,
            );
        }
    }

    /**
     * Fixes un-initialized properties and throws validation exception if un-initializable
     *
     * @throws Exception\InvalidArgumentException
     * @throws ValidationException
     */
    private function sanitizeAndValidateStrictProperties(object $dto): object
    {
        $errorCollection = new ErrorCollection();

        $reflection = new ReflectionClass($dto);
        foreach ($reflection->getProperties() as $property) {
            if ($property->isInitialized($dto)) {
                continue;
            }

            if ($property->getType()?->allowsNull()) {
                $property->setValue($dto, null);
                continue;
            }

            $errorCollection->add($this->errorFactory->createErrorItem(
                $property->getName(),
                'propertyOmitted',
                'This property must not be omitted.',
            ));
        }

        if (!$errorCollection->isEmpty()) {
            throw new ValidationException(errorCollection: $errorCollection);
        }

        return $dto;
    }
}
