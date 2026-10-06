# Bundled dependencies

| Library | Version | Source |
|---|---|---|
| AlertifyJS | 1.14.0 | https://www.npmjs.com/package/alertifyjs/v/1.14.0 |
| Tooltipster | 4.2.8 | https://www.npmjs.com/package/tooltipster/v/4.2.8 |
| jQuery UI Smoothness CSS and images | 1.14.2 | https://code.jquery.com/ui/1.14.2/themes/smoothness/jquery-ui.min.css |

Tooltipster bundle assets come from the published package. Core, SVG and shadow-theme minified files are rebuilt from that package's source using Terser and clean-css. Upstream trailing whitespace was removed from refreshed source assets. Alertify and Tooltipster license files are included. All updated CSS image references resolve locally.

Checked on 2026-10-06. Runtime assets remain bundled; WordPress supplies jQuery and jQuery UI JavaScript through its registered handles. Versions are pinned in enqueue calls for cache invalidation. Plugin release versions and `Tested up to` are not changed without a real WordPress integration test.

Validation: modified PHP parsed with php-parser (PHP 7.4 grammar); minified and unminified NiceScroll, SmoothScroll, Tooltipster and Alertify assets initialized in jsdom with jQuery 3.7.1. These checks do not replace a real WordPress browser test. The alpha color picker requires a WordPress/Iris integration test.
