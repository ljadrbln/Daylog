<?php
declare(strict_types=1);

namespace Daylog\Tests\Functional\Presentation\Controllers\Entries\Api\UpdateEntry;

use Daylog\Tests\FunctionalTester;
use Daylog\Tests\Support\Helper\EntriesSeeding;
use Daylog\Tests\Support\Scenarios\Entries\UpdateEntryScenario;

/**
 * AC-01 (happy path — title): Update only the title and refresh updatedAt.
 *
 * Purpose:
 *   Verify the API endpoint PUT /api/entries/{id} updates only the provided 'title'
 *   and refreshes 'updatedAt' while keeping 'createdAt' intact and other fields unchanged.
 *
 * Mechanics:
 *   - Seed a single entry row via EntriesSeeding using UpdateEntryScenario::ac01TitleOnly();
 *   - Issue PUT /api/entries/{id} with a JSON body containing only the new title;
 *   - Assert HTTP 200 + JSON envelope {success:true} per project contract;
 *   - Assert response payload: title replaced, body/date unchanged, createdAt unchanged,
 *     updatedAt strictly greater than previous.
 *
 * @covers \Daylog\Configuration\Providers\Entries\UpdateEntryProvider
 * @covers \Daylog\Application\UseCases\Entries\UpdateEntry\UpdateEntry
 * @group UC-UpdateEntry
 */
final class AC01_HappyPath_TitleOnlyCest extends BaseUpdateEntryFunctionalCest
{
    /**
     * API accepts title-only update and refreshes updatedAt.
     *
     * @param FunctionalTester $I Codeception functional tester.
     * @return void
     */
    public function testHappyPathUpdatesTitleAndRefreshesUpdatedAt(FunctionalTester $I): void
    {
        // Arrange — seed DB and prepare request
        $dataset  = UpdateEntryScenario::ac01TitleOnly();
        $rows     = $dataset['rows'];
        $targetId = $dataset['targetId'];
        $newTitle = $dataset['newTitle'];

        EntriesSeeding::intoDb($rows);

        $payload = ['title' => $newTitle];

        // Act — call API
        $this->updateEntry($I, $targetId, $payload);

        // Assert — 200 OK and success envelope
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();

        $successEnvelope = ['success' => true];
        $I->seeResponseContainsJson($successEnvelope);

        // Decode response for field-level assertions
        $rawResponse = $I->grabResponse();
        $decoded     = json_decode($rawResponse, true);

        $data   = $decoded['data'] ?? [];
        $entry  = $data['entry'] ?? [];

        // Baseline (pre-update) values
        $before = $rows[0];

        // Guard: ensure shape contains expected keys
        $requiredKeys = ['id', 'title', 'body', 'date', 'createdAt', 'updatedAt'];
        foreach ($requiredKeys as $key) {
            $message = sprintf('Response entry must contain key "%s"', $key);
            $I->assertArrayHasKey($key, $entry, $message);
        }

        // Field equality / inequality checks
        $message = 'Response id must match the target id';
        $I->assertSame($targetId, $entry['id'], $message);

        $message = 'Title must be updated to the new value';
        $I->assertSame($newTitle, $entry['title'], $message);

        $message = 'Body must remain unchanged when only title is updated';
        $I->assertSame($before['body'], $entry['body'], $message);

        $message = 'Date must remain unchanged when only title is updated';
        $I->assertSame($before['date'], $entry['date'], $message);

        $message = 'createdAt must remain unchanged after update';
        $I->assertSame($before['createdAt'], $entry['createdAt'], $message);

        // updatedAt must be strictly greater than before (ISO-8601 lexicographic compare is valid)
        $message = 'updatedAt must be strictly greater than the previous value';
        $I->assertTrue($entry['updatedAt'] > $before['updatedAt'], $message);
    }
}
