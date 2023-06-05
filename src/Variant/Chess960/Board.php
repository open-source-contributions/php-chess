<?php

namespace Chess\Variant\Chess960;

use Chess\Player\PgnPlayer;
use Chess\Variant\Classical\Board as ClassicalBoard;
use Chess\Variant\Classical\FEN\Field\CastlingAbility;
use Chess\Variant\Classical\PGN\Move;
use Chess\Variant\Classical\PGN\AN\Square;
use Chess\Variant\Chess960\StartPieces;
use Chess\Variant\Chess960\Rule\CastlingRule;

/**
 * Board
 *
 * Chess board representation to play Chess960.
 *
 * @author Jordi Bassagañas
 * @license GPL
 */
final class Board extends ClassicalBoard
{
    /**
     * Start position.
     *
     * @var array
     */
     private array $startPos;

    /**
     * Constructor.
     *
     * @param array $startPos
     */
    public function __construct(
        array $startPos = null,
        array $pieces = null,
        string $castlingAbility = '-',
        string $startFen = null
    ) {
        $this->size = Square::SIZE;
        $this->startPos = $startPos ?? (new StartPosition())->getClassical();
        $this->castlingRule =  (new CastlingRule($this->startPos))->getRule();
        $this->move = new Move();
        if (!$pieces) {
            $pieces = (new StartPieces($this->startPos, $this->castlingRule))->create();
            $this->castlingAbility = CastlingAbility::START;
        } else {
            $this->castlingAbility = $castlingAbility;
        }
        foreach ($pieces as $piece) {
            $this->attach($piece);
        }

        $this->refresh();

        $this->startFen = $startFen ?? $this->toFen();
    }

    /**
     * Returns the start position.
     *
     * @return array
     */
    public function getStartPos(): array
    {
        return $this->startPos;
    }

    /**
     * Undoes the last move.
     *
     * @return \Chess\Variant\Chess960\Board
     */
    public function undo(): Board
    {
        $movetext = $this->popHistory()->getMovetext();
        $board = new Board($this->startPos);

        return (new PgnPlayer($movetext, $board))->play()->getBoard();
    }
}
