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

namespace Adams\TeamSpeak3\Node;

use Adams\TeamSpeak3;
use Adams\TeamSpeak3\Adapter\ServerQuery\Exception;
use Adams\TeamSpeak3\Node;
use Adams\TeamSpeak3\Helper\Str;

/**
 * @class Channel
 * @brief Class describing a TeamSpeak 3 channel and all it's parameters.
 */
class Channel extends Node
{
  /**
   * The TeamSpeak3_Node_Channel constructor.
   *
   * @param  Server $server
   * @param  array  $info
   * @param  string $index
   * @throws Exception
   * @return Channel
   */
  public function __construct(Server $server, array $info, $index = "cid")
  {
    $this->parent = $server;
    $this->nodeInfo = $info;

    if(!array_key_exists($index, $this->nodeInfo))
    {
      throw new Exception("invalid channelID", 0x300);
    }

    $this->nodeId = $this->nodeInfo[$index];
  }

  /**
   * Returns an array filled with Channel objects.
   *
   * @param  array $filter
   * @return array|Channel[]
   */
  public function subChannelList(array $filter = array())
  {
    $channels = array();

    foreach($this->getParent()->channelList() as $channel)
    {
      if($channel["pid"] == $this->getId())
      {
        $channels[$channel->getId()] = $channel;
      }
    }

    return $this->filterList($channels, $filter);
  }

  /**
   * Returns the TeamSpeak3_Node_Channel object matching the given ID.
   *
   * @param  integer $cid
   * @throws Exception
   * @return Channel
   */
  public function subChannelGetById($cid)
  {
    if(!array_key_exists((int) $cid, $this->subChannelList()))
    {
      throw new Exception("invalid channelID", 0x300);
    }

    return $this->channelList[(int) $cid];
  }

  /**
   * Returns the TeamSpeak3_Node_Channel object matching the given name.
   *
   * @param  integer $name
   * @throws Exception
   * @return Channel
   */
  public function subChannelGetByName($name)
  {
    foreach($this->subChannelList() as $channel)
    {
      if($channel["channel_name"] == $name) return $channel;
    }

    throw new Exception("invalid channelID", 0x300);
  }

  /**
   * Returns an array filled with Client objects.
   *
   * @param  array $filter
   * @return array | Client[]
   */
  public function clientList(array $filter = array())
  {
    $clients = array();

    foreach($this->getParent()->clientList() as $client)
    {
      if($client["cid"] == $this->getId())
      {
        $clients[$client->getId()] = $client;
      }
    }

    return $this->filterList($clients, $filter);
  }

  /**
   * Returns the Client object matching the given ID.
   *
   * @param  integer $clid
   * @throws Exception
   * @return Client
   */
  public function clientGetById($clid)
  {
    if(!array_key_exists($clid, $this->clientList()))
    {
      throw new Exception("invalid clientID", 0x200);
    }

    return $this->clientList[intval($clid)];
  }

  /**
   * Returns the Client object matching the given name.
   *
   * @param  integer $name
   * @throws Exception
   * @return Client
   */
  public function clientGetByName($name)
  {
    foreach($this->clientList() as $client)
    {
      if($client["client_nickname"] == $name) return $client;
    }

    throw new Exception("invalid clientID", 0x200);
  }

  /**
   * Returns a list of permissions defined for a client in the channel.
   *
   * @param  integer $cldbid
   * @param  boolean $permsid
   * @return array
   */
  public function clientPermList($cldbid, $permsid = FALSE)
  {
    return $this->getParent()->channelClientPermList($this->getId(), $cldbid, $permsid);
  }

  /**
   * Adds a set of specified permissions to a client in a specific channel. Multiple permissions can be added by
   * providing the two parameters of each permission.
   *
   * @param  integer $cldbid
   * @param  integer $permid
   * @param  integer $permvalue
   * @return void
   */
  public function clientPermAssign($cldbid, $permid, $permvalue)
  {
    $this->getParent()->channelClientPermAssign($this->getId(), $cldbid, $permid, $permvalue);
  }

  /**
   * Alias for clientPermAssign().
   *
   * @deprecated
   */
  public function clientPermAssignByName($cldbid, $permname, $permvalue)
  {
    $this->clientPermAssign($cldbid, $permname, $permvalue);
  }

  /**
   * Removes a set of specified permissions from a client in the channel. Multiple permissions can be removed at once.
   *
   * @param  integer $cldbid
   * @param  integer $permid
   * @return void
   */
  public function clientPermRemove($cldbid, $permid)
  {
    $this->getParent()->channelClientPermRemove($this->getId(), $cldbid, $permid);
  }

  /**
   * Alias for clientPermRemove().
   *
   * @deprecated
   */
  public function clientPermRemoveByName($cldbid, $permname)
  {
    $this->clientPermRemove($cldbid, $permname);
  }

  /**
   * Returns a list of permissions defined for the channel.
   *
   * @param  boolean $permsid
   * @return array
   */
  public function permList($permsid = FALSE)
  {
    return $this->getParent()->channelPermList($this->getId(), $permsid);
  }

  /**
   * Adds a set of specified permissions to the channel. Multiple permissions can be added by
   * providing the two parameters of each permission.
   *
   * @param  integer $permid
   * @param  integer $permvalue
   * @return void
   */
  public function permAssign($permid, $permvalue)
  {
    $this->getParent()->channelPermAssign($this->getId(), $permid, $permvalue);
  }

  /**
   * Alias for permAssign().
   *
   * @deprecated
   */
  public function permAssignByName($permname, $permvalue)
  {
    $this->permAssign($permname, $permvalue);
  }

  /**
   * Removes a set of specified permissions from the channel. Multiple permissions can be removed at once.
   *
   * @param  integer $permid
   * @return void
   */
  public function permRemove($permid)
  {
    $this->getParent()->channelPermRemove($this->getId(), $permid);
  }

  /**
   * Alias for permRemove().
   *
   * @deprecated
   */
  public function permRemoveByName($permname)
  {
    $this->permRemove($permname);
  }

  /**
   * Returns a list of files and directories stored in the channels file repository.
   *
   * @param  string  $cpw
   * @param  string  $path
   * @param  boolean $recursive
   * @return array
   */
  public function fileList($cpw = "", $path = "/", $recursive = FALSE)
  {
    return $this->getParent()->channelFileList($this->getId(), $cpw, $path, $recursive);
  }

  /**
   * Returns detailed information about the specified file stored in the channels file repository.
   *
   * @param  string  $cpw
   * @param  string  $name
   * @return array
   */
  public function fileInfo($cpw = "", $name = "/")
  {
    return $this->getParent()->channelFileInfo($this->getId(), $cpw, $name);
  }

  /**
   * Renames a file in the channels file repository. If the two parameters $tcid and $tcpw are specified, the file
   * will be moved into another channels file repository.
   *
   * @param  string  $cpw
   * @param  string  $oldname
   * @param  string  $newname
   * @param  integer $tcid
   * @param  string  $tcpw
   * @return void
   */
  public function fileRename($cpw = "", $oldname = "/", $newname = "/", $tcid = null, $tcpw = null)
  {
    $this->getParent()->channelFileRename($this->getId(), $cpw, $oldname, $newname, $tcid, $tcpw);
  }

  /**
   * Deletes one or more files stored in the channels file repository.
   *
   * @param  string $cpw
   * @param  string $path
   * @return void
   */
  public function fileDelete($cpw = "", $name = "/")
  {
    $this->getParent()->channelFileDelete($this->getId(), $cpw, $name);
  }

  /**
   * Creates new directory in a channels file repository.
   *
   * @param  string  $cpw
   * @param  string  $dirname
   * @return void
   */
  public function dirCreate($cpw = "", $dirname = "/")
  {
    $this->getParent()->channelDirCreate($this->getId(), $cpw, $dirname);
  }

  /**
   * Returns the level of the channel.
   *
   * @return integer
   */
  public function getLevel()
  {
    return $this->getParent()->channelGetLevel($this->getId());
  }

  /**
   * Returns the pathway of the channel which can be used as a clients default channel.
   *
   * @return string
   */
  public function getPathway()
  {
    return $this->getParent()->channelGetPathway($this->getId());
  }

  /**
   * Returns the possible spacer type of the channel.
   *
   * @return integer
   */
  public function spacerGetType()
  {
    return $this->getParent()->channelSpacerGetType($this->getId());
  }

  /**
   * Returns the possible spacer alignment of the channel.
   *
   * @return integer
   */
  public function spacerGetAlign()
  {
    return $this->getParent()->channelSpacerGetAlign($this->getId());
  }

  /**
   * Returns TRUE if the channel is a spacer.
   *
   * @return boolean
   */
  public function isSpacer()
  {
    return $this->getParent()->channelIsSpacer($this);
  }

  /**
   * Downloads and returns the channels icon file content.
   *
   * @return Str
   */
  public function iconDownload()
  {
    if($this->iconIsLocal("channel_icon_id") || $this["channel_icon_id"] == 0) return;

    $download = $this->getParent()->transferInitDownload(rand(0x0000, 0xFFFF), 0, $this->iconGetName("channel_icon_id"));
    $transfer = TeamSpeak3::factory("filetransfer://" . (strstr($download["host"], ":") !== FALSE ? "[" . $download["host"] . "]" : $download["host"]) . ":" . $download["port"]);

    return $transfer->download($download["ftkey"], $download["size"]);
  }

  /**
   * Changes the channel configuration using given properties.
   *
   * @param  array $properties
   * @return void
   */
  public function modify(array $properties)
  {
    $properties["cid"] = $this->getId();

    $this->execute("channeledit", $properties);
    $this->resetNodeInfo();
  }

  /**
   * Sends a text message to all clients in the channel.
   *
   * @param  string $msg
   * @param  string $cpw
   * @return void
   */
  public function message($msg, $cpw = null)
  {
    if($this->getId() != $this->getParent()->whoamiGet("client_channel_id"))
    {
      $this->getParent()->clientMove($this->getParent()->whoamiGet("client_id"), $this->getId(), $cpw);
    }

    $this->execute("sendtextmessage", array("msg" => $msg, "target" => $this->getId(), "targetmode" => TeamSpeak3::TEXTMSG_CHANNEL));
  }

  /**
   * Deletes the channel.
   *
   * @param  boolean $force
   * @return void
   */
  public function delete($force = FALSE)
  {
    $this->getParent()->channelDelete($this->getId(), $force);
  }

  /**
   * Moves the channel to the parent channel specified with $pid.
   *
   * @param  integer $pid
   * @param  integer $order
   * @return void
   */
  public function move($pid, $order = null)
  {
    $this->getParent()->channelMove($this->getId(), $pid, $order);
  }
  
  /**
   * Sends a plugin command to all clients in the channel.
   *
   * @param  string  $plugin
   * @param  string  $data
   * @param  string  $cpw
   * @param  boolean $subscribed
   * @return void
   */
  public function sendPluginCmd($plugin, $data, $cpw = null, $subscribed = FALSE)
  {
    if($this->getId() != $this->getParent()->whoamiGet("client_channel_id"))
    {
      $this->getParent()->clientMove($this->getParent()->whoamiGet("client_id"), $this->getId(), $cpw);
    }
    
    $this->execute("plugincmd", array("name" => $plugin, "data" => $data, "targetmode" => $subscribed ? TeamSpeak3::PLUGINCMD_CHANNEL_SUBSCRIBED : TeamSpeak3::PLUGINCMD_CHANNEL));
  }

  /**
   * @ignore
   */
  protected function fetchNodeList()
  {
    $this->nodeList = array();

    if($this->getParent()->getLoadClientlistFirst())
    {
      foreach($this->clientList() as $client)
      {
        if($client["cid"] == $this->getId())
        {
          $this->nodeList[] = $client;
        }
      }

      foreach($this->subChannelList() as $channel)
      {
        if($channel["pid"] == $this->getId())
        {
          $this->nodeList[] = $channel;
        }
      }
    }
    else
    {
      foreach($this->subChannelList() as $channel)
      {
        if($channel["pid"] == $this->getId())
        {
          $this->nodeList[] = $channel;
        }
      }

      foreach($this->clientList() as $client)
      {
        if($client["cid"] == $this->getId())
        {
          $this->nodeList[] = $client;
        }
      }
    }
  }

  /**
   * @ignore
   */
  protected function fetchNodeInfo()
  {
    $this->nodeInfo = array_merge($this->nodeInfo, $this->execute("channelinfo", array("cid" => $this->getId()))->toList());
  }

  /**
   * Returns a unique identifier for the node which can be used as a HTML property.
   *
   * @return string
   */
  public function getUniqueId()
  {
    return $this->getParent()->getUniqueId() . "_ch" . $this->getId();
  }

  /**
   * Returns the name of a possible icon to display the node object.
   *
   * @return string
   */
  public function getIcon()
  {
    if($this["channel_maxclients"] != -1 && $this["channel_maxclients"] <= $this["total_clients"])
    {
      return "channel_full";
    }
    elseif($this["channel_flag_password"])
    {
      return "channel_pass";
    }
    else
    {
      return "channel_open";
    }
  }

  /**
   * Returns a symbol representing the node.
   *
   * @return string
   */
  public function getSymbol()
  {
    return "#";
  }

  /**
   * Returns a string representation of this node.
   *
   * @return string
   */
  public function __toString()
  {
    return (string) $this["channel_name"];
  }
}

