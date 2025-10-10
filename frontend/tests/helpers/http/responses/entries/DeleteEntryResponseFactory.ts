import type {DeleteEntryResponse} from '@src/Application/DTO/Entries/DeleteEntry/DeleteEntryResponse';
import type {DeleteEntryRequest} from '@src/Application/DTO/Entries/DeleteEntry/DeleteEntryRequest';

/**
 * Response factory for UC-4 DeleteEntry.
 * AC-01 returns success:true with a full Entry object in data.
 */
export function ac01HappyPath(req: DeleteEntryRequest): DeleteEntryResponse {
    const entry = {
        id: req.id,
        title: 'Valid title',
        body: 'Valid body',
        date: '2025-02-12',
        createdAt: '2025-10-09T12:01:57+00:00',
        updatedAt: '2025-10-09T12:02:00+00:00'
    };

    return {
        success: true,
        status: 200,
        data: entry
    };
}
