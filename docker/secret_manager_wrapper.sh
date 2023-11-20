#!/usr/bin/env bash

set -eu -o pipefail

SECRET_ID="${SECRET_ID:-}"

if [[ -n "${SECRET_ID}" ]]; then
  secrets=$(aws secretsmanager get-secret-value \
      --secret-id "$SECRET_ID" \
      --no-cli-pager \
      --query 'SecretString' \
  )

  export DB_HOST=$(echo "$secrets" | jq -r 'fromjson | .host')
  export DB_PORT=$(echo "$secrets" | jq -r 'fromjson | .port')
  export DB_USERNAME=$(echo "$secrets" | jq -r 'fromjson | .username')
  export DB_PASSWORD=$(echo "$secrets" | jq -r 'fromjson | .password')
fi

echo "DB_HOST: ${DB_HOST}, DB_PORT: ${DB_PORT}, DB_USERNAME: ${DB_USERNAME}"

/lambda-entrypoint.sh "$@"
