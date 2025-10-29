/**
 * @covers GetEntry
 * @group Application
 *
 * Purpose:
 * Validate that UC-3 returns { success:true, status:200, data: Entry } when repository finds the entry.
 *
 * Mechanics:
 * - Mock EntryRepository.findById to resolve a concrete Entry object.
 * - Build request with the same id.
 * - Assert response envelope and repository interaction.
 */

import {describe, it, expect, beforeEach, vi} from 'vitest';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import {GetEntry} from '@src/Application/UseCases/Entries/GetEntry/GetEntry';

import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';

describe('AC01 — GetEntry returns success=true and entry (Application)', () => {
    let repo: EntryRepositoryInterface;

    beforeEach(() => {
        const list = vi.fn();
        const findById = vi.fn();
        const deleteById = vi.fn();
        const save = vi.fn();

        repo = {list, findById, deleteById, save};
    });

    it('returns { success:true, status:200, data: entry } when entry exists', async () => {
        // Arrange
        const existing = EntryFactory.make({title: 'Valid title'});
        const id = existing.id;

        const findByIdMock = repo.findById as unknown as ReturnType<typeof vi.fn>;
        findByIdMock.mockResolvedValueOnce(existing);

        const uc = new GetEntry(repo);
        const request = {id};

        // Act
        const response = await uc.execute(request);

        // Assert
        expect(response.success).toBe(true);
        expect(response.status).toBe(200);
        expect(response.data).toEqual(existing);

        expect(findByIdMock).toHaveBeenCalledTimes(1);
        expect(findByIdMock).toHaveBeenCalledWith(id);
    });
});
