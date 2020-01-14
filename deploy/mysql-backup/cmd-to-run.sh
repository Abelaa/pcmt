#!/bin/bash
######################################################################
# Copyright (c) 2019, VillageReach
# Licensed under the Non-Profit Open Software License version 3.0.
# SPDX-License-Identifier: NPOSL-3.0
######################################################################

: "${CRED_PATH:=/run/secrets/mysql-creds}"
if [ ! -r "$CRED_PATH" ]; then
    echo "$CRED_PATH not readable"
    exit 1
fi

set -o allexport
source $CRED_PATH
set +o allexport

: "${DB_HOST:?DB_HOST not found}"
: "${DB_PORT:?DB_PORT not found}"
: "${DB_NAME:?DB_NAME not found}"
: "${DB_USER:?DB_USER not found}"
: "${DB_PASS:?DB_PASS not found}"
: "${BACKUP_DIR:=/backup}"
: "${PCMT_HOSTNAME:=pcmt}"

mysqldump -h $DB_HOST --port="$DB_PORT" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" \
    | gzip \
    > "$BACKUP_DIR/$PCMT_HOSTNAME-mysql-dump-$(date -u -Iminutes).sql.zip"