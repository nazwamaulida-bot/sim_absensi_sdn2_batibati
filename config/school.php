<?php

return [
    'nama_sekolah' => env('SCHOOL_NAME', 'UPTD SDN 2 Bati-Bati'),
    'alamat_sekolah' => env('SCHOOL_ADDRESS', 'Kecamatan Bati-Bati, Kabupaten Tanah Laut, Kalimantan Selatan'),
    
    // Titik Koordinat Sekolah (UPTD SDN 2 Bati-Bati: 3°36'17.3"S 114°42'01.1"E)
    'latitude' => (float) env('SCHOOL_LATITUDE', -3.604806),
    'longitude' => (float) env('SCHOOL_LONGITUDE', 114.700306),
    'koordinat_dms' => env('SCHOOL_COORDINATES_DMS', '3°36\'17.3"S 114°42\'01.1"E'),
    'radius_meter' => (int) env('SCHOOL_RADIUS_METERS', 100), // Radius absensi dalam meter

    // Jam Kerja Guru
    'jam_masuk' => env('SCHOOL_JAM_MASUK', '07:30:00'),
    'toleransi_terlambat_menit' => (int) env('SCHOOL_TOLERANSI_TERLAMBAT', 15), // Melebihi 15 menit dianggap terlambat (Aturan IF-THEN)
    'jam_pulang' => env('SCHOOL_JAM_PULANG', '14:00:00'),

    // Batas Kehadiran Minimal Siswa
    'ambang_kehadiran_rendah' => (float) env('STUDENT_MIN_ATTENDANCE_PCT', 75.0), // Di bawah 75% memicu peringatan (Aturan IF-THEN)
];
