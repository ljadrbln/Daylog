import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac02Non2xxResponse as makeRequest} from '@tests/helpers/http/requests/entries/AddEntryRequestFactory';
import {
    makeBadRequest,
    makeInternalError
} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';
import {
    ac03MalformedJson as makeMalformed,
    ac04SuccessFalse as makeSuccessFalse
} from '@tests/helpers/http/responses/entries/AddEntryResponseFactory';

/**
 * UC-1: Save Entry (Repository)
 * Verifies EntryRepository.save rejects on:
 * - AC02a — 400 Bad Request
 * - AC02b — 500 Internal Server Error
 * - AC03 — malformed JSON (success=true, data=null)
 * - AC04 — success=false
 */
describe('AC02–AC04 — EntryRepository.save rejects on invalid responses', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    const malformedMessage = 'Malformed response for PUT /api/entries/:id';

    const cases = [
        ['AC02 non-2xx 400', 400, makeBadRequest(), /400|bad request/i],
        ['AC02 non-2xx 500', 500, makeInternalError(), /500|internal/i],
        ['AC03 malformed', 200, makeMalformed(), malformedMessage],
        ['AC04 success=false', 200, makeSuccessFalse(), malformedMessage]
    ] as const;

    describe.each(cases)('%s', (_name, status, payload, msg) => {
        it('rejects', async () => {
            const req = makeRequest();
            mockJsonOnce(ctx.fetchMock, status, payload);

            const fn = ctx.repo.save(req);
            await expect(fn).rejects.toThrow(msg);
        });
    });
});
