# Bundled dependencies

The distributable plugin vendors browser assets because WordPress does not provide these versions. Keep unminified and minified files from the same upstream release and retain upstream license files when updating them.

| Dependency | Bundled version | Upstream |
| --- | --- | --- |
| AlertifyJS | 1.14.0 | <https://github.com/MohammadYounes/AlertifyJS> |
| Tooltipster | 4.2.8 | <https://github.com/iamceege/tooltipster> |
| jQuery UI Smoothness theme | 1.14.2 | <https://jqueryui.com/download/> |

The PHP implementation uses WordPress, Contact Form 7, and Flamingo APIs supplied by the host installation. OpenSSL is required. IMAP is optional and is used only when sent-message storage is enabled.
