<?php
/**
 * Preprocessing Data
 * - One-Hot Encoding
 * - Normalisasi Min-Max
 * - Split data training/testing
 */
class Preprocessing {
    
    // One-Hot Encoding untuk jenis_gangguan
    public static function encodeGangguan($jenis) {
        $list = ['Paper Jam','Overheat','Getaran Berlebih','Kerusakan Mekanik','Gangguan Listrik','Maintenance Rutin'];
        $result = [];
        foreach ($list as $j) {
            $key = 'gangguan_' . preg_replace('/[^a-z]/i','_', strtolower($j));
            $result[$key] = ($jenis === $j) ? 1 : 0;
        }
        return $result;
    }

    // Bangun matriks X dari dataset
    public static function buildFeatures($rows) {
        $X = [];
        foreach ($rows as $r) {
            $f = [
                'jam_operasi'         => (float)$r['jam_operasi'],
                'suhu_mesin'          => (float)$r['suhu_mesin'],
                'getaran'             => (float)$r['getaran'],
                'konsumsi_listrik'    => (float)$r['konsumsi_listrik'],
                'umur_komponen'       => (float)$r['umur_komponen'],
                'downtime'            => (float)$r['downtime'],
                'frekuensi_kerusakan' => (float)$r['frekuensi_kerusakan'],
            ];
            $f = array_merge($f, self::encodeGangguan($r['jenis_gangguan']));
            $X[] = $f;
        }
        return $X;
    }

    // Daftar nama fitur
    public static function featureNames() {
        $base = ['jam_operasi','suhu_mesin','getaran','konsumsi_listrik','umur_komponen','downtime','frekuensi_kerusakan'];
        $list = ['Paper Jam','Overheat','Getaran Berlebih','Kerusakan Mekanik','Gangguan Listrik','Maintenance Rutin'];
        foreach ($list as $j) {
            $base[] = 'gangguan_' . preg_replace('/[^a-z]/i','_', strtolower($j));
        }
        return $base;
    }

    // Split data (shuffle + ratio)
    public static function splitData($X, $y, $rasio = 0.8) {
        $idx = range(0, count($X) - 1);
        shuffle($idx);
        $nTrain = (int)floor(count($X) * $rasio);
        
        $trainIdx = array_slice($idx, 0, $nTrain);
        $testIdx  = array_slice($idx, $nTrain);
        
        $XTrain = $yTrain = $XTest = $yTest = [];
        foreach ($trainIdx as $i) { $XTrain[] = $X[$i]; $yTrain[] = $y[$i]; }
        foreach ($testIdx  as $i) { $XTest[]  = $X[$i]; $yTest[]  = $y[$i]; }
        
        return [
            'XTrain' => $XTrain, 'yTrain' => $yTrain,
            'XTest'  => $XTest,  'yTest'  => $yTest,
            'trainIdx' => $trainIdx, 'testIdx' => $testIdx
        ];
    }
}