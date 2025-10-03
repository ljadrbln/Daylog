# UC-2 — List Entries (Frontend AC)

## Scope
Frontend gateway validates only the parts of the API response it consumes.  
Authoritative sources:
- **HTTP status** (`res.ok` from Fetch)  
- **success: boolean**  
- **data.items: Entry[]**  

Ignored by frontend:
- The `status` field inside JSON (duplicated with HTTP status)  
- Any diagnostics fields not used by UI  

## Acceptance Criteria

- **AC-01 Happy Path**  
  200 OK, `success=true`, `Array.isArray(data.items)` ⇒ returns `Entry[]`.

- **AC-02 Non-2xx**  
  `res.ok=false` (e.g., 500) ⇒ throws `Error("HTTP {status} ...")`.

- **AC-03 Malformed JSON**  
  Missing or non-array `data.items` ⇒ throws `Error("Malformed response")`.

- **AC-04 Success=false**  
  200 OK but `success=false` ⇒ throws `Error("Malformed response: success")`.

## Coverage
- `frontend/tests/Unit/Entries/HttpEntriesGateway/AC01_HappyPath.test.ts`  
- `frontend/tests/Unit/Entries/HttpEntriesGateway/AC02_Non2xxResponse.test.ts`  
- `frontend/tests/Unit/Entries/HttpEntriesGateway/AC03_MalformedJson.test.ts`  
- `frontend/tests/Unit/Entries/HttpEntriesGateway/AC04_SuccessFalse.test.ts`
