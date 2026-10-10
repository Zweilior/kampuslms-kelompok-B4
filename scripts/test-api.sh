#!/bin/bash

# Konfigurasi URL dan Token (Ganti dengan token yang valid dari database/seeder Anda)
BASE_URL="http://127.0.0.1:8000/api/v1"
TOKEN_MAHASISWA="2|EbynceEL9ypq4CqztlT6Ib0pb4dDBMyCO7DQEBNIaace7a07"
TOKEN_DOSEN_A="3|6X8gbjenHTcGbOmQk78GHGdgImeuMi912MSXXWjPa8e1ac8b"
TOKEN_DOSEN_B="4|bzCY7E3PAMekVoxjAifWZSn7rY9QwyC0s8iT3ZPm716dc763"

# Variabel ID Data untuk pengujian kepemilikan
ID_KURSUS=1
ID_TUGAS_DOSEN_A=1 # Tugas yang dibuat oleh Dosen A
ID_TUGAS_DOSEN_B=13 # Tugas yang dibuat oleh Dosen B
ID_SUBMISSION=1    # Pengumpulan tugas dari mahasiswa

# Fungsi pembantu untuk melakukan request curl dan mencetak status HTTP
run_test() {
    local method=$1
    local endpoint=$2
    local token=$3
    local expected=$4
    local description=$5

    # Menyiapkan header otorisasi jika token diberikan
    local auth_header=""
    if [ "$token" != "none" ]; then
        auth_header="-H \"Authorization: Bearer $token\""
    fi

    # Eksekusi curl, ambil HTTP status code saja
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
echo " PENGUJIAN OTORISASI API KAMPUSLMS (BUILD NO 7 - PROMPT C)"
echo "=========================================================="
echo ""

# ---------------------------------------------------------
# ENDPOINT: POST /assignments (Membuat Tugas)
# ---------------------------------------------------------
echo ">> MENGUJI ENDPOINT: POST /assignments"
# 1. Tanpa token sama sekali → harus 401
run_test "POST" "/assignments" "none" "401" "1. Tanpa token (Unauthenticated)"

# 2. Token mahasiswa mengakses endpoint dosen → harus 403
run_test "POST" "/assignments" "$TOKEN_MAHASISWA" "403" "2. Token Mahasiswa membuat tugas (Forbidden)"

# 4. Token yang benar (Dosen) dengan hak yang benar → harus 201 (Created) / 200 (OK)
# Catatan: Karena curl di sini kosong tanpa body, bisa jadi error validasi (422). 
# Untuk tes murni otorisasi, anggap saja asumsi mendapat 422 jika lolos otorisasi, atau 201 jika data lengkap.
# Di sini kita ekspektasikan 422 (Unprocessable Entity) karena lolos otorisasi tapi form kosong, atau 201 jika Anda menyesuaikan body.
run_test "POST" "/assignments" "$TOKEN_DOSEN_A" "422" "4. Token Dosen A membuat tugas (Lolos Otorisasi, Form Kosong/422)"


# ---------------------------------------------------------
# ENDPOINT: PUT /assignments/{assignment} (Update Tugas)
# ---------------------------------------------------------
echo ">> MENGUJI ENDPOINT: PUT /assignments/{assignment}"
# 1. Tanpa token sama sekali → harus 401
run_test "PUT" "/assignments/$ID_TUGAS_DOSEN_A" "none" "401" "1. Tanpa token (Unauthenticated)"

# 2. Token mahasiswa mengakses endpoint dosen → harus 403
run_test "PUT" "/assignments/$ID_TUGAS_DOSEN_A" "$TOKEN_MAHASISWA" "403" "2. Token Mahasiswa mengedit tugas (Forbidden)"

# 3. Token dosen A mengakses data milik dosen B → harus 403
run_test "PUT" "/assignments/$ID_TUGAS_DOSEN_B" "$TOKEN_DOSEN_A" "403" "3. Dosen A mengedit tugas milik Dosen B (Forbidden)"

# 4. Token yang benar dengan hak yang benar (Dosen A edit tugas Dosen A) → harus 200
run_test "PUT" "/submissions/$ID_SUBMISSION/grade" "$TOKEN_DOSEN_A" "422" "4. Dosen A menilai tugas miliknya sendiri (Lolos Otorisasi, Form Kosong/422)"

# ---------------------------------------------------------
# ENDPOINT: PUT /submissions/{submission}/grade (Menilai Tugas)
# ---------------------------------------------------------
echo ">> MENGUJI ENDPOINT: PUT /submissions/{submission}/grade"
# 1. Tanpa token sama sekali → harus 401
run_test "PUT" "/submissions/$ID_SUBMISSION/grade" "none" "401" "1. Tanpa token (Unauthenticated)"

# 2. Token mahasiswa mengakses endpoint dosen → harus 403
run_test "PUT" "/submissions/$ID_SUBMISSION/grade" "$TOKEN_MAHASISWA" "403" "2. Token Mahasiswa menilai tugas (Forbidden)"

# 3. Token dosen B menilai submission pada tugas milik dosen A → harus 403
# Asumsi $ID_SUBMISSION terkait dengan $ID_TUGAS_DOSEN_A
run_test "PUT" "/submissions/$ID_SUBMISSION/grade" "$TOKEN_DOSEN_B" "403" "3. Dosen B menilai tugas milik Dosen A (Forbidden)"

# 4. Token yang benar (Dosen A menilai submission tugasnya) → harus 200
run_test "PUT" "/submissions/$ID_SUBMISSION/grade" "$TOKEN_DOSEN_A" "422" "4. Dosen A menilai tugas miliknya sendiri (Authorized)"

echo "Pengujian selesai."