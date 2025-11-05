/**
 * @covers AddEntry
 * @group Application
 *
 * UC-1 / AC-01 — Happy path (create) — Unit.
 *
 * Purpose:
 * Verify that AddEntry calls repo.save() with id='' and provided fields,
 * and returns { success:true, status:200, data } on success.
 *
 * Mechanics:
 * - Build request with title/body/date.
 * - Mock repo.save to resolve with created entry from factory.
 * - Assert success envelope and repository interaction.
 */

import {describe, it, expect, beforeEach, vi} from 'vitest';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {AddEntryRequest} from '@src/Application/DTO/Entries/AddEntry/AddEntryRequest';
import {AddEntry} from '@src/Application/UseCases/Entries/AddEntry/AddEntry';

import {EntryFactory} from '@tests/helpers/domain/entries/EntryFactory';
import {ensureSuccess} from '@tests/helpers/asserts';

describe('AC01 — AddEntry returns success=true and created entry (Application)', () => {
    let repo: EntryRepositoryInterface;

    beforeEach(() => {
        repo = {
            findById: vi.fn(),
            list: vi.fn(),
            save: vi.fn(),
            deleteById: vi.fn()
        };
    });

    it('returns { success:true, status:200, data } when entry is created', async () => {
        // Arrange
        const request: AddEntryRequest = {
            title: 'Valid title',
            body: 'Valid body',
            date: '2025-11-05'
        };

        const created = EntryFactory.make({
            title: request.title,
            body: request.body,
            date: request.date
        });

        const saveMock = repo.save as unknown as ReturnType<typeof vi.fn>;
        saveMock.mockResolvedValueOnce(created);

        const uc = new AddEntry(repo);

        // Act
        const response = await uc.execute(request);
        const ok = ensureSuccess(response);

        // Assert
        expect(ok.success).toBe(true);
        expect(ok.status).toBe(200);
        expect(ok.data).toEqual(created);

        expect(saveMock).toHaveBeenCalledTimes(1);
        const calledWith = saveMock.mock.calls[0][0];

        expect(calledWith.id).toBe('');
        expect(calledWith.title).toBe(request.title);
        expect(calledWith.body).toBe(request.body);
        expect(calledWith.date).toBe(request.date);
        expect(calledWith.createdAt).toBe('');
        expect(calledWith.updatedAt).toBe('');
    });
});
