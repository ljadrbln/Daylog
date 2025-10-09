# UC-5 — Update Entry (Frontend AC)

## Scope
Frontend gateway validates only the parts of the API response it consumes.  
Authoritative sources:
- **HTTP status** (`res.ok` from Fetch)
- **success: boolean**
- **data: Entry** (updated entry object)

Ignored by frontend:
- The `status` field inside JSON
- Any diagnostics fields not used by UI

## Acceptance Criteria

- **AC-01 Happy Path**  
  200 OK, `success=true`, `typeof data === 'object'` ⇒ returns updated `Entry`.

- **AC-02 Non-2xx**  
  `res.ok=false` (e.g., 404/500) ⇒ throws `Error("HTTP {status} ...")`.

- **AC-03 Malformed JSON**  
  Missing or non-object `data` ⇒ throws `Error("Malformed response")`.

- **AC-04 Success=false**  
  200 OK but `success=false` ⇒ throws `Error("Malformed response: success")`.

## Coverage
- `frontend/tests/Unit/Entries/UpdateEntry/AC01_HappyPath.test.ts`;
- `frontend/tests/Unit/Entries/UpdateEntry/AC02_Non2xxResponse.test.ts`;
- `frontend/tests/Unit/Entries/UpdateEntry/AC03_MalformedJson.test.ts`;
- `frontend/tests/Unit/Entries/UpdateEntry/AC04_SuccessFalse.test.ts`.
