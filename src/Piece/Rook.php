<?php

namespace Chess\Piece;

use Chess\Exception\PieceTypeException;
use Chess\Exception\UnknownNotationException;
use Chess\PGN\AN\Square;
use Chess\PGN\AN\Piece;
use Chess\Piece\AbstractPiece;

/**
 * Rook class.
 *
 * @author Jordi Bassagañas
 * @license GPL
 */
class Rook extends Slider
{
    /**
     * @var string
     */
    private string $type;

    /**
     * Constructor.
     *
     * @param string $color
     * @param string $sq
     * @param string $type
     * @throws \Chess\Exception\PieceTypeException
     */
    public function __construct(string $color, string $sq, string $type)
    {
        if (!in_array($type, RookType::all())) {
            throw new PieceTypeException;
        }

        parent::__construct($color, $sq, Piece::R);

        $this->type = $type;

        $this->mobility = (object)[
            'up' => [],
            'bottom' => [],
            'left' => [],
            'right' => []
        ];

        $this->mobility();
    }

    /**
     * Returns the rook's type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Calculates the piece's mobility.
     *
     * @return \Chess\Piece\AbstractPiece
     */
    protected function mobility(): AbstractPiece
    {
        // up
        try {
            $file = $this->sq[0];
            $rank = (int)$this->sq[1] + 1;
            while (Square::validate($file.$rank)) {
                $this->mobility->up[] = $file . $rank;
                $rank = (int)$rank + 1;
            }
        } catch (UnknownNotationException $e) {

        }

        // down
        try {
            $file = $this->sq[0];
            $rank = (int)$this->sq[1] - 1;
            while (Square::validate($file.$rank)) {
                $this->mobility->bottom[] = $file . $rank;
                $rank = (int)$rank - 1;
            }
        } catch (UnknownNotationException $e) {

        }

        // left
        try {
            $file = chr(ord($this->sq[0]) - 1);
            $rank = (int)$this->sq[1];
            while (Square::validate($file.$rank)) {
                $this->mobility->left[] = $file . $rank;
                $file = chr(ord($file) - 1);
            }
        } catch (UnknownNotationException $e) {

        }

        // right
        try {
            $file = chr(ord($this->sq[0]) + 1);
            $rank = (int)$this->sq[1];
            while (Square::validate($file.$rank)) {
                $this->mobility->right[] = $file . $rank;
                $file = chr(ord($file) + 1);
            }
        } catch (UnknownNotationException $e) {

        }

        return $this;
    }
}
