<?php

namespace Chess;

use Chess\Evaluation\InverseEvaluationInterface;
use Chess\FEN\StrToBoard;
use Chess\PGN\AN\Color;

class HeuristicsByFenString
{
    use HeuristicsTrait;

    protected Board $board;

    public function __construct(string $fen)
    {
        $this->board = (new StrToBoard($fen))->create();

        $this->calc();
    }

    /**
     * Returns the current evaluation of $this->board.
     *
     * The result obtained suggests which player is probably better.
     *
     * @return array
     */
    public function eval(): array
    {
        $result = [
            Color::W => 0,
            Color::B => 0,
        ];

        $weights = array_values($this->getDimensions());

        $pic = $this->getResult();

        for ($i = 0; $i < count($this->getDimensions()); $i++) {
            $result[Color::W] += $weights[$i] * $pic[Color::W][$i];
            $result[Color::B] += $weights[$i] * $pic[Color::B][$i];
        }

        $result[Color::W] = round($result[Color::W], 2);
        $result[Color::B] = round($result[Color::B], 2);

        return $result;
    }

    /**
     * Returns the resized balanced heuristics given a new range of values.
     *
     * @param float $newMin
     * @param float $newMax
     * @return array
     */
    public function getResizedBalance(float $newMin, float $newMax): array
    {
        $oldMin = -1;
        $oldMax = 1;
        $resize = [];
        foreach ($this->balance as $val) {
            $resized = (($val - $oldMin) / ($oldMax - $oldMin)) *
                ($newMax - $newMin) + $newMin;
            $resize[] = round($resized, 2);
        }

        return $resize;
    }

    /**
     * Heristics calc.
     *
     * @return \Chess\Heuristic\HeuristicsByFenString
     */
    protected function calc(): HeuristicsByFenString
    {
        $item = [];
        foreach ($this->dimensions as $className => $weight) {
            $dimension = new $className($this->board);
            $eval = $dimension->eval();
            if (is_array($eval[Color::W])) {
                if ($dimension instanceof InverseEvaluationInterface) {
                    $item[] = [
                        Color::W => count($eval[Color::B]),
                        Color::B => count($eval[Color::W]),
                    ];
                } else {
                    $item[] = [
                        Color::W => count($eval[Color::W]),
                        Color::B => count($eval[Color::B]),
                    ];
                }
            } else {
                if ($dimension instanceof InverseEvaluationInterface) {
                    $item[] = [
                        Color::W => $eval[Color::B],
                        Color::B => $eval[Color::W],
                    ];
                } else {
                    $item[] = $eval;
                }
            }
        }

        $this->result[Color::W] = array_column($item, Color::W);
        $this->result[Color::B] = array_column($item, Color::B);

        $this->normalize()->balance();

        return $this;
    }

    protected function normalize(): HeuristicsByFenString
    {
        $normalization = [];

        $values = [
            ...$this->result[Color::W],
            ...$this->result[Color::B]
        ];

        $min = min($values);
        $max = max($values);

        for ($i = 0; $i < count($this->dimensions); $i++) {
            if ($max - $min > 0) {
                $normalization[Color::W][$i] =
                    round(($this->result[Color::W][$i] - $min) / ($max - $min), 2);
                $normalization[Color::B][$i] =
                    round(($this->result[Color::B][$i] - $min) / ($max - $min), 2);
            } elseif ($max == $min) {
                $normalization[Color::W][$i] = round(1 / count($values), 2);
                $normalization[Color::B][$i] = round(1 / count($values), 2);
            }
        }

        $this->result = $normalization;

        return $this;
    }

    protected function balance(): HeuristicsByFenString
    {
        foreach ($this->result[Color::W] as $key => $val) {
            $this->balance[$key] =
                round($this->result[Color::W][$key] - $this->result[Color::B][$key], 2);
        }

        return $this;
    }
}
