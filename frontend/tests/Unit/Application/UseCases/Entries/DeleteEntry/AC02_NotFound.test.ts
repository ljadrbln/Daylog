// tests/Unit/Application/UseCases/Entries/DeleteEntry/AC02_NotFound.test.ts

/**
 * AC-02 — Not found (404) — Application level.
 *
 * Purpose:
 * Verify that DeleteEntry returns a typed failure envelope
 * { success:false, status:404, code:'ENTRY_NOT_FOUND' } when repository signals absence.
 *
 * Mechanics:
 * - Repository.deleteById is mocked to resolve with null (no entry deleted).
 * - Use case executed with any UUID.
 * - Assert envelope fields and interaction (called once with id).
 *
 * @covers DeleteEntry
 * @group Application
 */

import {describe, it, expect, beforeEach, vi} from 'vitest';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import {DeleteEntry} from '@src/Application/UseCases/Entries/DeleteEntry/DeleteEntry';
import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';

describe('UC-4 / AC-02 — DeleteEntry returns 404 with ENTRY_NOT_FOUND', () => {
    let repo: EntryRepositoryInterface;

    beforeEach(() => {
        const list = vi.fn();
        const findById = vi.fn();
        const deleteById = vi.fn();
        const save = vi.fn();

        repo = {list, findById, deleteById, save};
    });

    it('returns { success:false, status:404, code:"ENTRY_NOT_FOUND" } when repository returns null', async () => {
        // Arrange
        const probe = EntryFactory.make(); // only for a valid UUID
        const id = probe.id;

        const deleteByIdMock = repo.deleteById as unknown as ReturnType<typeof vi.fn>;
        deleteByIdMock.mockResolvedValueOnce(null);

        const uc = new DeleteEntry(repo);
        const request = {id};

        // Act
        const response = await uc.execute(request);

        // Assert
        expect(response.success).toBe(false);
        expect(response.status).toBe(404);
        expect(response).toHaveProperty('code', 'ENTRY_NOT_FOUND');

        expect(deleteByIdMock).toHaveBeenCalledTimes(1);
        expect(deleteByIdMock).toHaveBeenCalledWith(id);
    });
});
