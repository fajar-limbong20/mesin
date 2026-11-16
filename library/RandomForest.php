<?php
require_once __DIR__ . '/DecisionTree.php';

/**
 * Random Forest Classifier
 * - Bootstrap aggregating (bagging)
 * - Random feature subset per split
 * - Majority voting
 */
class RandomForest {
    public $nTrees;
    public $maxDepth;
    public $minSamples;
    public $trees = [];
    public $features = [];

    public function __construct($nTrees = 10, $maxDepth = 5, $minSamples = 2) {
        $this->nTrees     = $nTrees;
        $this->maxDepth   = $maxDepth;
        $this->minSamples = $minSamples;
    }

    public function fit($X, $y, $features) {
        $this->features = $features;
        $this->trees    = [];
        $n = count($X);

        for ($t = 0; $t < $this->nTrees; $t++) {
            // Bootstrap sampling (sampling with replacement)
            $bx = []; $by = [];
            for ($i = 0; $i < $n; $i++) {
                $idx = mt_rand(0, $n - 1);
                $bx[] = $X[$idx];
                $by[] = $y[$idx];
            }
            $tree = new DecisionTree($this->maxDepth, $this->minSamples);
            $tree->fit($bx, $by, $features);
            $this->trees[] = $tree;
        }
    }

    public function predictProba($row) {
        $votes = [];
        foreach ($this->trees as $tree) {
            $p = $tree->predict($row);
            if ($p === null) continue;
            $votes[$p] = ($votes[$p] ?? 0) + 1;
        }
        $total = array_sum($votes);
        $proba = [];
        foreach ($votes as $k => $v) {
            $proba[$k] = $total > 0 ? round($v / $total * 100, 2) : 0;
        }
        return $proba;
    }

    public function predict($row) {
        $proba = $this->predictProba($row);
        if (empty($proba)) return null;
        arsort($proba);
        return array_key_first($proba);
    }

    public function toJson() {
        $arr = [];
        foreach ($this->trees as $t) $arr[] = $t->toArray();
        return json_encode(['trees' => $arr, 'features' => $this->features]);
    }

    public function loadJson($json) {
        $data = json_decode($json, true);
        if (!$data) return false;
        $this->features = $data['features'] ?? [];
        $this->trees    = [];
        foreach ($data['trees'] as $tArr) {
            $t = new DecisionTree();
            $t->tree = $tArr;
            $this->trees[] = $t;
        }
        return true;
    }

    /**
 * Prediksi + catat trace lengkap dari semua pohon (untuk visualisasi)
 */
public function predictWithTrace($row) {
    $trace = [
        'input'       => $row,
        'trees'       => [],
        'votes'       => [],
        'features'    => $this->features,
        'n_trees'     => count($this->trees),
    ];

    foreach ($this->trees as $i => $tree) {
        $r = $tree->predictWithPath($row);
        $trace['trees'][] = [
            'nomor'      => $i + 1,
            'prediction' => $r['prediction'],
            'path'       => $r['path'],
            'leaf'       => $r['leaf'],
        ];
        if ($r['prediction'] !== null) {
            $trace['votes'][$r['prediction']] = ($trace['votes'][$r['prediction']] ?? 0) + 1;
        }
    }

    $total = array_sum($trace['votes']);
    $trace['total_votes'] = $total;

    $trace['probability'] = [];
    foreach ($trace['votes'] as $k => $v) {
        $trace['probability'][$k] = $total > 0 ? round($v / $total * 100, 2) : 0;
    }

    // Urutkan votes desc
    arsort($trace['votes']);
    $trace['final']      = array_key_first($trace['votes']);
    $trace['final_prob'] = $trace['probability'][$trace['final']] ?? 0;

    // Hitung juga detail pohon yang vote Ya vs Tidak
    $trace['tree_predictions'] = array_map(fn($t) => $t['prediction'], $trace['trees']);

    return $trace;
}
}