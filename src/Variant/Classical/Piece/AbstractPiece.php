<?php

namespace Chess\Variant\Classical\Piece;

use Chess\Exception\UnknownNotationException;
use Chess\Variant\Classical\PGN\AN\Color;
use Chess\Variant\Classical\PGN\AN\Piece;
use Chess\Variant\Classical\PGN\AN\Square;
use Chess\Variant\Classical\Board;

/**
 * AbstractPiece
 *
 * @author Jordi Bassagañas
 * @license GPL
 */
abstract class AbstractPiece
{
    use PieceObserverBoardTrait;

    /**
     * The piece's color in PGN format.
     *
     * @var string
     */
    protected string $color;

    /**
     * The piece's id in PGN format.
     *
     * @var string
     */
    protected string $id;

    /**
     * The piece's square.
     *
     * @var string
     */
    protected string $sq;

    /**
     * The piece's mobility.
     *
     * @var mixed object|array
     */
    protected array|object $mobility;

    /**
     * The piece's next move.
     *
     * @var object
     */
    protected object $move;

    /**
     * The chessboard.
     *
     * @var \Chess\Variant\Classical\Board
     */
    protected Board $board;

    /**
     * Constructor.
     *
     * @param string $color
     * @param string $sq
     * @param string $id
     */
    public function __construct(string $color, string $sq, string $id)
    {
        $this->color = Color::validate($color);
        $this->sq = Square::validate($sq);
        $this->id = $id;
    }

    /**
     * Calculates the piece's mobility.
     *
     * @return \Chess\Variant\Classical\Piece\AbstractPiece
     */
    abstract protected function mobility(): AbstractPiece;

    /**
     * Returns the piece's legal moves.
     *
     * @return array
     */
    abstract public function sqs(): array;

    /**
     * Returns the squares defended by the piece.
     *
     * @return mixed array|null
     */
    abstract public function defendedSqs(): ?array;

    /**
     * Returns the pieces attacked by the piece.
     *
     * @return mixed array|null
     */
    public function attackedPieces(): ?array
    {
        $pieces = [];
        foreach ($sqs = $this->sqs() as $sq) {
            if ($piece = $this->board->getPieceBySq($sq)) {
                $pieces[] = $piece;
            }
        }

        return $pieces;
    }

    /**
     * Checks out if the opponent's king is attacked by the piece.
     *
     * @return bool
     */
    public function isAttackingKing(): bool
    {
        foreach ($this->attackedPieces() as $piece) {
            if ($piece->getId() === Piece::K) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the piece's color.
     *
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Gets the piece's id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Gets the piece's position on the board.
     *
     * @return string
     */
    public function getSq(): string
    {
        return $this->sq;
    }

    /**
     * Gets the piece's mobility.
     *
     * @return mixed array|object
     */
    public function getMobility(): array|object
    {
        return $this->mobility;
    }

    /**
     * Gets the piece's move.
     *
     * @return object
     */
    public function getMove(): object
    {
        return $this->move;
    }

    /**
     * Sets the piece's next move.
     *
     * @param object $move
     */
    public function setMove(object $move): AbstractPiece
    {
        $this->move = $move;

        return $this;
    }

    /**
     * Gets the piece's opposite color.
     *
     * @return string
     */
    public function oppColor(): string
    {
        return Color::opp($this->color);
    }

    /**
     * Checks whether or not the piece can be moved.
     *
     * @return boolean
     */
    public function isMovable(): bool
    {
        if ($this->move) {
            return in_array($this->move->sq->next, $this->sqs());
        }

        return false;
    }
}
