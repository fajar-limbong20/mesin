<?php
/**
 * Decision Tree (CART - Gini Index)
 * Untuk Random Forest - klasifikasi biner (Ya/Tidak)
 */
class DecisionTree {
    public $maxDepth;
    public $minSamples;
    public $nFeatures;
    public $tree = null;

    public function __construct($maxDepth = 5, $minSamples = 2, $nFeatures = null) {
        $this->maxDepth   = $maxDepth;
        $this->minSamples = $minSamples;
        $this->nFeatures  = $nFeatures;
    }

    public function fit($X, $y, $features) {
        $this->tree = $this->build($X, $y, $features, 0);
    }

    private function build($X, $y, $features, $depth) {
        $n = count($y);
        
        // Kondisi berhenti
        if ($depth >= $this->maxDepth || $n < $this->minSamples || $this->isPure($y)) {
            return [
                'leaf'       => true,
                'prediction' => $this->majority($y),
                'count'      => $n,
            ];
        }

        // Pilih subset fitur secara acak
        $nFeat = $this->nFeatures ?? max(1, (int)round(sqrt(count($features))));
        $nFeat = min($nFeat, count($features));
        $keys  = array_rand($features, $nFeat);
        if (!is_array($keys)) $keys = [$keys];

        // Cari split terbaik
        $best = null;
        foreach ($keys as $k) {
            $feat = $features[$k];
            $vals = array_unique(array_column($X, $feat));
            sort($vals);
            for ($i = 0; $i < count($vals) - 1; $i++) {
                $thr  = ($vals[$i] + $vals[$i+1]) / 2;
                $gini = $this->giniSplit($X, $y, $feat, $thr);
                if ($best === null || $gini < $best['gini']) {
                    $best = ['gini' => $gini, 'feature' => $feat, 'threshold' => $thr];
                }
            }
        }

        if ($best === null) {
            return ['leaf' => true, 'prediction' => $this->majority($y), 'count' => $n];
        }

        // Split data
        $LX = $LY = $RX = $RY = [];
        foreach ($X as $i => $row) {
            if ($row[$best['feature']] <= $best['threshold']) {
                $LX[] = $row; $LY[] = $y[$i];
            } else {
                $RX[] = $row; $RY[] = $y[$i];
            }
        }

        if (empty($LY) || empty($RY)) {
            return ['leaf' => true, 'prediction' => $this->majority($y), 'count' => $n];
        }

        return [
            'leaf'      => false,
            'feature'   => $best['feature'],
            'threshold' => $best['threshold'],
            'gini'      => round($best['gini'], 4),
            'samples'   => $n,
            'left'      => $this->build($LX, $LY, $features, $depth + 1),
            'right'     => $this->build($RX, $RY, $features, $depth + 1),
        ];
    }

    private function giniSplit($X, $y, $feature, $threshold) {
        $L = $R = [];
        foreach ($X as $i => $row) {
            if ($row[$feature] <= $threshold) $L[] = $y[$i];
            else                             $R[] = $y[$i];
        }
        $n = count($y);
        return (count($L) / $n) * $this->gini($L) + (count($R) / $n) * $this->gini($R);
    }

    private function gini($y) {
        $n = count($y);
        if ($n === 0) return 0;
        $counts = array_count_values($y);
        $sum = 0;
        foreach ($counts as $c) { $p = $c / $n; $sum += $p * $p; }
        return 1 - $sum;
    }

    private function isPure($y) {
        return count(array_unique($y)) === 1;
    }

    private function majority($y) {
        if (empty($y)) return null;
        $counts = array_count_values($y);
        arsort($counts);
        return array_key_first($counts);
    }

    /**
 * Prediksi + catat jalur keputusan (untuk visualisasi)
 */
public function predictWithPath($row) {
    $node  = $this->tree;
    $path  = [];
    $depth = 0;

    while (!$node['leaf']) {
        if (!array_key_exists($node['feature'], $row)) {
            return ['prediction' => null, 'path' => $path, 'leaf' => null];
        }

        $val     = $row[$node['feature']];
        $goLeft  = ($val <= $node['threshold']);

        $path[] = [
            'depth'     => $depth,
            'feature'   => $node['feature'],
            'threshold' => $node['threshold'],
            'value'     => $val,
            'operator'  => $goLeft ? '<=' : '>',
            'decision'  => $goLeft ? 'KIRI' : 'KANAN',
            'gini'      => $node['gini'] ?? null,
            'samples'   => $node['samples'] ?? null,
        ];

        $node = $goLeft ? $node['left'] : $node['right'];
        $depth++;
    }

    return [
        'prediction' => $node['prediction'],
        'path'       => $path,
        'leaf'       => [
            'prediction' => $node['prediction'],
            'count'      => $node['count'] ?? null,
        ],
    ];
}

    public function predict($row) {
        $node = $this->tree;
        while (!$node['leaf']) {
            if (!array_key_exists($node['feature'], $row)) return null;
            $node = ($row[$node['feature']] <= $node['threshold']) ? $node['left'] : $node['right'];
        }
        return $node['prediction'];
    }

    public function toArray() { return $this->tree; }
}