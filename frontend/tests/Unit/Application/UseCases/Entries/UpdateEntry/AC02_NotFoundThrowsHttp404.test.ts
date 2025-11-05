/**
 * @covers UpdateEntry
 * @group Application
 *
 * UC-5 / AC-02 — Not found (404) — Unit.
 *
 * Purpose:
 * Validate that UpdateEntry propagates NotFoundError (404)
 * when repository.save() throws ENTRY_NOT_FOUND.
 *
 * Mechanics:
 * - Mock EntryRepository.save to reject with { status:404, code:'ENTRY_NOT_FOUND' }.
 * - Build request with a valid-looking UUID and a changed field.
 * - Assert rejection shape and repository interaction.
 *
 * Cases:
 * - AC02 Not found → rejects with 404 error.
 */

import {describe, it, expect, beforeEach, vi} from 'vitest';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {UpdateEntryRequest} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryRequest';
import {UpdateEntry} from '@src/Application/UseCases/Entries/UpdateEntry/UpdateEntry';

import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';

describe('AC02 — UpdateEntry propagates NotFoundError (404) (Application)', () => {
    let repo: EntryRepositoryInterface;

    beforeEach(() => {
        repo = {
            findById: vi.fn(),
            list: vi.fn(),
            save: vi.fn(),
            deleteById: vi.fn()
        };
    });

    it('rejects with NotFoundError { status:404, code:"ENTRY_NOT_FOUND" }', async () => {
        // Arrange
        const sample = EntryFactory.make();
        const id = sample.id;

        const request: UpdateEntryRequest = {
            id,
            title: 'New title'
        };

        const notFoundError = new Error('ENTRY_NOT_FOUND') as Error & {
            status?: number;
            code?: string;
        };
        notFoundError.status = 404;
        notFoundError.code = 'ENTRY_NOT_FOUND';

        const saveMock = repo.save as unknown as ReturnType<typeof vi.fn>;
        saveMock.mockRejectedValueOnce(notFoundError);

        const uc = new UpdateEntry(repo);

        // Act + Assert
        await expect(uc.execute(request)).rejects.toMatchObject({
            status: 404,
            code: 'ENTRY_NOT_FOUND',
            message: 'ENTRY_NOT_FOUND'
        });

        expect(saveMock).toHaveBeenCalledTimes(1);
        const calledWith = saveMock.mock.calls[0][0];
        expect(calledWith.id).toBe(id);
        expect(calledWith.title).toBe('New title');
    });
});
