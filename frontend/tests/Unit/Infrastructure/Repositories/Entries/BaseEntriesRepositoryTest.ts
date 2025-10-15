import type {HttpTestCtxBase} from '@tests/Unit/Infrastructure/Repositories/BaseRepositoryTest';
import {
    createHttpCtx,
    mockJsonOnce,
    mockRejectOnce,
    assertSuccess
} from '@tests/Unit/Infrastructure/Repositories/BaseRepositoryTest';
import {EntryRepository} from '@src/Infrastructure/Repositories/Entries/EntryRepository';

export type RepositoryTestCtx = HttpTestCtxBase & {
    repo: EntryRepository;
};

export function createRepository(baseUrl = 'http://localhost'): RepositoryTestCtx {
    const ctx = createHttpCtx(baseUrl);
    const repo = new EntryRepository(ctx.httpClient);

    return {...ctx, repo};
}

export {mockJsonOnce, mockRejectOnce, assertSuccess};
