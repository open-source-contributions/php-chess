<?php

namespace Chess\Piece;

use Chess\Exception\UnknownNotationException;
use Chess\PGN\AN\Color;
use Chess\PGN\AN\Square;
use Chess\PGN\AN\Piece;
use Chess\Piece\AbstractPiece;

/**
 * Pawn class.
 *
 * @author Jordi Bassagañas
 * @license GPL
 */
class Pawn extends AbstractPiece
{
    /**
     * @var string
     */
    private string $file;

    /**
     * @var object
     */
    private object $ranks;

    /**
     * @var array
     */
    private array $captureSqs;

    /**
     * @var string
     */
    private string $enPassantSq = '';

    /**
     * Constructor.
     *
     * @param string $color
     * @param string $sq
     */
    public function __construct(string $color, string $sq)
    {
        parent::__construct($color, $sq, Piece::P);

        $this->file = $this->sq[0];

        if ($this->color === Color::W) {
            $this->ranks = (object) [
                'initial' => 2,
                'next' => (int)$this->sq[1] + 1,
                'promotion' => 8
            ];
        } elseif ($this->color === Color::B) {
            $this->ranks = (object) [
                'initial' => 7,
                'next' => (int)$this->sq[1] - 1,
                'promotion' => 1
            ];
        }

        $this->captureSqs = [];

        $this->mobility = [];

        $this->mobility();
    }

    /**
     * Calculates the piece's mobility.
     *
     * @return \Chess\Piece\AbstractPiece
     */
    protected function mobility(): AbstractPiece
    {
        // next rank
        try {
            if (Square::validate($this->file . $this->ranks->next, true)) {
                $this->mobility[] = $this->file . $this->ranks->next;
            }
        } catch (UnknownNotationException $e) {

        }

        // two square advance
        if ($this->sq[1] == 2 && $this->ranks->initial == 2) {
            $this->mobility[] = $this->file . ($this->ranks->initial + 2);
        }
        elseif ($this->sq[1] == 7 && $this->ranks->initial == 7) {
            $this->mobility[] = $this->file . ($this->ranks->initial - 2);
        }

        // capture square
        try {
            $file = chr(ord($this->file) - 1);
            if (Square::validate($file.$this->ranks->next, true)) {
                $this->captureSqs[] = $file . $this->ranks->next;
            }
        } catch (UnknownNotationException $e) {

        }

        // capture square
        try {
            $file = chr(ord($this->file) + 1);
            if (Square::validate($file.$this->ranks->next, true)) {
                $this->captureSqs[] = $file . $this->ranks->next;
            }
        } catch (UnknownNotationException $e) {

        }

        return $this;
    }

    /**
     * Returns the piece's legal moves.
     *
     * @return mixed array|null
     */
    public function sqs(): ?array
    {
        $sqs = [];
        // add up squares
        foreach($this->mobility as $sq) {
            if (in_array($sq, $this->board->getSqEval()->free)) {
                $sqs[] = $sq;
            } else {
                break;
            }
        }
        // add capture squares
        foreach($this->captureSqs as $sq) {
            if (in_array($sq, $this->board->getSqEval()->used->{$this->oppColor()})) {
                $sqs[] = $sq;
            }
        }
        // en passant implementation
        if ($this->board->getLastHistory() &&
            $this->board->getLastHistory()->move->id === Piece::P &&
            $this->board->getLastHistory()->move->color === $this->oppColor()
        ) {
            if ($this->color === Color::W) {
                if ((int)$this->sq[1] === 5) {
                    $captureSquare =
                        $this->board->getLastHistory()->move->sq->next[0] .
                        ($this->board->getLastHistory()->move->sq->next[1]+1);
                    if (in_array($captureSquare, $this->captureSqs)) {
                        $this->enPassantSq = $this->board->getLastHistory()->move->sq->next;
                        $sqs[] = $captureSquare;
                    }
                }
            } elseif ($this->color === Color::B) {
                if ((int)$this->sq[1] === 4) {
                    $captureSquare =
                        $this->board->getLastHistory()->move->sq->next[0] .
                        ($this->board->getLastHistory()->move->sq->next[1]-1);
                    if (in_array($captureSquare, $this->captureSqs)) {
                        $this->enPassantSq = $this->board->getLastHistory()->move->sq->next;
                        $sqs[] = $captureSquare;
                    }
                }
            }
        }

        return $sqs;
    }

    /**
     * Returns the squares defended by the piece.
     *
     * @return mixed array|null
     */
    public function defendedSqs(): ?array
    {
        $sqs = [];
        foreach($this->captureSqs as $sq) {
            if (in_array($sq, $this->board->getSqEval()->used->{$this->getColor()})) {
                $sqs[] = $sq;
            }
        }

        return $sqs;
    }

    /**
     * Gets the pawn's file.
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Gets the pawn's rank info.
     *
     * @return object
     */
    public function getRanks(): object
    {
        return $this->ranks;
    }

    /**
     * Gets the capture squares.
     *
     * @return array
     */
    public function getCaptureSqs(): array
    {
        return $this->captureSqs;
    }

    /**
     * Gets the en passant square.
     *
     * @return string
     */
    public function getEnPassantSq(): ?string
    {
        return $this->enPassantSq;
    }

    /**
     * Checks whether the pawn is promoted.
     *
     * @return boolean
     */
    public function isPromoted(): bool
    {
        return isset($this->move->newId) &&
            (int)$this->getMove()->sq->next[1] === $this->ranks->promotion;
    }
}
