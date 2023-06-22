<?php

namespace Chess\Play;

use Chess\Variant\Classical\Board;

/**
 * AbstractPlay.
 *
 * @author Jordi Bassagañas
 * @license GPL
 */
abstract class AbstractPlay
{
    /**
     * Chess board.
     *
     * @var \Chess\Variant\Classical\Board
     */
    protected Board $board;

    /**
     * FEN history.
     *
     * @var array
     */
    protected array $fen;

    /**
     * Moves.
     *
     * @var array
     */
    protected array $moves;

    /**
     * Returns the chessboard object.
     *
     * @return \Chess\Variant\Classical\Board
     */
    public function getBoard(): Board
    {
        return $this->board;
    }

    /**
     * Returns the FEN history.
     *
     * @return array
     */
    public function getFen(): array
    {
        return $this->fen;
    }

    /**
     * Returns the moves.
     *
     * @return array
     */
    public function getMoves(): array
    {
        return $this->moves;
    }
}
