# HTTP Status Guide (Daylog)

This document defines the mapping between application-level errors and HTTP response status codes.

## Transport / Protocol (before DTO parsing)
- **400 Bad Request** — malformed JSON, empty body, or structurally invalid request payload.
- **405 Method Not Allowed** — invalid HTTP method for the endpoint.

## Routing / Resource
- **404 Not Found**
  - Unknown endpoint path.
  - Resource not found (e.g., `ENTRY_NOT_FOUND`).

## Validation / Business Rules (after DTO parsing)

### 400 Bad Request (Transport)
- Missing required JSON key (`title`, `body`, or `date` not present at all).  
  Error codes raised by transport rules (e.g., `TitleTransportRule`):  
  - `TITLE_REQUIRED`  
  - `TITLE_MUST_BE_STRING`
- Missing or wrong type for `id` field (checked by `IdTransportRule`):  
  - `ID_REQUIRED`  
  - `ID_NOT_STRING`

### 422 Unprocessable Entity (Domain)
- JSON structure is valid, keys are present, but **business rules are violated**:  
  - `TITLE_REQUIRED` (present, but empty after trimming).  
  - `TITLE_TOO_LONG`.  
  - `BODY_REQUIRED` (present, but empty after trimming).  
  - `BODY_TOO_LONG`.  
  - `DATE_REQUIRED` (present, but empty after trimming).  
  - `DATE_INVALID` (wrong format or non-real calendar date).  
  - `ID_INVALID` (present, string, but not a valid UUID v4).

## Identifiers
- **400 Bad Request (Transport)**  
  - `ID_REQUIRED` (id key missing).  
  - `ID_NOT_STRING` (id present but not a string).  

- **422 Unprocessable Entity (Domain)**  
  - `ID_INVALID` (id present and string, but not a valid UUID v4).  

- **404 Not Found**  
  - `ENTRY_NOT_FOUND` (valid UUID, but resource with given id does not exist).

---

### Notes
- Transport-level errors are always **400/405**.  
- Business-rule violations (UC / ENTRY-BR / BR) are always **422**, except when a required key is **missing entirely**, which is treated as **400**.  
- The same error code string (e.g., `TITLE_REQUIRED`) can appear in both **Transport** and **Domain** contexts:  
  - As `TransportValidationException` → **400 Bad Request**.  
  - As `DomainValidationException` → **422 Unprocessable Entity**.  
- This dual use is intentional to keep codes consistent with UC documents, but HTTP status is determined by **exception category**.