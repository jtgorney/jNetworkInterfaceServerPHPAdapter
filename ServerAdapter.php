<?php
/**
* The MIT License (MIT)
* Copyright (c) 2015 Jacob Gorney
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*/

/**
 * Server adapter class for jNetworkInterfaceServer.
 */
class ServerAdapter {

   /**
   * Hostname/IP address of server.
   */
   private $hostname;
   /**
   * Port number of the server (TCP).
   */
   private $port;
   /**
   * Flag for connection status.
   */
   private $isConnected = false;
   /**
   * Socket object from the connection params.
   */
   private $clientSocket;

   // Constructor
   public function __construct($hostname, $port) {
      $this->hostname = $hostname;
      $this->port = $port;
   }

   // Connection status for the socket.
   public function isConnected() {
      $this->connect();
      return $this->isConnected;
   }

   // Send a command to the socket server.
   public function sendCommand($command, $data) {
      $this->connect();
      if ($this->isConnected) {
         fwrite($this->clientSocket, $command.PHP_EOL);
      if (!empty($data))
         foreach ($data as $dataItem)
            fwrite($this->clientSocket, $dataItem.PHP_EOL);
         fwrite($this->clientSocket, "END COMMAND".PHP_EOL);
         $return = stream_get_contents($this->clientSocket);
         $this->closeConnection();
         return $return;
      } else
         return "INVALID CONNECTION";
   }

   // Make a connection to the socket server.
   private function connect() {
      if (!$this->isConnected) {
         $this->clientSocket = @stream_socket_client("tcp://".$this->hostname.":".$this->port, $errno, $msg, 5);
      }
      if ($this->clientSocket === false)
         $this->isConnected = false;
      else
         $this->isConnected = true;
   }

   // Close the socket connections and update the flag.
   private function closeConnection() {
      if ($this->isConnected)
         fclose($this->clientSocket);
      $this->isConnected = false;
   }
}
