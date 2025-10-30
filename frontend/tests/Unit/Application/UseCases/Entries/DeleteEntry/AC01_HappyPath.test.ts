// tests/Unit/Application/UseCases/Entries/DeleteEntry/AC01_HappyPath.test.ts

/**
 * AC-01 — Happy path — Application level.
 *
 * Purpose:
 * Verify that DeleteEntry calls repository with the given id and
 * returns a typed success envelope { success:true, status:200, data:Entry }.
 *
 * Mechanics:
 * - Repository.deleteById is mocked to resolve with an Entry.
 * - Use case is executed with the same id.
 * - Assert envelope fields and interaction (called once with id).
 *
 * @covers DeleteEntry
 * @group Application
 */

import {describe, it, expect, beforeEach, vi} from 'vitest';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import {DeleteEntry} from '@src/Application/UseCases/Entries/DeleteEntry/DeleteEntry';
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import {ensureSuccess} from '@tests/helpers/asserts';

describe('UC-4 / AC-01 — DeleteEntry returns success=true with deleted entry', () => {
    let repo: EntryRepositoryInterface;

    beforeEach(() => {
        const list = vi.fn();
        const findById = vi.fn();
        const deleteById = vi.fn();
        const save = vi.fn();

        repo = {list, findById, deleteById, save};
    });

    it('wraps deleted entry in { success:true, status:200, data } and calls repo once', async () => {
        // Arrange
        const deleted = EntryFactory.make({title: 'Valid title'});
        const id = deleted.id;

        const deleteByIdMock = repo.deleteById as unknown as ReturnType<typeof vi.fn>;
        deleteByIdMock.mockResolvedValueOnce(deleted);

        const uc = new DeleteEntry(repo);
        const request = {id};

        // Act
        const response = await uc.execute(request);
        const okResponse = ensureSuccess(response);

        // Assert
        expect(okResponse.success).toBe(true);
        expect(okResponse.status).toBe(200);
        expect(okResponse.data).toEqual(deleted);

        expect(deleteByIdMock).toHaveBeenCalledTimes(1);
        expect(deleteByIdMock).toHaveBeenCalledWith(id);
    });
});
