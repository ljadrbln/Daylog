/**
 * @covers UpdateEntry
 * @group Application
 *
 * UC-5 / AC-01 — Happy path (update existing) — Unit.
 *
 * Purpose:
 * Verify that UpdateEntry calls repo.save() with id and provided fields,
 * and returns { success:true, status:200, data }.
 *
 * Mechanics:
 * - Build request with id and one changed field.
 * - Mock repo.save to resolve with updated entry from factory.
 * - Assert success envelope and repository interaction.
 */

import {describe, it, expect, beforeEach, vi} from 'vitest';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {UpdateEntryRequest} from '@src/Application/DTO/Entries/UpdateEntry/UpdateEntryRequest';
import {UpdateEntry} from '@src/Application/UseCases/Entries/UpdateEntry/UpdateEntry';

import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import {ensureSuccess} from '@tests/helpers/asserts';

describe('AC01 — UpdateEntry returns success=true and updated entry (Application)', () => {
    let repo: EntryRepositoryInterface;

    beforeEach(() => {
        const list = vi.fn();
        const findById = vi.fn();
        const deleteById = vi.fn();
        const save = vi.fn();

        repo = {list, findById, deleteById, save};
    });

    it('returns { success:true, status:200, data } when entry is updated', async () => {
        // Arrange
        const existing = EntryFactory.make();
        const id = existing.id;

        const request: UpdateEntryRequest = {
            id,
            title: 'Valid title'
        };

        const updated = EntryFactory.make({
            id,
            title: 'Valid title'
        });

        const saveMock = repo.save as unknown as ReturnType<typeof vi.fn>;
        saveMock.mockResolvedValueOnce(updated);

        const uc = new UpdateEntry(repo);

        // Act
        const response = await uc.execute(request);
        const ok = ensureSuccess(response);

        // Assert
        expect(ok.success).toBe(true);
        expect(ok.status).toBe(200);
        expect(ok.data).toEqual(updated);

        expect(saveMock).toHaveBeenCalledTimes(1);

        const calledWith = saveMock.mock.calls[0][0];
        expect(calledWith.id).toBe(id);
        expect(calledWith.title).toBe('Valid title');
    });
});
