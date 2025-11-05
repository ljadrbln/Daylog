/**
 * @covers AddEntry
 * @group Application
 *
 * UC-1 / AC-02 — Validation error (422) — Unit.
 *
 * Purpose:
 * Validate that AddEntry propagates DomainValidationError (422)
 * when repository.save() rejects with VALIDATION_FAILED.
 *
 * Mechanics:
 * - Mock repo.save to reject with { status:422, code:'VALIDATION_FAILED' }.
 * - Build request with invalid fields (e.g., empty title or too long body).
 * - Assert rejection shape and repository interaction.
 *
 * Cases:
 * - AC02 Validation → rejects with 422 error.
 */

import {describe, it, expect, beforeEach, vi} from 'vitest';
import type {EntryRepositoryInterface} from '@src/Domain/Interfaces/Entries/EntryRepositoryInterface';
import type {AddEntryRequest} from '@src/Application/DTO/Entries/AddEntry/AddEntryRequest';
import {AddEntry} from '@src/Application/UseCases/Entries/AddEntry/AddEntry';

describe('AC02 — AddEntry propagates DomainValidationError (422) (Application)', () => {
    let repo: EntryRepositoryInterface;

    beforeEach(() => {
        repo = {
            findById: vi.fn(),
            list: vi.fn(),
            save: vi.fn(),
            deleteById: vi.fn()
        };
    });

    function makeValidationError() {
        const message = 'VALIDATION_FAILED';
        const error = new Error(message) as Error & {status?: number; code?: string};
        error.status = 422;
        error.code = 'VALIDATION_FAILED';
        return error;
    }

    it('rejects with { status:422, code:"VALIDATION_FAILED" }', async () => {
        // Arrange
        const request: AddEntryRequest = {
            title: '', // invalid: required
            body: 'x'.repeat(6000), // invalid: too long (example)
            date: '2025-11-05'
        };

        const saveMock = repo.save as unknown as ReturnType<typeof vi.fn>;
        const validationError = makeValidationError();
        saveMock.mockRejectedValueOnce(validationError);

        const uc = new AddEntry(repo);

        // Act + Assert
        await expect(uc.execute(request)).rejects.toMatchObject({
            status: 422,
            code: 'VALIDATION_FAILED',
            message: 'VALIDATION_FAILED'
        });

        expect(saveMock).toHaveBeenCalledTimes(1);
    });
});
