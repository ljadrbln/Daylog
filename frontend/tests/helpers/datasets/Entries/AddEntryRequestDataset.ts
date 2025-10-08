/**
 * UC-1 (Add Entry) — request datasets.
 *
 * Purpose:
 * Provide minimal request payloads for AC-tests.
 * Only transport shape (title, body, date) — без доменной логики.
 */
export class AddEntryRequestDataset {
    /**
     * AC-01 — Happy Path request payload.
     */
    static ac01HappyPath(): {title: string; body: string; date: string} {
        const title = 'Valid title (UC-01, AC-01)';
        const body = 'Valid body';
        const date = '2025-02-14';

        const payload = {title, body, date};
        return payload;
    }

    /**
     * AC-04 — Invalid payload to trigger validation errors.
     */
    static ac04SuccessFalse(): {title: string; body: string; date: string} {
        const title = '';
        const body = '';
        const date = '2025-02-14';

        const payload = {title, body, date};
        return payload;
    }
}
[]