<?php

/**
 * @file
 * TeamSpeak 3 PHP Framework
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   TeamSpeak3
 * @author    Sven 'ScP' Paulsen
 * @copyright Copyright (c) Planet TeamSpeak. All rights reserved.
 */

namespace Adams\TeamSpeak3\Helper\Signal;

/**
 * @class Handler
 * @brief Helper class providing handler functions for signals.
 */
class Handler
{
  /**
   * Stores the name of the subscribed signal.
   *
   * @var string
   */
  protected $signal = null;

  /**
   * Stores the callback function for the subscribed signal.
   *
   * @var mixed
   */
  protected $callback = null;

  /**
   * The Handler constructor.
   *
   * @param  string $signal
   * @param  mixed  $callback
   * @throws Exception
   * @return Handler
   */
  public function __construct($signal, $callback)
  {
    $this->signal = (string) $signal;

    if(!is_callable($callback))
    {
      throw new Exception("invalid callback specified for signal '" . $signal . "'");
    }

    $this->callback = $callback;
  }

  /**
   * Invoke the signal handler.
   *
   * @param  array $args
   * @return mixed
   */
  public function call(array $args = array())
  {
    return call_user_func_array($this->callback, $args);
  }
}
