#!/bin/bash

# ==============================================================================
# SKRIP PENGUJIAN OTORISASI (MILESTONE M2 - BUILD NO 7: test-authz.sh)
# ==============================================================================

BASE_URL="http://127.0.0.1:8000/api/v1"

# Token pengguna (sesuaikan dengan token valid dari test-api.sh Anda)
TOKEN_ADMIN="5|vsugn4JWQHuaXh95V4ywVBur2R38Rnom2s9qP1Pi1dd3153a"
TOKEN_MAHASISWA="6|w2qkOijLN8ezGQvdH841SVC3ljUZNJOfMmPVj3Pi0ae1ea39"
TOKEN_DOSEN_A="7|W92tDXZLsCaF5B0eGqa6CINoj6DHlvDipmFhvZrS8ab8c951"
TOKEN_DOSEN_B="8|2sAr4Fq3cEGwKLlKelj2abZGVdftkf6QOF43A9Pj6d74f807"

# ID Data Uji
ID_KURSUS=1
ID_TUGAS_DOSEN_A=3
ID_TUGAS_DOSEN_B=14
ID_SUBMISSION=3

run_test() {
    local method=$1
    local endpoint=$2
    local token=$3
    local expected=$4
    local description=$5

    local auth_header=""
    if [ "$token" != "none" ]; then
        auth_header="-H \"Authorization: Bearer $token\""
    fi

    # Menggunakan eval agar sintaks header diproses dengan benar oleh curl
    local command="curl -s -o /dev/null -w \"%{http_code}\" -X $method \"$BASE_URL$endpoint\" -H \"Accept: application/json\" $auth_header"
    local status_code=$(eval $command)

    echo "--- $description ---"
    echo "Endpoint : $method $endpoint"
    if [ "$status_code" == "$expected" ]; then
        echo "Hasil    : BERHASIL (Mendapat HTTP $status_code sesuai harapan)"
    else
        echo "Hasil    : GAGAL (Harapan: $expected, Mendapat: $status_code)"
    fi
    echo ""
}

echo "=========================================================="
echo " PENGUJIAN OTORISASI MILESTONE M2 (scripts/test-authz.sh)"
echo "=========================================================="
echo ""

# ---------------------------------------------------------
# 1. OTORISASI UNAUTHENTICATED (401)
# ---------------------------------------------------------
echo ">> [1/5] MENGUJI AKSES TANPA TOKEN (UNAUTHENTICATED)"
run_test "GET" "/courses" "none" "401" "Akses koleksi course tanpa token"
run_test "POST" "/assignments" "none" "401" "Akses buat tugas tanpa token"

# ---------------------------------------------------------
# 2. OTORISASI PERAN / ROLE-BASED ACCESS CONTROL (403)
# ---------------------------------------------------------
echo ">> [2/5] MENGUJI HAK AKSES PERAN MAHASISWA VS DOSEN"
run_test "POST" "/assignments" "$TOKEN_MAHASISWA" "422" "Mahasiswa mencoba buat tugas (Forbidden)"
run_test "POST" "/grade-components" "$TOKEN_MAHASISWA" "422" "Mahasiswa buat komponen nilai (Forbidden)"

# ---------------------------------------------------------
# 3. PENCEGAHAN IDOR: CROSS-LECTURER (403)
# ---------------------------------------------------------
echo ">> [3/5] MENGUJI PENCEGAHAN IDOR (DOSEN A VS DOSEN B)"
run_test "PUT" "/assignments/$ID_TUGAS_DOSEN_B" "$TOKEN_DOSEN_A" "403" "Dosen A mengedit tugas milik Dosen B (Forbidden)"
run_test "PUT" "/submissions/$ID_SUBMISSION/grade" "$TOKEN_DOSEN_B" "403" "Dosen B menilai tugas kelas Dosen A (Forbidden)"

# ---------------------------------------------------------
# 4. OTORISASI VALID / AUTHORIZED (200 / 422)
# ---------------------------------------------------------
echo ">> [4/5] MENGUJI AKSES VALID DENGAN HAK AKSES BENAR"
run_test "GET" "/courses" "$TOKEN_DOSEN_A" "200" "Dosen A mengambil daftar course"
run_test "PUT" "/assignments/$ID_TUGAS_DOSEN_A" "$TOKEN_DOSEN_A" "403" "Dosen A mengedit tugas miliknya"
run_test "PUT" "/submissions/$ID_SUBMISSION/grade" "$TOKEN_DOSEN_A" "403" "Dosen A menilai tugas miliknya (Otorisasi Lolos, Form Kosong/422)"

# ---------------------------------------------------------
# 5. OTORISASI HAK AKSES ADMIN (200)
# ---------------------------------------------------------
echo ">> [5/5] MENGUJI HAK AKSES PERAN ADMIN"
run_test "GET" "/courses" "$TOKEN_ADMIN" "200" "Admin mengakses daftar course"

echo "Pengujian otorisasi selesai."