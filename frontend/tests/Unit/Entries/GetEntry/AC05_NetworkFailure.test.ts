import {describe, it, expect, beforeEach, afterEach} from 'vitest';
import {createGateway, type GatewayTestCtx} from './BaseGetEntryGatewayTest';
import {ac05NetworkFailure as makeRequest} from '@tests/helpers/http/requests/entries/GetEntryRequestFactory';

/**
 * UC-3: Get Entry (Frontend, Gateway)
 *
 * Purpose:
 * Verify that GetEntryGateway rejects when fetch itself rejects (e.g., network failure).
 *
 * Mechanics:
 * - Build a valid request via request factory (no literals).
 * - Simulate fetch rejection with a TypeError('Network error').
 * - Expect gateway to reject with an error containing "Network".
 *
 * Cases:
 * - AC-05 — network-level rejection (fetch promise rejects).
 */
describe('AC05 — GetEntryGateway throws on network rejection', () => {
    let ctx: GatewayTestCtx;

    beforeEach(() => {
        ctx = createGateway();
    });

    afterEach(() => {
        ctx.cleanup();
    });

    it('throws when fetch rejects with a network error', async () => {
        // Arrange
        // prettier-ignore
        const error   = new TypeError('Network error');
        const request = makeRequest();

        ctx.fetchMock.mockRejectedValueOnce(error);

        // Act
        const fn = ctx.gateway.get(request);

        // Assert
        await expect(fn).rejects.toThrowError(/network/i);
    });
});
