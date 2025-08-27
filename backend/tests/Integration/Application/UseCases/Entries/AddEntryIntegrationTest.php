<?php
declare(strict_types=1);

namespace Daylog\Tests\Integration\Application\UseCases\Entries;

use Codeception\Test\Unit;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequestInterface;
use Daylog\Application\DTO\Entries\AddEntry\AddEntryRequest;
use Daylog\Application\Exceptions\DomainValidationException;
use Daylog\Application\UseCases\Entries\AddEntry;
use Daylog\Application\Validators\Entries\AddEntry\AddEntryValidator;
use Daylog\Domain\Models\Entries\Entry;
use Daylog\Domain\Services\UuidGenerator;
use Daylog\Tests\Support\Helper\EntryTestData;
use Daylog\Tests\Support\Fakes\FakeEntryRepository;

/**
 * Integration test for UC-1 AddEntry.
 *
 * Purpose: exercise the pipeline "real validator + use case" with a repository spy.
 * Mechanics:
 * - Happy path: validator passes, UC persists via repository, returns UUID v4.
 * - Error path: validator throws; repository must not be touched.
 *
 * @covers \Daylog\Application\UseCases\Entries\AddEntry
 * @covers \Daylog\Application\Validators\Entries\AddEntryValidator
 */
final class AddEntryIntegrationTest extends Unit
{
    /**
     * Happy path: real validator, UC persists entry and returns UUID v4.
     */
    public function testHappyPathPersistsAndReturnsUuid(): void
    {
        /** Arrange **/
        $data = EntryTestData::getOne();

        /** @var AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        $repo      = new FakeEntryRepository();
        $validator = new AddEntryValidator();
        $useCase   = new AddEntry($repo, $validator);

        /** Act **/
        $response = $useCase->execute($request);
        $uuid = $response->getId();

        /** Assert **/
        $isValid = UuidGenerator::isValid($uuid);
        $this->assertTrue($isValid);

        $savedOnce = ($repo->getSaveCalls() === 1);
        $this->assertTrue($savedOnce);

        $lastSaved = $repo->getLastSaved();
        $this->assertInstanceOf(Entry::class, $lastSaved);
    }

    /**
     * Error path: validator throws; repository is not touched.
     */
    public function testValidatorErrorDoesNotTouchRepository(): void
    {
        /** Arrange **/
        $data  = EntryTestData::getOne();
        $data['title'] = ''; // invalid: empty title

        /** @var AddEntryRequestInterface $request */
        $request = AddEntryRequest::fromArray($data);

        $repo = new FakeEntryRepository();

        $validatorClass = AddEntryValidator::class;
        $validator      = new $validatorClass();

        $ucClass = AddEntry::class;
        $uc      = new $ucClass($repo, $validator);

        /** Assert **/
        $this->expectException(DomainValidationException::class);

        /** Act **/
        try {
            $uc->execute($request);
        } finally {
            $saveCalls = $repo->getSaveCalls();
            $this->assertSame(0, $saveCalls);
            $this->assertNull($repo->getLastSaved());
        }
    }
}