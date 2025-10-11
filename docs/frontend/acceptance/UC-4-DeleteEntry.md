# UC-4 — Delete Entry (Frontend AC)

## Scope

Frontend gateway validates only the parts of the API response it consumes.  
Authoritative sources:
- **HTTP status** (`res.ok` from Fetch)
- **success: boolean**
- **data: Entry** (deleted entry object)

Ignored by frontend:
- The `status` field inside JSON
- Any diagnostics fields not used by UI

## Acceptance Criteria

- **AC-01 Happy Path**  
  200 OK, `success=true`, `typeof data === 'object'` ⇒ returns deleted `Entry`.

- **AC-02 Non-2xx**  
  `res.ok=false` (e.g., 404/500) ⇒ throws `Error("HTTP {status} ...")`.

- **AC-03 Malformed JSON**  
  Missing or non-object `data` ⇒ throws `Error("Malformed response")`.

- **AC-04 Success=false**  
  200 OK but `success=false` ⇒ throws `Error("Malformed response: success")`.

## Coverage

- `frontend/tests/Unit/Entries/DeleteEntry/AC01_HappyPath.test.ts`;
- `frontend/tests/Unit/Entries/DeleteEntry/AC02_Non2xxResponse.test.ts`;
- `frontend/tests/Unit/Entries/DeleteEntry/AC03_MalformedJson.test.ts`;
- `frontend/tests/Unit/Entries/DeleteEntry/AC04_SuccessFalse.test.ts`.
