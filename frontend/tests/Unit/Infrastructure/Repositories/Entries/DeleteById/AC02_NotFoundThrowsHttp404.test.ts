/**
 * @covers EntryRepository.deleteById
 *
 * Purpose:
 * Validate repository behavior for UC-4.
 * Repository must bubble up HttpError from transport layer.
 *
 * Mechanics:
 * - Mock HTTP 404 response and assert that repo rejects with {status:404}.
 *
 * Cases:
 * - AC02 Not Found (404) ⇒ rejects with HttpError { status:404 }
 */
import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac02Non2xxResponse as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {notFound} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';

describe('AC02 — EntryRepository.deleteById rejects with HttpError{status:404}', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('rejects with {status:404} when API responds with 404 Not Found', async () => {
        // Arrange
        // prettier-ignore
        const request  = makeRequest();
        const response = notFound();

        mockJsonOnce(ctx.fetchMock, 404, response);

        // Act
        const promise = ctx.repo.findById(request.id);

        // Assert
        const expectation = expect(promise);
        await expectation.rejects.toMatchObject({status: 404});
    });
});
