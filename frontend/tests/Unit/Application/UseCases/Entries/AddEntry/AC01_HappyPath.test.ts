/**
 * @covers AddEntry
 * @group Application
 *
 * UC-1 / AC-01 — Happy path — Unit.
 *
 * Purpose:
 * Verify that AddEntry calls repo.save() with extracted fields and returns
 * { success:true, status:200, data: Entry } on success.
 *
 * Mechanics:
 * - Arrange a valid request (title/body/date).
 * - Mock repo.save to resolve with a concrete Entry returned by factory.
 * - Assert success envelope and repository interaction.
 */

import {describe, it, expect, beforeEach, vi} from 'vitest';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {AddEntryRequest} from '@src/Application/DTO/Entries/AddEntry/AddEntryRequest';
import {AddEntry} from '@src/Application/UseCases/Entries/AddEntry/AddEntry';

import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import {ensureSuccess} from '@tests/helpers/asserts';

describe('AC01 — AddEntry returns success=true and saved entry (Application)', () => {
    let repo: EntryRepositoryInterface;

    beforeEach(() => {
        const list = vi.fn();
        const findById = vi.fn();
        const deleteById = vi.fn();
        const save = vi.fn();

        repo = {list, findById, deleteById, save};
    });

    it('returns { success:true, status:200, data } for valid request', async () => {
        // Arrange
        const request: AddEntryRequest = {
            title: 'Valid title',
            body: 'Valid body',
            date: '2025-01-01'
        };

        const saved = EntryFactory.make({
            title: request.title,
            body: request.body,
            date: request.date
        });

        const saveMock = repo.save as unknown as ReturnType<typeof vi.fn>;
        saveMock.mockResolvedValueOnce(saved);

        const uc = new AddEntry(repo);

        // Act
        const response = await uc.execute(request);
        const ok = ensureSuccess(response);

        // Assert
        expect(ok.success).toBe(true);
        expect(ok.status).toBe(200);
        expect(ok.data).toEqual(saved);

        expect(saveMock).toHaveBeenCalledTimes(1);

        // We only check that save was called with an object carrying our fields.
        const calledWith = saveMock.mock.calls[0][0];
        expect(calledWith.title).toBe(request.title);
        expect(calledWith.body).toBe(request.body);
        expect(calledWith.date).toBe(request.date);
    });
});
