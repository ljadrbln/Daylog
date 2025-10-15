import {describe, it, expect} from 'vitest';
import {
    createRepository,
    mockJsonOnce
} from '@tests/Unit/Infrastructure/Repositories/Entries/BaseEntriesRepositoryTest';

import {ac02Non2xxResponse as makeRequest} from '@tests/helpers/http/requests/entries/DeleteEntryRequestFactory';

import {
    makeBadRequest,
    makeInternalError
} from '@tests/helpers/http/responses/common/Non2xxResponseFactory';

import {
    ac03MalformedJson as makeMalformed,
    ac04SuccessFalse as makeSuccessFalse
} from '@tests/helpers/http/responses/entries/DeleteEntryResponseFactory';

const malformedMessage = 'Malformed response for DELETE /api/entries/:id';
const cases = [
    ['AC02 non-2xx 400', 400, makeBadRequest(), /400|bad request/i],
    ['AC02 non-2xx 500', 500, makeInternalError(), /500|internal/i],
    ['AC03 malformed', 200, makeMalformed(), malformedMessage],
    ['AC04 success=false', 200, makeSuccessFalse(), malformedMessage]
] as const;

describe.each(cases)('%s', (_name, status, payload, msg) => {
    it('rejects', async () => {
        // Arrange
        const req = makeRequest();
        const {repo, fetchMock} = createRepository();
        mockJsonOnce(fetchMock, status, payload);

        // Act
        const fn = repo.deleteById(req.id);

        // Assert
        await expect(fn).rejects.toThrow(msg);
    });
});
