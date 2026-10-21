#!/usr/bin/env bash

set -euo pipefail

plugin_file=${1:?"Pass the main plugin file as the first argument."}
expected_version=${2:-}

if [[ ! -f "$plugin_file" ]]; then
	printf 'Plugin file not found: %s\n' "$plugin_file" >&2
	exit 1
fi

if [[ ! -f readme.txt ]]; then
	printf 'WordPress.org readme not found: readme.txt\n' >&2
	exit 1
fi

plugin_version=$(sed -nE 's/^[[:space:]]*\*?[[:space:]]*Version:[[:space:]]*([^[:space:]]+).*$/\1/p' "$plugin_file" | head -n 1)
stable_tag=$(sed -nE 's/^Stable tag:[[:space:]]*([^[:space:]]+).*$/\1/p' readme.txt | head -n 1)
expected_version=${expected_version#v}

if [[ -z "$plugin_version" || -z "$stable_tag" ]]; then
	printf 'Could not read the plugin version or Stable tag.\n' >&2
	exit 1
fi

if [[ "$plugin_version" != "$stable_tag" ]]; then
	printf 'Version mismatch: plugin=%s, stable-tag=%s\n' "$plugin_version" "$stable_tag" >&2
	exit 1
fi

if [[ -n "$expected_version" && "$plugin_version" != "$expected_version" ]]; then
	printf 'Release tag mismatch: plugin=%s, release=%s\n' "$plugin_version" "$expected_version" >&2
	exit 1
fi

if ! grep -Fq "= ${plugin_version} =" readme.txt; then
	printf 'No changelog section found for version %s.\n' "$plugin_version" >&2
	exit 1
fi

printf 'Validated WordPress.org release metadata for version %s.\n' "$plugin_version"

if [[ -n "${GITHUB_OUTPUT:-}" ]]; then
	printf 'version=%s\n' "$plugin_version" >> "$GITHUB_OUTPUT"
fi
