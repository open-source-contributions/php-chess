## PHP Chess

[![Latest Stable Version](https://poser.pugx.org/programarivm/php-chess/v/stable)](https://packagist.org/packages/programarivm/php-chess)
[![Build Status](https://travis-ci.org/programarivm/php-chess.svg?branch=master)](https://travis-ci.org/programarivm/php-chess)
[![License: GPL v3](https://img.shields.io/badge/License-GPL%20v3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)

A chess library for PHP.

### Install

Via composer:

    $ composer require programarivm/php-chess

### Play Chess

This is how to create a new game to play chess:

```php
use Chess\Game;

$game = new Game();

$isLegalMove = $game->play('w', 'e4');
```
The call to the `$game->play` method returns `true` or `false` depending on whether or not a chess move can be run on the board.

If you want to play with the AI pass the `Game::MODE_AI` parameter when creating a `$game` as described next.

```php
$game = new Game(Game::MODE_AI);

$game->play('w', 'e4');
$game->play('b', $game->response());
$game->play('w', 'e5');
$game->play('b', $game->response());
```

Currently a few machine learning models are being built at [programarivm/chess-data](https://github.com/programarivm/chess-data) with the help of [Rubix ML](https://github.com/RubixML/ML). The AIs are stored in the [`model`](https://github.com/programarivm/php-chess/tree/master/model) folder and the default is `a1.model`, however another AI can be used by passing a second parameter to the `Chess\Game` constructor:

```php
$game = new Game(Game::MODE_AI, 'a2.model');

$game->play('w', 'e4');
$game->play('b', $game->response());
$game->play('w', 'e5');
$game->play('b', $game->response());
```

### Documentation

Read the latest docs [here](https://php-chess.readthedocs.io/en/latest/).

### License

The GNU General Public License.

### Contributions

Would you help make this library better? Contributions are welcome.

- Feel free to send a pull request
- Drop an email at info@programarivm.com with the subject "PHP Chess Contributions"
- Leave me a comment on [Twitter](https://twitter.com/programarivm)

Many thanks.
