# Security policy

Please report vulnerabilities privately through GitHub's security advisory feature instead of opening a public issue.

## Credential storage

Bonaire 1.1.1 derives its authenticated AES-256-GCM encryption key from installation-specific WordPress salts. It does not ship a shared fallback key.

Credentials saved by releases up to 1.0.0 may have used a publicly shared fallback key and therefore cannot be considered confidential. Administrators upgrading from those releases must enter the affected mail password again. Bonaire will continue to migrate legacy ciphertext that was created with the site's own `AUTH_KEY` and `AUTH_SALT`.
