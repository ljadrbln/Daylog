import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, mockJsonOnce, type GatewayTestCtx} from './BaseGetEntryGatewayTest';
import {okGet} from '@tests/helpers/api-responses/UC-3-GetEntry';

describe('UC-3 / AC-02 — HttpGetEntryGateway throws on non-2xx HTTP response', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws "HTTP {status} for {url}" when API responds with 404', async () => {
        const id = 'missing-id';
        const url = `http://localhost/api/entries/${id}`;
        const status = 404;

        // Тело неважно для проверки res.ok=false, кладём валидный JSON для консистентности
        const payload = okGet({
            id: 'stub',
            title: 'x',
            body: 'x',
            date: '2025-01-01',
            createdAt: '2025-01-01T00:00:00Z',
            updatedAt: '2025-01-01T00:00:00Z'
        });

        mockJsonOnce(ctx.fetchMock, status, payload);

        const fn = ctx.gw.get(id);
        const message = `HTTP ${status} for ${url}`;

        await expect(fn).rejects.toThrow(message);
    });
});
