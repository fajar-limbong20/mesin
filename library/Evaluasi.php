<?php
/**
 * Evaluasi Model Klasifikasi
 * - Confusion Matrix
 * - Akurasi, Precision, Recall, F1-Score
 */
class Evaluasi {
    public $tp = 0, $tn = 0, $fp = 0, $fn = 0;

    /**
     * Hitung confusion matrix
     * @param array $yActual  label aktual
     * @param array $yPred    label prediksi
     * @param string $positif label positif (mis. 'Ya')
     */
    public function hitung($yActual, $yPred, $positif = 'Ya') {
        $this->tp = $this->tn = $this->fp = $this->fn = 0;
        foreach ($yActual as $i => $act) {
            $pred = $yPred[$i] ?? null;
            if     ($act === $positif   && $pred === $positif)   $this->tp++;
            elseif ($act !== $positif   && $pred !== $positif)   $this->tn++;
            elseif ($act !== $positif   && $pred === $positif)   $this->fp++;
            elseif ($act === $positif   && $pred !== $positif)   $this->fn++;
        }
    }

    public function akurasi() {
        $total = $this->tp + $this->tn + $this->fp + $this->fn;
        return $total > 0 ? round(($this->tp + $this->tn) / $total * 100, 2) : 0;
    }

    public function precision() {
        $denom = $this->tp + $this->fp;
        return $denom > 0 ? round($this->tp / $denom * 100, 2) : 0;
    }

    public function recall() {
        $denom = $this->tp + $this->fn;
        return $denom > 0 ? round($this->tp / $denom * 100, 2) : 0;
    }

    public function f1() {
        $p = $this->precision();
        $r = $this->recall();
        return ($p + $r) > 0 ? round(2 * $p * $r / ($p + $r), 2) : 0;
    }

    public function hasil() {
        return [
            'tp' => $this->tp, 'tn' => $this->tn,
            'fp' => $this->fp, 'fn' => $this->fn,
            'akurasi'   => $this->akurasi(),
            'precision' => $this->precision(),
            'recall'    => $this->recall(),
            'f1'        => $this->f1(),
        ];
    }
}