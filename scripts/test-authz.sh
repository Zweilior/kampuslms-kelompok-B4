#!/usr/bin/env bash

# Uji otorisasi terhadap API KampusLMS.
# BASE_URL harus berisi URL lengkap sampai /api/v1, misalnya:
#   https://kampuslms.example.com/api/v1
# Token dan ID data uji wajib diberikan melalui environment.

set -u

require_env() {
    local name="$1"

    if [[ -z "${!name:-}" ]]; then
        printf 'Variabel environment %s wajib diisi.\n' "$name" >&2
        exit 2
    fi
}

for name in BASE_URL TOKEN_ADMIN TOKEN_MAHASISWA TOKEN_DOSEN_A TOKEN_DOSEN_B \
    ID_TUGAS_DOSEN_A ID_TUGAS_DOSEN_B ID_SUBMISSION; do
    require_env "$name"
done

BASE_URL="${BASE_URL%/}"
FAILURES=0

run_test() {
    local method="$1"
    local endpoint="$2"
    local token="$3"
    local expected="$4"
    local description="$5"
    local status_code
    local -a curl_args=(
        --silent
        --show-error
        --output /dev/null
        --write-out '%{http_code}'
        --max-time 20
        --request "$method"
        --header 'Accept: application/json'
    )

    if [[ "$token" != "none" ]]; then
        curl_args+=(--header "Authorization: Bearer $token")
    fi

    if ! status_code="$(curl "${curl_args[@]}" "$BASE_URL$endpoint")"; then
        status_code="000"
    fi

    printf '%s\n' "--- $description ---"
    printf 'Endpoint : %s %s\n' "$method" "$endpoint"
    if [[ "$status_code" == "$expected" ]]; then
        printf 'Hasil    : BERHASIL (HTTP %s)\n\n' "$status_code"
    else
        printf 'Hasil    : GAGAL (harapan HTTP %s, mendapat %s)\n\n' "$expected" "$status_code"
        FAILURES=$((FAILURES + 1))
    fi
}

printf '%s\n' '=========================================================='
printf '%s\n' ' PENGUJIAN OTORISASI API KAMPUSLMS'
printf '%s\n\n' '=========================================================='

printf '%s\n' '>> [1/5] AKSES TANPA TOKEN'
run_test GET '/courses' none 401 'Tanpa token melihat daftar course'
run_test POST '/assignments' none 401 'Tanpa token membuat tugas'

printf '%s\n' '>> [2/5] PEMBATASAN BERDASARKAN PERAN'
run_test POST '/assignments' "$TOKEN_MAHASISWA" 403 'Mahasiswa membuat tugas'
run_test POST '/grade-components' "$TOKEN_MAHASISWA" 403 'Mahasiswa membuat komponen nilai'

printf '%s\n' '>> [3/5] PENCEGAHAN IDOR ANTAR DOSEN'
run_test PUT "/assignments/$ID_TUGAS_DOSEN_B" "$TOKEN_DOSEN_A" 403 'Dosen A mengubah tugas Dosen B'
run_test PUT "/submissions/$ID_SUBMISSION/grade" "$TOKEN_DOSEN_B" 403 'Dosen B menilai submission milik Dosen A'

printf '%s\n' '>> [4/5] AKSES VALID DENGAN HAK YANG SESUAI'
run_test GET '/courses' "$TOKEN_DOSEN_A" 200 'Dosen A melihat daftar course'
run_test PUT "/assignments/$ID_TUGAS_DOSEN_A" "$TOKEN_DOSEN_A" 422 'Dosen A mengubah tugas miliknya dengan form kosong'
run_test PUT "/submissions/$ID_SUBMISSION/grade" "$TOKEN_DOSEN_A" 422 'Dosen A menilai submission miliknya dengan form kosong'

printf '%s\n' '>> [5/5] AKSES ADMIN'
run_test GET '/courses' "$TOKEN_ADMIN" 200 'Admin melihat daftar course'

if (( FAILURES > 0 )); then
    printf 'Pengujian selesai dengan %s kegagalan.\n' "$FAILURES" >&2
    exit 1
fi

printf '%s\n' 'Semua pengujian otorisasi berhasil.'
