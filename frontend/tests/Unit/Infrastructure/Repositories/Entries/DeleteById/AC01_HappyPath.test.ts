/**
 * @covers EntryRepository.deleteById
 *
 * Purpose:
 * Validate repository behavior for UC-4 (DeleteEntry) on the happy path.
 *
 * Mechanics:
 * - Mock HTTP 200 response for DELETE /api/entries/:id
 * - Ensure transport envelope is valid and data is a proper Entry object.
 *
 * Cases:
 * - AC01 Happy path — returns the deleted Entry object
 */

import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {
    createRepository,
    mockJsonOnce,
    type RepositoryTestCtx
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';
import {ac01HappyPath as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';
import {ac01HappyPath as makeResponse} from '@tests/helpers/http/responses/entries/DeleteEntryResponseFactory';

/** UC-4 Delete Entry (Repository): 200 + {success:true} -> resolves void. */
describe('AC01 — EntryRepository.deleteById resolves on success', () => {
    let ctx: RepositoryTestCtx;

    beforeEach(() => {
        ctx = createRepository();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('resolves when API returns success=true', async () => {
        // Arrange
        const req = makeRequest();
        const res = makeResponse(req);
        mockJsonOnce(ctx.fetchMock, 200, res);

        // Act
        await ctx.repo.deleteById(req.id);

        // Assert
        expect(res).toBeDefined();
        expect(typeof res).toBe('object');

        expect(res).toHaveProperty('id', req.id);
        expect(res).toHaveProperty('title', req.);
        expect(res).toHaveProperty('body', 'Valid body');
        expect(res).toHaveProperty('date', '2025-02-12');

        // createdAt / updatedAt — строки ISO
        expect(typeof result.createdAt).toBe('string');
        expect(typeof result.updatedAt).toBe('string');
    });
});




// import { createRepository } from '../../../_helpers/createRepository';
// import { mockJsonOnce } from '../../../_helpers/http/mockJsonOnce';
// import { makeDeletedEntryPayload } from '../../../_helpers/http/responses/entries/DeleteEntryResponseFactory';

// describe('UC-4 DeleteEntry — AC01 Happy path', () => {
//   it('returns deleted Entry object', async () => {
//     const ctx = createRepository();
//     const { repo, fetchMock, ids } = ctx;
//     const entryId = ids.entryId;

//     // Подготавливаем корректный payload, который вернёт API в data
//     const payload = makeDeletedEntryPayload({
//       id: entryId,
//       title: 'Valid title',
//       body: 'Valid body',
//       date: '2025-02-12',
//     });

//     // Мокаем успешный DELETE 200 с объектом Entry в data
//     mockJsonOnce(fetchMock, 200, payload);

//     const result = await repo.deleteById(entryId);


//   });
// });
