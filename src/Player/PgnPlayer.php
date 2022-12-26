<?php

namespace Chess\Player;

use Chess\Exception\PlayerException;
use Chess\Variant\Classical\Board;
use Chess\Movetext;

/**
 * PgnPlayer.
 *
 * Plays a chess game in PGN format.
 *
 * @author Jordi Bassagañas
 * @license GPL
 */
class PgnPlayer extends AbstractPlayer
{
    /**
     * Constructor.
     *
     * @param string $text
     * @param \Chess\Variant\Classical\Board $board
     */
    public function __construct(string $text, Board $board = null)
    {
        $movetext = (new Movetext($text))->validate();
        $board ? $this->board = $board : $this->board = new Board();
        $this->moves = (new Movetext($movetext))->getMovetext()->moves;
        $this->history = [array_values((new Board())->toAsciiArray())];
    }

    /**
     * Plays a chess game.
     *
     * @return \Chess\Player\PgnPlayer
     */
    public function play(): PgnPlayer
    {
        foreach ($this->moves as $key => $val) {
            if ($key % 2 === 0) {
                if (!$this->board->play('w', $val)) {
                    throw new PlayerException();
                }
            } else {
                if (!$this->board->play('b', $val)) {
                    throw new PlayerException();
                }
            }
            $this->history[] = array_values($this->board->toAsciiArray());
        }

        return $this;
    }
}
