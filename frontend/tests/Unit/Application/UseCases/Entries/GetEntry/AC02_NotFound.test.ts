/**
 * @covers GetEntry
 * @group Application
 *
 * Purpose:
 * Validate that UC-3 returns { success:false, status:404, code:'ENTRY_NOT_FOUND' } when repository returns null.
 *
 * Mechanics:
 * - Mock EntryRepository.findById to resolve null.
 * - Build request with arbitrary UUID.
 * - Assert failure envelope and repository interaction.
 *
 * Cases:
 * - AC02 Not found → 404
 */

import {describe, it, expect, beforeEach, vi} from 'vitest';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import {GetEntry} from '@src/Application/UseCases/Entries/GetEntry/GetEntry';

import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';

describe('AC02 — GetEntry returns success=false and 404 when entry is not found (Application)', () => {
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
        const sample = EntryFactory.make(); // use factory to get a valid-looking UUID
        const id = sample.id;

        const findByIdMock = repo.findById as unknown as ReturnType<typeof vi.fn>;
        findByIdMock.mockResolvedValueOnce(null);

        const uc = new GetEntry(repo);
        const request = {id};

        // Act
        const response = await uc.execute(request);

        // Assert
        expect(response.success).toBe(false);
        expect(response.status).toBe(404);
        expect(response).toHaveProperty('code', 'ENTRY_NOT_FOUND');

        expect(findByIdMock).toHaveBeenCalledTimes(1);
        expect(findByIdMock).toHaveBeenCalledWith(id);
    });
});
